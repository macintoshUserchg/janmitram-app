<?php

use App\Models\Media;
use App\Models\Product;
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
        if (! Schema::hasTable('product_thumbnails')) {
            Schema::create('product_thumbnails', function (Blueprint $table) {
                $table->foreignIdFor(Product::class)->constrained()->cascadeOnDelete();
                $table->foreignIdFor(Media::class)->constrained()->cascadeOnDelete();
            });
        }

        if (! Schema::hasTable('product_attachments')) {
            Schema::create('product_attachments', function (Blueprint $table) {
                $table->foreignIdFor(Product::class)->constrained()->cascadeOnDelete();
                $table->foreignIdFor(Media::class)->constrained()->cascadeOnDelete();
                $table->timestamps();
            });
        }

        Schema::dropIfExists('product_units');
        Schema::dropIfExists('paypal_payments');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Tables are superseded by media, units, and payments tables.
    }
};
