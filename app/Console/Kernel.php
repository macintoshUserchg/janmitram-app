<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     */
    protected function schedule(Schedule $schedule): void
    {
        // Deactivation first, so the 1st's payout sees a cleaned tree.
        $schedule->command('mlm:deactivate-inactive')->dailyAt('00:30');
        $schedule->command('mlm:calculate-payouts')->monthlyOn(1, '01:00');

        // Prune expired mobile and web API tokens weekly (older than 7 days)
        $schedule->command('sanctum:prune-expired --hours=168')->weekly();
    }

    /**
     * Register the commands for the application.
     */
    protected function commands(): void
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
