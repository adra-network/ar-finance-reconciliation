<?php

namespace Phone\Repositories;

use App\User;
use Carbon\CarbonInterface;
use Phone\DTO\TransactionGroup;
use Illuminate\Support\Collection;
use Phone\Models\PhoneTransaction;
use Phone\DTO\TransactionListParameters;

class TransactionListRepository
{
    /** @var TransactionListParameters */
    private $params;

    /** @var Collection */
    private $groups;

    /** @var Collection */
    private $transactions;

    /**
     * @var User
     */
    private $user;

    /**
     * @return Collection
     */
    public function getTransactionListGroups(): Collection
    {
        if ($this->user->isAdmin() && ! $this->params->numberFilter) {
            return new Collection();
        }

        $this->loadTransactions();
        $transactions = $this->transactions;

        $this->groups = collect([]);
        $c = 0;
        foreach ($transactions as $transaction) {
            $groupKey = $transaction[$this->params->groupBy];

            if ($groupKey === null) {
                continue; //TODO GET CLIENTS ANSWER FOR WHAT TO DO WITH THESE NULL TRANSACTIONS
                $groupKey = 'no-number';
            }

            if ($groupKey instanceof CarbonInterface) {
                $groupKey = $groupKey->format('m/d/Y');
            }

            if (! isset($this->groups[$groupKey])) {
                $group = new TransactionGroup();
                $group->groupKey = $groupKey;
                $group->groupedBy = $this->params->groupBy;

                $this->groups[$groupKey] = $group;
            }

            /** @var TransactionGroup */
            $group = $this->groups[$groupKey];
            $group->addTransaction($transaction);
            $c++;
        }

        //strip down the keys from the array
        $this->groups = $this->groups->values();

        $this->params->set('transactionCount', $c);

        //sort the array by date if grouping by date
        if ($this->params->groupBy === $this->params::GROUP_BY_DATE) {
            $groups = $this->groups->toArray();
            usort($groups, function ($a, $b) {
                if ($this->params->orderDirection === TransactionListParameters::ORDER_BY_DESC) {
                    return strtotime($b->groupKey) > strtotime($a->groupKey);
                } else {
                    return strtotime($b->groupKey) < strtotime($a->groupKey);
                }
            });

            return collect($groups);
        }

        return $this->groups;
    }

    /**
     * @param TransactionListParameters $params
     */
    public function setParams(TransactionListParameters $params): void
    {
        $this->params = $params;
    }

    /**
     * @param User $user
     */
    public function setUser(User $user): void
    {
        $this->user = $user;
    }

    private function loadTransactions(): void
    {
        $query = PhoneTransaction::query()
            ->with('allocatedTo')
            ->select(['phone_transactions.*', 'caller_phone_numbers.phone_number as phone_number', 'account_phone_numbers.phone_number as account_phone_number'])
            ->leftJoin('account_phone_numbers', 'account_phone_numbers.id', '=', 'phone_transactions.account_phone_number_id')
            ->leftJoin('caller_phone_numbers', 'caller_phone_numbers.id', '=', 'phone_transactions.caller_phone_number_id');

        if ($this->params->orderBy) {
            $query->orderBy($this->params->orderBy, $this->params->orderDirection);
        }

        if ($this->params->numberFilter) {
            $query->where('account_phone_numbers.phone_number', $this->params->numberFilter);
        }

        if ($this->params->dateFilter) {
            $query->whereBetween('phone_transactions.date', $this->params->dateFilter);
        }

        if (! $this->params->showZeroCharges) {
            $query->where('total_charges', '>', 0);
        }

        if ($this->params->limit) {
            $query->limit($this->params->limit);
        }

        if ($this->params->page) {
            $skipBy = ($this->params->page * $this->params->limit) - $this->params->limit;
            $query->skip($skipBy);
        }

        $this->transactions = $query->get();
    }
}
