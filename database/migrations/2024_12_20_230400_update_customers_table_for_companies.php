<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('customers', function (Blueprint $table) {
            // Make first_name and last_name nullable
            $table->string('first_name')->nullable()->change();
            $table->string('last_name')->nullable()->change();
            
            // Add new company-related fields if they don't exist
            if (!Schema::hasColumn('customers', 'customer_type')) {
                $table->enum('customer_type', ['individual', 'company'])->default('individual')->after('id');
            }
            if (!Schema::hasColumn('customers', 'company_name')) {
                $table->string('company_name')->nullable()->after('last_name');
            }
            if (!Schema::hasColumn('customers', 'contact_person_name')) {
                $table->string('contact_person_name')->nullable()->after('company_name');
            }
            if (!Schema::hasColumn('customers', 'contact_person_phone')) {
                $table->string('contact_person_phone')->nullable()->after('contact_person_name');
            }
        });
    }

    public function down()
    {
        Schema::table('customers', function (Blueprint $table) {
            // Revert first_name and last_name to required
            $table->string('first_name')->nullable(false)->change();
            $table->string('last_name')->nullable(false)->change();
            
            // Remove company-related fields
            $table->dropColumn([
                'customer_type',
                'company_name',
                'contact_person_name',
                'contact_person_phone'
            ]);
        });
    }
};
