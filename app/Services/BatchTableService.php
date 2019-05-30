<?php

namespace App\Services;

use App\Repositories\AccountRepository;
use App\Repositories\AccountTransactionRepository;

/**
 * todo TEST
 * Class BatchTableService
 * @package App\Services
 */
class BatchTableService
{

    /** @var int */
    protected $withPreviousMonths = 0;

    /** @var  float */
    protected $closingBalance;

    /** @var  object */
    protected $table;

    /** @var  int */
    protected $account_id = null;

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
        $this->table->accounts = AccountRepository::getAccountsForBatchTableView($this->withPreviousMonths, $this->account_id);
        $this->table->unallocatedTransactions = AccountTransactionRepository::getUnallocatedTransactionsWithoutGrouping();
        $this->table->transactionGroups = AccountTransactionRepository::getUnallocatedTransactionGroups();

        return $this->table;
    }

    /**
     * @param int $number
     * @return BatchTableService
     */
    public function setWithPreviousMonths(int $number): BatchTableService
    {
        $this->withPreviousMonths = $number;

        return $this;
    }

    /**
     * @param float $balance
     * @return BatchTableService
     */
    public function setClosingBalance(float $balance): BatchTableService
    {
        $this->closingBalance = $balance;

        return $this;
    }

    /**
     * @param bool $value
     * @return BatchTableService
     */
    public function showVariance(bool $value = true): BatchTableService
    {
        $this->table->showVariance = $value;

        return $this;
    }

    /**
     * @param int $id
     * @return BatchTableService
     */
    public function showOneAccount(int $id): BatchTableService
    {
        $this->account_id = $id;

        return $this;
    }

}