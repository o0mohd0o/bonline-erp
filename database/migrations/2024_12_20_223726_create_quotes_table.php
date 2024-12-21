<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('quotes', function (Blueprint $table) {
            $table->id();
            $table->string('quote_number')->unique();
            $table->foreignId('customer_id')->constrained()->onDelete('cascade');
            $table->date('quote_date');
            $table->string('status')->default('draft'); // draft, sent, accepted, rejected
            $table->decimal('subtotal', 10, 2);
            $table->decimal('vat_rate', 5, 2)->default(14.00); // 14% VAT
            $table->decimal('vat_amount', 10, 2);
            $table->decimal('total', 10, 2);
            $table->string('currency')->default('EGP');
            $table->text('notes')->nullable();
            $table->date('valid_until')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('quote_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('quote_id')->constrained()->onDelete('cascade');
            $table->string('service_name');
            $table->text('description')->nullable();
            $table->text('details')->nullable(); // JSON array of service details/bullets
            $table->string('icon')->nullable(); // FontAwesome icon class
            $table->decimal('amount', 10, 2);
            $table->timestamps();
        });

        // Terms and conditions that can be reused across quotes
        Schema::create('quote_terms', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('content');
            $table->boolean('is_default')->default(false);
            $table->integer('display_order')->default(0);
            $table->timestamps();
        });

        // Pivot table for quotes and their terms
        Schema::create('quote_quote_terms', function (Blueprint $table) {
            $table->foreignId('quote_id')->constrained()->onDelete('cascade');
            $table->foreignId('quote_term_id')->constrained()->onDelete('cascade');
            $table->primary(['quote_id', 'quote_term_id']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('quote_quote_terms');
        Schema::dropIfExists('quote_terms');
        Schema::dropIfExists('quote_items');
        Schema::dropIfExists('quotes');
    }
};
