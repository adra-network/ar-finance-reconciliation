<?php

namespace Account\DTO;

class AccountData
{
    /** @var string */
    public $code;

    /** @var float */
    public $beginningBalance;

    /** @var \Illuminate\Support\Collection */
    public $transactions;

    /** @var float */
    public $netChange;

    /** @var float */
    public $endingBalance;

    /** @var string */
    public $name;

    /** @var string */
    public $bebinningBalanceDate;

    /** @var string */
    public $endingBalanceDate;

    /**
     * AccountData constructor.
     */
    public function __construct()
    {
        $this->transactions = collect([]);
    }
}
