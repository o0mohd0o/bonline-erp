<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddReceiptNumberToReceiptsTable extends Migration
{
    public function up()
    {
        Schema::table('receipts', function (Blueprint $table) {
            $table->string('receipt_number', 6)->unique()->after('id');
        });
    }

    public function down()
    {
        Schema::table('receipts', function (Blueprint $table) {
            $table->dropColumn('receipt_number');
        });
    }
}
