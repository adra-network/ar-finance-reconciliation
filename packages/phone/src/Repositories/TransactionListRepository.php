<?php

namespace Phone\Repositories;

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
     * @return Collection
     */
    public function getTransactionListGroups(): Collection
    {
        $this->loadTransactions();
        $transactions = $this->transactions;

        $this->groups = collect([]);
        foreach ($transactions as $transaction) {
            $groupKey = $transaction[$this->params->groupBy];
            if ($groupKey instanceof CarbonInterface) {
                $groupKey = $groupKey->format('Y-m-d');
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
        }

        //strip down the keys from the array
        $this->groups = $this->groups->values();

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

    private function loadTransactions(): void
    {
        $query = PhoneTransaction::query()
            ->select(['phone_transactions.*', 'phone_numbers.phone_number'])
            ->leftJoin('phone_numbers', 'phone_numbers.id', '=', 'phone_transactions.phone_number_id');

        if ($this->params->orderBy) {
            $query->orderBy($this->params->orderBy, $this->params->orderDirection);
        }

        if ($this->params->numberFilter) {
            $query->where('phone_numbers.phone_number', $this->params->numberFilter);
        }

        if ($this->params->dateFilter) {
            $query->whereBetween('phone_transactions.date', $this->params->dateFilter);
        }

        $q = $query;
        $this->params->set('transactionCount', $q->count());

        if ($this->params->limit) {
            $query->limit($this->params->limit);
        }

        if ($this->params->page) {
            $skipBy = ($this->params->page * $this->params->limit) - $this->params->limit;
            $query->skip($skipBy);
        }

        if (! $this->params->showZeroCharges) {
            $q->where('total_charges', '>', 0);
        }

        $this->transactions = $query->get();
    }
}
