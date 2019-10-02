<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddBeginingBalanceInSyncFlagToAccountPeriodSummariesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('account_period_summaries', function (Blueprint $table) {
            $table->boolean('beginning_balance_in_sync')->default(true);
        });
    }
}
