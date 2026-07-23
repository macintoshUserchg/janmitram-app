<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class TruncateData extends Command
{
    protected $signature = 'app:truncate-data';

    protected $description = 'Truncate all business/transactional data while preserving root + admin + shop users, My Shop, Demo Shop, and reference data.';

    /**
     * Tables with reference / configuration data that must be preserved.
     */
    private const PRESERVED_TABLES = [
        'areas',
        'contact_us',
        'countries',
        'currencies',
        'delivery_charges',
        'failed_jobs',
        'footer_items',
        'footers',
        'generate_settings',
        'google_re_captchas',
        'languages',
        'legal_pages',
        'media',
        'menus',
        'migrations',
        'model_has_permissions',
        'model_has_roles',
        'module_settings',
        'pages',
        'payment_gateways',
        'permissions',
        'role_has_permissions',
        'roles',
        's_m_s_configs',
        'social_auths',
        'social_links',
        'subscription_plans',
        'tags',
        'theme_colors',
        'ticket_issue_types',
        'vat_taxes',
        'verify_manages',
    ];

    /**
     * Tables with business / transactional data to truncate.
     */
    private const TRUNCATE_TABLES = [
        'addresses',
        'admin_coupons',
        'ads',
        'banners',
        'blog_tags',
        'blog_views',
        'blogs',
        'brands',
        'cart_access_tokens',
        'carts',
        'categories',
        'category_subcategories',
        'colors',
        'coupon_collects',
        'coupons',
        'customers',
        'device_keys',
        'driver_locations',
        'driver_orders',
        'drivers',
        'favorites',
        'flash_sale_products',
        'flash_sales',
        'galleries',
        'notifications',
        'order_payments',
        'order_products',
        'order_vat_taxes',
        'orders',
        'payments',
        'paypal_payments',
        'personal_access_tokens',
        'pos_cart_products',
        'pos_carts',
        'product_attachments',
        'product_categories',
        'product_colors',
        'product_licenses',
        'product_sizes',
        'product_subcategories',
        'product_thumbnails',
        'product_translations',
        'product_units',
        'product_vat_taxes',
        'products',
        'recent_views',
        'return_order_details',
        'return_orders',
        'reviews',
        'shop_categories',
        'shop_subscriptions',
        'shop_user',
        'shop_user_chats',
        'sizes',
        'sub_categories',
        'support_ticket_attachments',
        'support_ticket_messages',
        'support_tickets',
        'supports',
        'translate_utilities',
        'transactions',
        'units',
        'user_non_permissions',
        'verify_otps',
        'wallets',
        'withdraws',
    ];

    private const KEEP_EMAILS = [
        'root@readyecommerce.com',
        'admin@readyecommerce.com',
        'shop@readyecommerce.com',
    ];

    public function handle(): int
    {
        $rootOrAdminExists = User::whereIn('email', self::KEEP_EMAILS)->exists();

        if (! $rootOrAdminExists) {
            $this->error('Neither the root nor admin user exists in the database.');
            $this->warn('Have you run `php artisan migrate --seed` yet?');

            return Command::FAILURE;
        }

        $totalUsers = User::count();
        $usersToKeep = User::whereIn('email', self::KEEP_EMAILS)->count();
        $usersToDelete = $totalUsers - $usersToKeep;

        $this->alert('DATA TRUNCATE');
        $this->line('This will:');
        $this->line(sprintf('  • Truncate %d business tables (orders, products, customers, etc.)', count(self::TRUNCATE_TABLES)));
        $this->line(sprintf('  • Delete %d of %d users (keeping root + admin + shop)', $usersToDelete, $totalUsers));
        $this->line(sprintf('  • Delete extra shops not owned by preserved users'));
        $this->line(sprintf('  • Preserve %d reference / config tables (roles, currencies, settings, etc.)', count(self::PRESERVED_TABLES)));
        $this->line('');
        $this->line('The shops "My Shop" (root) and "Demo Shop" (shop user) and their owners will be preserved.');
        $this->line('Reference data (roles, permissions, currencies, settings, media, etc.) will NOT be affected.');

        if (! $this->confirm('Are you sure you want to proceed? This cannot be undone.', false)) {
            $this->info('Cancelled.');

            return Command::SUCCESS;
        }

        $this->line('');

        try {
            $this->truncateTables();
            $this->deleteNonDefaultUsers();
            $this->deleteOrphanShops();
            $this->cleanOrphanedRoleAssignments();
        } catch (\Throwable $e) {
            $this->error('An error occurred: '.$e->getMessage());

            return Command::FAILURE;
        }

        $remainingUsers = User::count();
        $this->newLine();
        $this->info('✓ Data truncation complete.');
        $this->line(sprintf('  • %2d business tables truncated', count(self::TRUNCATE_TABLES)));
        $this->line(sprintf('  • %2d reference tables preserved', count(self::PRESERVED_TABLES)));
        $this->line(sprintf('  • %2d users kept (root + admin + shop)', $remainingUsers));
        $this->line(sprintf('  • %2d orphaned role assignments cleaned', $usersToDelete));
        $this->newLine();
        $this->line('Login credentials:');
        $this->warn('  root@readyecommerce.com / secret');
        $this->warn('  admin@readyecommerce.com / secret');
        $this->warn('  shop@readyecommerce.com / secret');

        return Command::SUCCESS;
    }

    private function truncateTables(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS = 0');

        $bar = $this->output->createProgressBar(count(self::TRUNCATE_TABLES));
        $bar->start();

        try {
            foreach (self::TRUNCATE_TABLES as $table) {
                DB::table($table)->truncate();
                $bar->advance();
            }
        } finally {
            DB::statement('SET FOREIGN_KEY_CHECKS = 1');
        }

        $bar->finish();
        $this->newLine(2);
    }

    private function deleteNonDefaultUsers(): void
    {
        // If there are duplicate users with the same email (e.g. after
        // migrate:fresh + seed), keep the one that has roles assigned.
        foreach (self::KEEP_EMAILS as $email) {
            $duplicates = User::withTrashed()->where('email', $email)->orderBy('id')->get();
            if ($duplicates->count() <= 1) {
                continue;
            }

            $best = $duplicates->first(fn ($u) => $u->roles->isNotEmpty());

            $duplicates->each(function (User $user) use ($best) {
                if ($best && $user->id !== $best->id) {
                    $this->line(sprintf('  • Removed duplicate %s (id=%d, had %d roles)', $user->email, $user->id, $user->roles->count()));
                    $user->forceDelete();
                }
            });
        }

        // Force-delete non-default users (bypasses SoftDeletes so related
        // data in Spatie tables is cleaned up by cascading events).
        User::whereNotIn('email', self::KEEP_EMAILS)->each(function (User $user) {
            $user->forceDelete();
        });
    }

    private function deleteOrphanShops(): void
    {
        $keptUserIds = User::whereIn('email', self::KEEP_EMAILS)->pluck('id');

        $deleted = DB::table('shops')
            ->whereNotIn('user_id', $keptUserIds)
            ->delete();

        if ($deleted > 0) {
            $this->line(sprintf('  • Deleted %d extra shops', $deleted));
        }
    }

    private function cleanOrphanedRoleAssignments(): void
    {
        $cleanCount = 0;

        // Only consider non-soft-deleted users as valid so orphaned
        // rows from force-deleted users are caught.
        $validUserIds = function ($query) {
            $query->select('id')->from('users')->whereNull('deleted_at');
        };

        foreach (['model_has_roles', 'model_has_permissions'] as $table) {
            $deleted = DB::table($table)
                ->whereNotIn('model_id', $validUserIds)
                ->delete();

            $cleanCount += $deleted;
        }

        if ($cleanCount > 0) {
            $this->line(sprintf('  • Cleaned %d orphaned model_has_* rows', $cleanCount));
        }
    }
}
