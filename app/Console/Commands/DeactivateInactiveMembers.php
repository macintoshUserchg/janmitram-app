<?php

namespace App\Console\Commands;

use App\Services\PayoutService;
use Illuminate\Console\Command;

class DeactivateInactiveMembers extends Command
{
    protected $signature = 'mlm:deactivate-inactive';

    protected $description = 'Deactivate members with no order activity in the last 90 days and clear their MLM tree position';

    public function handle(): int
    {
        $result = PayoutService::deactivateInactiveMembers();

        $this->info(count($result['errors']) === 0
            ? "{$result['deactivated']} member(s) deactivated for 90 days of inactivity."
            : "{$result['deactivated']} member(s) deactivated, ".count($result['errors']).' error(s).');

        foreach ($result['errors'] as $error) {
            $this->error("Shop {$error['shop_id']}: {$error['message']}");
        }

        return $result['errors'] !== [] ? Command::FAILURE : Command::SUCCESS;
    }
}
