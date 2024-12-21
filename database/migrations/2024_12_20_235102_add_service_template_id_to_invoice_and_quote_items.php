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
        if (!Schema::hasColumn('invoice_items', 'service_template_id')) {
            Schema::table('invoice_items', function (Blueprint $table) {
                $table->foreignId('service_template_id')->nullable()->constrained('service_templates')->nullOnDelete();
            });
        }

        if (!Schema::hasColumn('quote_items', 'service_template_id')) {
            Schema::table('quote_items', function (Blueprint $table) {
                $table->foreignId('service_template_id')->nullable()->constrained('service_templates')->nullOnDelete();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasColumn('invoice_items', 'service_template_id')) {
            Schema::table('invoice_items', function (Blueprint $table) {
                $table->dropForeign(['service_template_id']);
                $table->dropColumn('service_template_id');
            });
        }

        if (Schema::hasColumn('quote_items', 'service_template_id')) {
            Schema::table('quote_items', function (Blueprint $table) {
                $table->dropForeign(['service_template_id']);
                $table->dropColumn('service_template_id');
            });
        }
    }
};
