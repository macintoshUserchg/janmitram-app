<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TruncateDataTest extends TestCase
{
    use RefreshDatabase;

    public function test_aborts_when_no_default_users_exist(): void
    {
        User::query()->delete();

        $exitCode = $this->artisan('app:truncate-data')->run();

        $this->assertSame(1, $exitCode);
    }

    public function test_cancels_when_user_declines(): void
    {
        User::factory()->create(['email' => 'root@readyecommerce.com']);

        $exitCode = $this->artisan('app:truncate-data')
            ->expectsConfirmation('Are you sure you want to proceed? This cannot be undone.', 'no')
            ->run();

        $this->assertSame(0, $exitCode);
        $this->assertDatabaseCount('users', 1);
    }

    public function test_truncates_business_data_and_preserves_reference_tables(): void
    {
        User::factory()->create(['email' => 'root@readyecommerce.com']);
        User::factory()->create(['email' => 'admin@readyecommerce.com']);
        User::factory()->create(['email' => 'shop@readyecommerce.com']);
        User::factory(5)->create();

        $exitCode = $this->artisan('app:truncate-data')
            ->expectsConfirmation('Are you sure you want to proceed? This cannot be undone.', 'yes')
            ->run();

        $this->assertSame(0, $exitCode);
        $this->assertSame(3, User::count());
        $this->assertEqualsCanonicalizing(
            ['root@readyecommerce.com', 'admin@readyecommerce.com', 'shop@readyecommerce.com'],
            User::pluck('email')->all(),
        );
    }
}
