<?php

use Illuminate\Database\Migrations\Migration;

class UpdateMonthlySummariesSync extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $notInSyncSummaries = \Account\Models\MonthlySummary::where('beginning_balance_in_sync', false)->get();
        foreach ($notInSyncSummaries as $summary) {
            $checker = new \Account\Services\SummaryBeginningBalanceChecker($summary);

            if ($checker->diff() == 0) {
                $summary->update(['beginning_balance_in_sync' => true]);
            }
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}
