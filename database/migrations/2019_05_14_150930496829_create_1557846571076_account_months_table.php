<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class Create1557846571076AccountMonthsTable extends Migration
{
    public function up()
    {
        if (!Schema::hasTable('account_months')) {
            Schema::create('account_months', function (Blueprint $table) {
                $table->increments('id');
                $table->unsignedInteger('account_id');
                $table->foreign('account_id', 'account_fk_54360')->references('id')->on('accounts');
                $table->date('month_date');
                $table->decimal('beginning_balance', 15, 2);
                $table->decimal('net_change', 15, 2);
                $table->decimal('ending_balance', 15, 2);
                $table->date('export_date')->nullable();
                $table->timestamps();
                $table->softDeletes();
            });
        }
    }

    public function down()
    {
        Schema::dropIfExists('account_months');
    }
}
