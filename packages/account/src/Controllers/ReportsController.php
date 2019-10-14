<?php


namespace Account\Controllers;


use Account\Services\EmployeeSummaryService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;

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
            $summaries = $repo->getSummaries();
            $months = $repo->getMonths();
        }

        return view('account::reports.employeeSummary', [
            'summaries' => $summaries ?? null,
            'months' => $months ?? new Collection(),
        ]);
    }

}