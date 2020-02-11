<?php

namespace Account\Services;

use Account\Models\Account;
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

    /**
     * BatchTableService constructor.
     */
    public function __construct()
    {
        $this->table = (object)[];
    }

    /**
     * @param boolean $showZeroVariance
     * @param int $pageNumber
     * @param int $entriesPerPage
     *
     * @return object
     */
    public function getTableData($showZeroVariance = false, $pageNumber = NULL, $entriesPerPage = NULL)
    {
        $this->table->accounts = $this->getAccounts($showZeroVariance, $pageNumber, $entriesPerPage);

        if (!is_null($pageNumber)) {
            $this->table->accountsCount = $this->countAccounts($showZeroVariance);
            $this->table->pages = ceil($this->table->accountsCount / $entriesPerPage);
        }

        return $this->table;
    }

    /**
     * @param boolean $showZeroVariance
     * @param int $pageNumber
     * @param int $entriesPerPage
     *
     * @return Collection
     */
    public function getAccounts($showZeroVariance = false, $pageNumber = NULL, $entriesPerPage = NULL): Collection
    {
        $accounts = Account::query()->with('monthlySummaries', 'reconciliations.transactions', 'transactions');
        if ($this->account_id) {
            $accounts->where('id', $this->account_id);
        }

        $accounts = $accounts->get();

        if (!$showZeroVariance) {
            $accounts = $accounts->filter(function(Account $account) {
                return $account->getVariance() !== 0.0;
            });
        }

        $accounts = $accounts->sortBy(function (Account $account) {
            return Str::lower($account->getNameOnly());
        });

        if (!is_null($pageNumber)) {
            $accounts = $accounts->skip(($pageNumber - 1) * $entriesPerPage)->take($entriesPerPage);
        }

        return $accounts;
    }

    /**
     * @param boolean $showZeroVariance
     *
     * @return int
     */
    public function countAccounts($showZeroVariance = false): int
    {
        $accounts = Account::query();
        if ($this->account_id) {
            $accounts->where('id', $this->account_id);
        }

        $accounts = $accounts->get();

        if (!$showZeroVariance) {
            $accounts = $accounts->filter(function(Account $account) {
                return $account->getVariance() !== 0.0;
            });
        }

        return $accounts->count();
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
}
