<?php

namespace Phone\DTO;

use Illuminate\Support\Collection;
use Phone\Models\PhoneTransaction;

class TransactionGroup
{
    /** @var string */
    public $groupKey;

    /** @var string */
    public $groupedBy;

    /** @var Collection */
    private $transactions;

    /**
     * TransactionGroup constructor.
     */
    public function __construct()
    {
        $this->transactions = collect([]);
    }

    /**
     * @param PhoneTransaction $item
     */
    public function addTransaction(PhoneTransaction $item)
    {
        $this->transactions->push($item);
    }

    /**
     * @return Collection
     */
    public function getTransactions(): Collection
    {
        return $this->transactions;
    }
}
