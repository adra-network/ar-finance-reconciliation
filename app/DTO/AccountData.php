<?php

namespace App\DTO;

class AccountData
{
    /** @var  string */
    public $code;

    /** @var  float */
    public $beginningBalance;

    /** @var \Illuminate\Support\Collection  */
    public $transactions;

    /** @var  float */
    public $netChange;

    /** @var  float */
    public $endingBalance;

    /** @var  string */
    public $name;

    /**
     * AccountData constructor.
     */
    function __construct()
    {
        $this->transactions = collect([]);
    }
}