<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Card system: a membership card issued to a customer whose number, entered
     * at online or POS checkout, grants a flat discount on every purchase. Also
     * drops the dead coupon-collect scheme it replaces.
     */
    public function up(): void
    {
        if (! Schema::hasTable('cards')) {
            Schema::create('cards', function (Blueprint $table) {
                $table->id();
                $table->string('card_number')->unique();
                $table->foreignId('customer_id')->nullable()->constrained()->nullOnDelete();
                $table->boolean('is_active')->default(true);
                $table->timestamps();
            });
        }

        if (Schema::hasTable('orders')) {
            Schema::table('orders', function (Blueprint $table) {
                if (! Schema::hasColumn('orders', 'card_id')) {
                    $table->foreignId('card_id')->nullable()->constrained('cards')->nullOnDelete();
                }
                if (! Schema::hasColumn('orders', 'card_discount')) {
                    $table->decimal('card_discount', 10, 2)->nullable();
                }
            });
        }

        if (Schema::hasTable('pos_carts')) {
            Schema::table('pos_carts', function (Blueprint $table) {
                if (! Schema::hasColumn('pos_carts', 'card_id')) {
                    $table->foreignId('card_id')->nullable()->constrained('cards')->nullOnDelete();
                }
            });
        }

        if (Schema::hasTable('generate_settings')) {
            Schema::table('generate_settings', function (Blueprint $table) {
                if (! Schema::hasColumn('generate_settings', 'card_discount_percentage')) {
                    $table->integer('card_discount_percentage')->default(10);
                }
                if (! Schema::hasColumn('generate_settings', 'card_min_order_amount')) {
                    $table->decimal('card_min_order_amount', 10, 2)->default(500);
                }
            });
        }

        Schema::dropIfExists('coupon_collects');
    }

    public function down(): void
    {
        Schema::create('coupon_collects', function (Blueprint $table) {
            $table->id();
            $table->foreignId('coupon_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->timestamps();
        });

        Schema::table('generate_settings', function (Blueprint $table) {
            $table->dropColumn(['card_discount_percentage', 'card_min_order_amount']);
        });

        Schema::table('pos_carts', function (Blueprint $table) {
            $table->dropForeign(['card_id']);
            $table->dropColumn('card_id');
        });

        Schema::table('orders', function (Blueprint $table) {
            $table->dropForeign(['card_id']);
            $table->dropColumn(['card_id', 'card_discount']);
        });

        Schema::dropIfExists('cards');
    }
};
