<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MockDataSeeder extends Seeder
{
    public function run(): void
    {
        // Only seed if tables are empty
        if (DB::table('flash_sales')->count() === 0) {
            DB::table('flash_sales')->insert([
                'shop_id' => 1, 'title' => 'Summer Sale', 'start_date' => now(),
                'end_date' => now()->addDays(7), 'status' => 'active', 'created_at' => now(), 'updated_at' => now(),
            ]);
        }

        if (DB::table('delivery_charges')->count() === 0) {
            DB::table('delivery_charges')->insert([
                'shop_id' => 1, 'name' => 'Standard', 'charge' => 5.00, 'is_active' => 1,
                'created_at' => now(), 'updated_at' => now(),
            ]);
        }

        if (DB::table('sub_categories')->count() === 0) {
            DB::table('sub_categories')->insert([
                ['category_id' => 1, 'name' => 'T-Shirts', 'slug' => 't-shirts', 'is_active' => 1, 'created_at' => now(), 'updated_at' => now()],
                ['category_id' => 1, 'name' => 'Jeans', 'slug' => 'jeans', 'is_active' => 1, 'created_at' => now(), 'updated_at' => now()],
            ]);
        }

        if (DB::table('vat_taxes')->count() === 0) {
            DB::table('vat_taxes')->insert([
                ['shop_id' => 1, 'name' => 'VAT 5%', 'percentage' => 5.00, 'is_active' => 1, 'created_at' => now(), 'updated_at' => now()],
                ['shop_id' => 1, 'name' => 'VAT 12%', 'percentage' => 12.00, 'is_active' => 1, 'created_at' => now(), 'updated_at' => now()],
            ]);
        }

        if (DB::table('ticket_issue_types')->count() === 0) {
            DB::table('ticket_issue_types')->insert([
                ['name' => 'Technical Issue', 'is_active' => 1, 'created_at' => now(), 'updated_at' => now()],
                ['name' => 'Billing Issue', 'is_active' => 1, 'created_at' => now(), 'updated_at' => now()],
            ]);
        }

        if (DB::table('ads')->count() === 0) {
            DB::table('ads')->insert([
                ['shop_id' => 1, 'name' => 'Banner Ad', 'type' => 'banner', 'url' => 'https://example.com', 'is_active' => 1, 'created_at' => now(), 'updated_at' => now()],
            ]);
        }
    }
}
