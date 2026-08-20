<?php

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
        Schema::table('addresses', function (Blueprint $table) {
            if (! Schema::hasColumn('addresses', 'city')) {
                $table->string('city', 100)->nullable()->after('address_type');
            }
            if (! Schema::hasColumn('addresses', 'state')) {
                $table->string('state', 100)->nullable()->after('city');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('addresses', function (Blueprint $table) {
            if (Schema::hasColumn('addresses', 'city')) {
                $table->dropColumn('city');
            }
            if (Schema::hasColumn('addresses', 'state')) {
                $table->dropColumn('state');
            }
        });
    }
};
