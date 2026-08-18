<?php

declare(strict_types=1);

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
        Schema::table('reviews', function (Blueprint $table) {
            $table->json('photos')->nullable()->after('description');
            $table->text('reply')->nullable()->after('photos');
            $table->timestamp('replied_at')->nullable()->after('reply');
            $table->boolean('is_active')->default(0)->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('reviews', function (Blueprint $table) {
            $table->dropColumn(['photos', 'reply', 'replied_at']);
            $table->boolean('is_active')->default(1)->change();
        });
    }
};
