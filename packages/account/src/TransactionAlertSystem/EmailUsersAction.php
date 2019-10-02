<?php

namespace Account\TransactionAlertSystem;

use Illuminate\Support\Collection;
use Account\Services\GroupTransactionsByUser;
use Account\Repositories\TransactionRepository;
use Account\TransactionAlertSystem\Notifications\TransactionsLateMixedDays;

class EmailUsersAction
{
    /** @var array|null */
    private $users;

    /** @var bool */
    private $force = false;

    /**
     * EmailUsersAction constructor.
     * @param array|null $users
     * @param bool $force
     */
    public function __construct(array $users = null, $force = false)
    {
        $this->users = $users;
        $this->force = $force;
    }

    /**
     * @param array|null $users
     */
    public function __invoke()
    {
        $transactions = TransactionRepository::getLateTransactions();
        $groups = (new GroupTransactionsByUser())($transactions);

        foreach ($groups as $group) {
            if ($this->users && ! in_array($group->user->id, $this->users)) {
                continue;
            }
            if ($this->needsNotifying($group->transactions) || $this->force) {
                $this->notify($group);
            }
        }
    }

    /**
     * @param Collection $transactions
     * @return bool
     */
    private function needsNotifying(Collection $transactions): bool
    {
        foreach ($transactions as $transaction) {

            /** @var Interval $interval */
            $interval = $transaction->getInterval();

            if ($interval->frequency === $interval::FREQUENCY_EVERY_MONDAY) {
                if (now()->is($interval::FREQUENCY_EVERY_MONDAY)) {
                    return true; //sends notifications every monday only
                }
                continue;
            }

            if ($interval->frequency === $interval::FREQUENCY_DAILY) {
                return true;
            }
        }

        return false;
    }

    /**
     * @param $group
     */
    private function notify($group): void
    {

        //first check if all the emails are of the same type,
        //if not then send a mixed email
        $mixedEmail = false;
        $emailClass = null;
        foreach ($group->transactions as $transaction) {
            if ($emailClass === null) {
                $emailClass = $transaction->getInterval()->getEmailClass();
                continue;
            }

            if ($emailClass !== $transaction->getInterval()->getEmailClass()) {
                $mixedEmail = true;
                break;
            }
        }

        if ($mixedEmail) {
            $emailClass = TransactionsLateMixedDays::class;
        }

        if ($group->user->email_notifications_enabled || $this->force) {
            $group->user->notify(new $emailClass($group->transactions));
        }
    }
}
