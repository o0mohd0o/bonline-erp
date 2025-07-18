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
        Schema::create('subscriptions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('customer_id')->constrained()->onDelete('cascade');
            $table->foreignId('service_template_id')->constrained()->onDelete('cascade');
            
            // Subscription details
            $table->string('subscription_number')->unique();
            $table->enum('billing_cycle', ['monthly', 'every_6_months', 'yearly']);
            $table->decimal('price', 10, 2);
            $table->string('currency', 3)->default('SAR');
            
            // Dates
            $table->date('start_date');
            $table->date('end_date');
            $table->date('next_billing_date');
            
            // Status
            $table->enum('status', ['active', 'inactive', 'cancelled', 'expired'])->default('active');
            
            // Notification settings
            $table->string('notification_email')->default('mohd.itc4@gmail.com');
            $table->integer('notify_before_days')->default(15);
            $table->boolean('notification_enabled')->default(true);
            
            // Tracking fields
            $table->timestamp('last_notification_sent')->nullable();
            $table->timestamp('expiry_notification_sent')->nullable();
            
            // Auto-renewal
            $table->boolean('auto_renew')->default(false);
            
            // Notes
            $table->text('notes')->nullable();
            
            $table->timestamps();
            
            // Indexes
            $table->index(['status', 'end_date']);
            $table->index('next_billing_date');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('subscriptions');
    }
};
