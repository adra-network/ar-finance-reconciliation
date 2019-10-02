<?php

namespace Account\Controllers;

use Account\Models\Transaction;
use Account\Services\GroupTransactionsByUser;
use Account\TransactionAlertSystem\Intervals;
use Account\Repositories\TransactionRepository;
use Barryvdh\Debugbar\Controllers\BaseController;

class LateTransactionsController extends BaseController
{
    public function index()
    {
        $interval = (new Intervals())->getIntervals(0);
        $lateTransactions = TransactionRepository::getLateTransactions()->filter(function (Transaction $transaction) use ($interval) {
            return $transaction->getInterval()->isInterval($interval);
        });
        $groups = (new GroupTransactionsByUser())($lateTransactions)->filter(function ($group) use ($interval) {
            return ! $group->user->email_notifications_enabled && $group->user->hasNotLoggedInFor($interval->min) && $group->transactions->count() > 0;
        });

        return view('account::lateTransactions.index', ['groups' => $groups]);
    }
}
