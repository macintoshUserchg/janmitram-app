<?php

namespace Database\Seeders;

use App\Enums\Roles;
use App\Models\User;
use App\Models\Wallet;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;

class CompleteResetSeeder extends Seeder
{
    /**
     * Run the database seeds cleanly resetting all demo data while preserving root user & base setup.
     */
    public function run(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS = 0;');

        $preserveTables = [
            'migrations',
        ];

        $tables = DB::select('SHOW TABLES');
        $dbName = DB::getDatabaseName();
        $prop = 'Tables_in_'.$dbName;

        foreach ($tables as $t) {
            $tbl = $t->$prop;

            if (in_array($tbl, $preserveTables)) {
                continue;
            }

            DB::table($tbl)->truncate();
        }

        DB::statement('SET FOREIGN_KEY_CHECKS = 1;');

        // 1. Seed Roles & Permissions
        $this->call(RoleSeeder::class);
        $this->call(PermissionSeeder::class);

        // 2. Create/Update Root User
        $rootUser = User::updateOrCreate(
            ['email' => 'root@janmitram.com'],
            [
                'name' => 'Super Admin',
                'phone' => '01000000001',
                'password' => bcrypt('secret'),
                'is_active' => true,
                'email_verified_at' => now(),
                'phone_verified_at' => now(),
            ]
        );

        $rootUser->syncRoles([Roles::ROOT->value]);

        // 3. Seed Base System Configuration & Structure
        $this->call(CurrencySeeder::class);
        $this->call(GeneraleSettingSeeder::class);
        $this->call(LegalPageSeeder::class);
        $this->call(PaymentGatewaySeeder::class);
        $this->call(SocialLinkSeeder::class);
        $this->call(ThemeColorSeeder::class);
        $this->call(SocialAuthSeeder::class);
        $this->call(VerifyManageSeeder::class);
        $this->call(PageSeeder::class);
        $this->call(MenuSeeder::class);
        $this->call(CountrySeeder::class);
        $this->call(FooterSeeder::class);
        $this->call(RootAdminShopSeeder::class);
        $this->call(WarehouseSeeder::class);
        $this->call(WalletSeeder::class);

        // 4. Force delete any dummy factory users created during seeding evaluation
        DB::table('users')->where('email', '!=', 'root@janmitram.com')->delete();
        DB::table('model_has_roles')->where('model_id', '!=', $rootUser->id)->delete();

        // 5. Reset Wallet Balance for Root User
        Wallet::where('user_id', $rootUser->id)->update(['balance' => 0]);

        // 6. Clear Cache
        Artisan::call('cache:clear');
        Artisan::call('config:clear');
        Artisan::call('view:clear');

        if ($this->command) {
            $this->command->info('Database cleanly reset to base state with Root user intact.');
        }
    }
}
