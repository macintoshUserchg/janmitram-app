<?php

namespace App\Repositories;

use App\Models\Card;
use App\Support\Repositories\Repository;

class CardRepository extends Repository
{
    /**
     * base method
     */
    public static function model()
    {
        return Card::class;
    }

    /**
     * Generate a unique 8-digit card number.
     */
    public static function generateUniqueNumber(): string
    {
        do {
            $number = (string) random_int(10000000, 99999999);
        } while (self::query()->where('card_number', $number)->exists());

        return $number;
    }

    /**
     * Resolve a card for online checkout: exists, active, and bound to the
     * given customer (a card can only be used by the customer it was issued to).
     */
    public static function resolveForCustomer(string $cardNumber, ?int $customerId): ?Card
    {
        return self::query()
            ->where('card_number', $cardNumber)
            ->where('is_active', true)
            ->where('customer_id', $customerId)
            ->first();
    }

    /**
     * Resolve a card for POS: exists and active (a physical card presented at
     * the counter is the credential, regardless of who holds the account).
     */
    public static function resolveActive(string $cardNumber): ?Card
    {
        return self::query()
            ->where('card_number', $cardNumber)
            ->where('is_active', true)
            ->first();
    }

    /**
     * The flat discount terms for cards (global setting).
     *
     * @return array{percentage: int, min_order_amount: float}
     */
    public static function terms(): array
    {
        $setting = generaleSetting('setting');

        return [
            'percentage' => (int) ($setting?->card_discount_percentage ?? 10),
            'min_order_amount' => (float) ($setting?->card_min_order_amount ?? 500),
        ];
    }

    /**
     * Discount amount for a card on a given subtotal: percentage of the
     * subtotal when the order meets the minimum, otherwise 0.
     */
    public static function discountFor(int|float $totalAmount): float
    {
        $terms = self::terms();

        if ($totalAmount < $terms['min_order_amount']) {
            return 0.0;
        }

        return round($totalAmount * $terms['percentage'] / 100, 2);
    }

    /**
     * Create a card for a customer, keeping exactly one active card per
     * customer (any previous cards are deactivated).
     */
    public static function createForCustomer(int $customerId): Card
    {
        $card = self::create([
            'card_number' => self::generateUniqueNumber(),
            'customer_id' => $customerId,
            'is_active' => true,
        ]);

        self::query()->where('customer_id', $customerId)->where('id', '!=', $card->id)->update(['is_active' => false]);

        return $card;
    }
}
