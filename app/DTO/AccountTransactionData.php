<?php

namespace App\DTO;

use Carbon\Carbon;

class AccountTransactionData
{
    /** @var Carbon */
    public $date;

    /** @var string */
    public $code;

    /** @var string */
    public $journal;

    /** @var string */
    public $reference;

    /** @var float */
    public $debit;

    /** @var float */
    public $credit;
}
