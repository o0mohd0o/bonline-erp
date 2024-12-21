<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateReceiptsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('receipts', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('customer_id'); // Assuming receipt is linked to a customer
            $table->decimal('amount', 10, 2);
            $table->enum('currency', ['USD', 'EGP', 'SAR'])->default('USD'); // Add more as needed
            $table->string('description')->nullable();
            $table->timestamps();

            // Foreign key to link with customers table
            $table->foreign('customer_id')->references('id')->on('customers')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('receipts');
    }
}
