<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class Create1557847403848TransactionsTable extends Migration
{
    public function up()
    {
        if (!Schema::hasTable('transactions')) {
            Schema::create('transactions', function (Blueprint $table) {
                $table->increments('id');
                $table->unsignedInteger('user_id');
                $table->foreign('user_id', 'user_fk_54391')->references('id')->on('users');
                $table->date('transaction_date');
                $table->string('code');
                $table->string('journal')->nullable();
                $table->string('reference')->nullable();
                $table->decimal('debit_amount', 15, 2)->nullable();
                $table->decimal('credit_amount', 15, 2)->nullable();
                $table->longText('comment')->nullable();
                $table->string('status')->nullable();
                $table->timestamps();
                $table->softDeletes();
            });
        }
    }

    public function down()
    {
        Schema::dropIfExists('transactions');
    }
}
