<?php

namespace App\Repositories;

use App\Http\Requests\WithdrawRequest;
use App\Models\Withdraw;
use App\Support\Repositories\Repository;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class WithdrawRepository extends Repository
{
    /**
     * base method
     *
     * @method model()
     */
    public static function model()
    {
        return Withdraw::class;
    }

    /**
     * store new withdraw
     */
    public static function storeByRequest(WithdrawRequest $request): Withdraw
    {
        $shop = generaleSetting('shop');

        return self::create([
            'shop_id' => $shop->id,
            'amount' => $request->amount,
            'name' => $request->name ?? auth()->user()->fullName,
            'contact_number' => $request->contact_number ?? auth()->user()->phone,
            'reason' => $request->message,
        ]);
    }

    /**
     * update withdraw
     *
     * Approval is idempotent: if the withdraw is already approved, the wallet
     * debit is skipped (re-submitting status=approved must not double-debit).
     * On a genuine approval the owner's wallet balance is re-verified fresh
     * inside a transaction (locked) and the debit only proceeds if the balance
     * covers the amount; otherwise approval is blocked and the status stays
     * pending. Disbursement is treated as final — un-approving (moving an
     * approved withdraw back to rejected/pending) does not reverse the debit.
     *
     * @return array{ok: bool, message: string}
     */
    public static function updateWithdraw(Withdraw $withdraw, Request $request): array
    {
        $previousStatus = $withdraw->status;

        $withdraw->update([
            'status' => $request->status,
            'reason' => $request->reason ?? $withdraw->reason,
        ]);

        if ($request->status !== 'approved') {
            return ['ok' => true, 'message' => __('Withdraw request updated successfully')];
        }

        // Already approved on a previous call → no-op (prevents double debit).
        if ($previousStatus === 'approved') {
            return ['ok' => true, 'message' => __('Withdraw request already approved')];
        }

        try {
            $result = DB::transaction(function () use ($withdraw) {
                $wallet = $withdraw->shop->user->wallet()->lockForUpdate()->firstOrFail();

                if ((float) $wallet->balance < (float) $withdraw->amount) {
                    // Roll back the status change so the request stays pending.
                    $withdraw->update(['status' => 'pending']);

                    return ['ok' => false, 'message' => __('Insufficient wallet balance — approval blocked.')];
                }

                TransactionRepository::storeByRequest(
                    $wallet,
                    $withdraw->amount,
                    'debit',
                    false,
                    false,
                    'payout_withdraw',
                    "Withdrawal disbursement for shop {$withdraw->shop->name} (#{$withdraw->id})"
                );

                return ['ok' => true, 'message' => __('Withdraw request updated successfully')];
            });
        } catch (QueryException $e) {
            return ['ok' => false, 'message' => __('Could not process withdrawal: ').$e->getMessage()];
        }

        return $result;
    }
}
