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

if (! function_exists('getAccountsWithUnsyncedSummaries')) {
    function getAccountsWithUnsyncedSummaries()
    {
        /** @var User $user */
        $user = auth()->user();

        return (new AccountRepository())->getAccountsWithUnsyncedSummaries($user);
    }
}
