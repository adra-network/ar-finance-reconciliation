<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateReconciliationTransactionTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('reconciliation_transaction', function (Blueprint $table) {
            $table->unsignedInteger('reconciliation_id');
            $table->foreign('reconciliation_id')->references('id')->on('reconciliations');
            $table->unsignedInteger('transaction_id');
            $table->foreign('transaction_id')->references('id')->on('transaction_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('reconciliation_transaction');
    }
}
