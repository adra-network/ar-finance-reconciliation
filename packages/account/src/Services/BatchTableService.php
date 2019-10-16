<?php

namespace Account\Services;

use Account\Models\Account;
use Carbon\CarbonInterface;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

/**
 * This class is basicaly covered in test_getBatchTableReconciliations_method
 * Class BatchTableService.
 */
class BatchTableService
{
    /** @var float */
    protected $closingBalance;

    /** @var object */
    protected $table;

    /** @var int */
    protected $account_id = null;

    /** @var array  */
    protected $monthFilter = [null, null];

    /**
     * BatchTableService constructor.
     */
    public function __construct()
    {
        $this->table = (object)[];
    }

    /**
     * @return object
     */
    public function getTableData()
    {
        $this->table->accounts = $this->getAccounts();

        return $this->table;
    }

    /**
     * @return Collection
     */
    public function getAccounts(): Collection
    {
        $accounts = Account::query()->with('reconciliations.transactions');
        if ($this->account_id) {
            $accounts->where('id', $this->account_id);
        }
        $accounts = $accounts->get();
        $accounts = $accounts->each(function (Account $account) {
            $account->batchTableMonthFilter = $this->monthFilter;
        })->sortBy(function (Account $account) {
            return Str::lower($account->getNameOnly());
        });

        return $accounts;
    }

    /**
     * @param float $balance
     *
     * @return BatchTableService
     */
    public function setClosingBalance(float $balance): self
    {
        $this->closingBalance = $balance;

        return $this;
    }

    /**
     * @param bool $value
     *
     * @return BatchTableService
     */
    public function showVariance(bool $value = true): self
    {
        $this->table->showVariance = $value;

        return $this;
    }

    /**
     * @param int $id
     *
     * @return BatchTableService
     */
    public function showOneAccount(int $id): self
    {
        $this->account_id = $id;

        return $this;
    }

    /**
     * @param CarbonInterface|null $dateFrom
     * @param CarbonInterface|null $dateTo
     */
    public function limitByDateRange(CarbonInterface $dateFrom = null, CarbonInterface $dateTo = null): void
    {
        $this->monthFilter = [$dateFrom, $dateTo];
    }
}
