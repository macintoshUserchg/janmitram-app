<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        DB::table('generate_settings')
            ->where('name', 'Laravel')
            ->orWhereNull('name')
            ->update(['name' => 'Janmitram']);

        DB::table('generate_settings')
            ->where('title', 'Laravel')
            ->orWhereNull('title')
            ->update(['title' => 'Janmitram']);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // No action needed
    }
};
