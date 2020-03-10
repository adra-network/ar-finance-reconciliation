<?php

use App\User;
use Account\Repositories\AccountRepository;
use Account\Repositories\TransactionRepository;

if (! function_exists('getLateTransactions')) {
    function getLateTransactions()
    {
        return TransactionRepository::getLateTransactions(auth()->user()->id);
    }
}

if (! function_exists('getUnsyncedSummariesWithAccounts')) {
    function getUnsyncedSummariesWithAccounts()
    {
        return (new AccountRepository())->getUnsyncedSummariesWithAccounts();
    }
}
