<?php


namespace Account\Services;


use Account\Models\AccountImport;
use Account\Models\MonthlySummary;
use Carbon\CarbonInterface;
use Illuminate\Support\Collection;

class EmployeeSummaryService
{

    private $dateFrom;
    private $dateTo;

    /** @var Collection */
    private $months;

    public function __construct(CarbonInterface $dateFrom, CarbonInterface $dateTo)
    {
        $this->dateFrom = $dateFrom;
        $this->dateTo = $dateTo;
    }

    public function getSummaries()
    {
        $summaries = new Collection();

        $imports = AccountImport::with('summaries.account')->whereDate('date_from', '>=', $this->dateFrom)->whereDate('date_from', '<=', $this->dateTo)->get();

        /** @var AccountImport $import */
        foreach ($imports as $import) {

            if (!isset($this->months[$import->date_from->format('Y-m')])) {
                $this->months[$import->date_from->format('Y-m')] = $import->date_from;
            }

            /** @var MonthlySummary $summary */
            foreach ($import->summaries as $summary) {

                if (!isset($summaries[$summary->account->id])) {
                    $es = (object)[];
                    $es->account = $summary->account;
                    $es->months = new Collection();
                    $summaries[$summary->account->id] = $es;
                }

                /** @var Collection $esm */
                $esm = $summaries[$summary->account->id]->months;
                $esm->push((object)[
                    'date' => $import->date_from,
                ]);

                $esm->endingBalance = $summary->ending_balance;
//                $es->endingBalance = $summary->ending_balance;
//                $es->variance = $summary->ending_balance - ($this->getSummaryByMonth($import->date_from->subMonth()) ?? 0);

            }

        }

        return $summaries;
    }

    public function getMonths()
    {
        return $this->months;
    }

}