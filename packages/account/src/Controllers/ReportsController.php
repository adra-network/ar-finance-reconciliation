<?php


namespace Account\Controllers;


use Account\Models\MonthlySummary;
use Account\Services\EmployeeSummaryService;
use Carbon\Carbon;
use Illuminate\Http\Request;

class ReportsController
{

    public function employeeSummary(Request $request)
    {
        $dateFilter = $request->input('date_filter', null);
        if ($dateFilter) {
            [$d1, $d2] = explode(' - ', $dateFilter);
            $d1 = Carbon::parse($d1)->startOfMonth();
            $d2 = Carbon::parse($d2)->endOfMonth();
        }

        $imports = null;
        if (isset($d1) && isset($d2)) {
            $repo = new EmployeeSummaryService($d1, $d2);
            $accounts = $repo->getAccounts();
            $months = $repo->getMonths();
        }

        return view('account::reports.employeeSummary', [
            'accounts' => $accounts ?? null,
            'months' => $months ?? [],
        ]);
    }

    public function summariesOutOfSync(Request $request)
    {

        $summaries = MonthlySummary::with('account.user')->where('beginning_balance_in_sync', false)->get();

        foreach ($summaries as $summary) {
            $summary->syncChecker = $summary->getSyncChecker();
        }

        return view('account::reports.summariesOutOfSync', [
            'summaries' => $summaries,
        ]);

    }

}