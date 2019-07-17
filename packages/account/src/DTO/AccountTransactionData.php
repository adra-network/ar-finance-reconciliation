<?php

namespace Account\DTO;

use Carbon\CarbonInterface;

class AccountTransactionData
{
    /** @var CarbonInterface */
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
