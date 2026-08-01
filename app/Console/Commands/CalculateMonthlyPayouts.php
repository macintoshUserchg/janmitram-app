<?php

namespace App\Console\Commands;

use App\Services\PayoutService;
use Illuminate\Console\Command;

class CalculateMonthlyPayouts extends Command
{
    protected $signature = 'mlm:calculate-payouts {--month=} {--year=}';

    protected $description = 'Calculate and credit MLM monthly payouts (defaults to the previous month)';

    public function handle(): int
    {
        $now = now();
        $month = (int) ($this->option('month') ?? $now->subMonthNoOverflow()->month);
        $year = (int) ($this->option('year') ?? $now->subMonthNoOverflow()->year);

        if ($month < 1 || $month > 12) {
            $this->error('Month must be between 1 and 12.');

            return Command::FAILURE;
        }
        if ($year < 2000 || $year > 2100) {
            $this->error('Year is out of range.');

            return Command::FAILURE;
        }

        $result = PayoutService::payoutMonth($year, $month);

        $this->info(sprintf(
            'Payouts for %04d-%02d: %d processed, %d credited, %d skipped (snapshot exists), %d errors',
            $year,
            $month,
            $result['processed'],
            $result['credited'],
            $result['skipped'],
            count($result['errors'])
        ));

        foreach ($result['errors'] as $error) {
            $this->error("Shop {$error['shop_id']}: {$error['message']}");
        }

        return $result['errors'] !== [] ? Command::FAILURE : Command::SUCCESS;
    }
}
