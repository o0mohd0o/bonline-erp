<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Modify the currency enum to include AUD
        DB::statement("ALTER TABLE receipts MODIFY COLUMN currency ENUM('USD', 'EGP', 'SAR', 'AUD') DEFAULT 'USD'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Revert back to original enum values
        DB::statement("ALTER TABLE receipts MODIFY COLUMN currency ENUM('USD', 'EGP', 'SAR') DEFAULT 'USD'");
    }
};
