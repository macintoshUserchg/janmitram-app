<?php

declare(strict_types=1);

require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';

$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$response = $kernel->handle(
    $request = Illuminate\Http\Request::capture()
);

use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

try {
    DB::table('users')->where('id', 1)->update(['password' => Hash::make('secret')]);
    Artisan::call('view:clear');
    Artisan::call('cache:clear');
    Artisan::call('config:cache');
    echo 'SUCCESS: Password for root@janmitram.com updated to secret & caches cleared.';
} catch (\Throwable $e) {
    echo 'ERROR: '.$e->getMessage();
}
