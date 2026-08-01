<?php

use App\Models\Shop;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('shop_monthly_payouts', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(Shop::class)->constrained()->cascadeOnDelete();
            $table->unsignedSmallInteger('year');
            $table->unsignedTinyInteger('month');
            $table->decimal('personal_sales', 15, 2);
            $table->decimal('group_sales', 15, 2);
            $table->unsignedInteger('group_size');
            $table->unsignedTinyInteger('level')->nullable();
            $table->decimal('phase1_amount', 15, 2);
            $table->decimal('phase2_amount', 15, 2);
            $table->decimal('total_payout', 15, 2);
            $table->timestamps();

            // Idempotency guard: a re-run can never double-pay.
            $table->unique(['shop_id', 'year', 'month']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('shop_monthly_payouts');
    }
};
