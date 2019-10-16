<?php


namespace Account\Services;


use Account\Models\AccountImport;
use Carbon\Carbon;
use Carbon\CarbonInterface;
use Illuminate\Support\Collection;

class EmployeeSummaryService
{

    private $dateFrom;
    private $dateTo;

    /** @var Collection */
    private $imports;

    /** @var Collection */
    private $accounts;

    /** @var Collection */
    private $months;


    public function __construct(CarbonInterface $dateFrom, CarbonInterface $dateTo)
    {
        $this->dateFrom = $dateFrom;
        $this->dateTo = $dateTo;

        $this->imports = AccountImport::with('summaries.account')->whereDate('date_from', '>=', $this->dateFrom)->whereDate('date_from', '<=', $this->dateTo)->get();
        $this->loadAccounts();
        $this->loadMonths();

    }

    public function getAccounts(): Collection
    {
        return $this->accounts;
    }

    public function getMonths(): Collection
    {
        return $this->months;
    }

    private function loadMonths(): void
    {
        $months = [];
        foreach ($this->accounts as $account) {
            $monthsRepeating = [];
            foreach ($account->summaries as $summary) {
                $m = $summary->import->date_from->startOfMonth()->format('Y-m-d');
                if (!isset($monthsRepeating[$m])) {
                    $monthsRepeating[$m] = 0;
                }
                $monthsRepeating[$m]++;
            }


            foreach ($monthsRepeating as $month => $times) {
                if (!isset($months[$month])) {
                    $months[$month] = $times;
                } else {
                    if ($months[$month] < $times) {
                        $months[$month] = $times;
                    }
                }
            }

        }


        $return = collect([]);
        foreach ($months as $month => $times) {
            for ($i = 0; $i < $times; $i++) {
                $return->push(Carbon::parse($month));
            }
        }

        $this->months = $return;
    }

    private function loadAccounts(): void
    {
        $accounts = new Collection();
        $previousSummary = [];

        foreach ($this->imports as $import) {

            foreach ($import->summaries as $summary) {
                if (isset($summary->account) && isset($summary->account->id)) {
                    if (!isset($accounts[$summary->account->id])) {
                        $accounts[$summary->account->id] = (object)[
                            'account' => $summary->account,
                            'summaries' => new Collection(),
                        ];
                    }

                    $accounts[$summary->account->id]->summaries[] = (object)[
                        'summary' => $summary,
                        'import' => $import,
                        'variance' => (isset($previousSummary[$summary->account->id]) ? $previousSummary[$summary->account->id]->ending_balance : $summary->ending_balance * 2) - $summary->ending_balance,
                    ];

                    $previousSummary[$summary->account->id] = $summary;
                }
            }
        }

        $this->accounts = $accounts;
    }


}