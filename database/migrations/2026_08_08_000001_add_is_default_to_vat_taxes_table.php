<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * A single tax rate is the platform-wide default (applied to products with
     * no per-product override). All other active rates are per-product
     * overrides only.
     */
    public function up(): void
    {
        Schema::table('vat_taxes', function (Blueprint $table) {
            $table->boolean('is_default')->default(false)->after('is_active');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('vat_taxes', function (Blueprint $table) {
            $table->dropColumn('is_default');
        });
    }
};
