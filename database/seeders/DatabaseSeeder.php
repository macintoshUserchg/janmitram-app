<?php

namespace Database\Seeders;

use App\Models\Shop;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Artisan;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call(RoleSeeder::class);
        $this->call(PermissionSeeder::class);
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

        if (app()->environment('local')) {
            $this->call(UserSeeder::class);
            $this->call(CustomerSeeder::class);
            $this->call(RiderSeeder::class);
            $this->call(ShopSeeder::class);

            // Demo shops do not go through the registration flow, so they have no
            // KYC submission. Create an explicit (all-null) shop_kyc row for each
            // so the admin KYC review card renders instead of silently missing.
            // The root platform shop is excluded — it is the platform owner.
            Shop::whereDoesntHave('kyc')
                ->whereHas('user', fn ($query) => $query->where('email', '!=', 'root@janmitram.com'))
                ->get()
                ->each(fn (Shop $shop) => $shop->kyc()->create());
            $this->call(CategorySeeder::class);
            $this->call(BrandSeeder::class);
            $this->call(SizeSeeder::class);
            $this->call(ColorSeeder::class);
            $this->call(UnitSeeder::class);
            $this->call(AreaSeeder::class);
            $this->call(ProductSeeder::class);
            $this->call(BannerSeeder::class);
            $this->call(CouponSeeder::class);
            $this->call(AddressSeeder::class);
            $this->call(OrderSeeder::class);
            $this->call(ReviewSeeder::class);
            $this->call(FavoriteSeeder::class);
            $this->call(BlogSeeder::class);
        }

        $this->call(WalletSeeder::class);
        $this->call(WarehouseSeeder::class);
        $this->command->info('Database seeded successfully');

        // clear cache
        Artisan::call('cache:clear');

        $this->userInfo();
    }

    private function userInfo()
    {
        // info for root user in command line
        $this->command->line('');
        $this->command->info('Root user created:');
        $this->command->warn('- Email: root@janmitram.com');
        $this->command->warn('- Password: secret');
        $this->command->info('');

        if (app()->environment('local')) {
            // info for shop user in command line
            $this->command->info('Demo Shop created:');
            $this->command->warn('- Email: shop@janmitram.com');
            $this->command->warn('- Password: secret');

            // info for rider user in command line
            $this->command->info('Rider created:');
            $this->command->warn('- Email: rider@janmitram.com');
            $this->command->warn('- Password: secret');
        }
    }
}
