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
        Schema::table('service_templates', function (Blueprint $table) {
            $table->enum('subscription_type', ['one_time', 'monthly', 'every_6_months', 'yearly'])
                  ->default('one_time')
                  ->after('currency');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('service_templates', function (Blueprint $table) {
            $table->dropColumn('subscription_type');
        });
    }
};
