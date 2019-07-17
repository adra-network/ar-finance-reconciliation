<?php

namespace Account\Models;

use App\Traits\Auditable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Collection;

class Account extends Model
{
    use SoftDeletes, Auditable;

    public $table = 'accounts';

    protected $dates = [
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    protected $fillable = [
        'code',
        'name',
        'email',
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    public static $searchable = [
        'name',
    ];

    /** @var int */
    public $batchTableWithPreviousMonths = 0;

    /**
     * @return HasMany
     */
    public function monthlySummaries(): HasMany
    {
        return $this->hasMany(MonthlySummary::class);
    }

    /**
     * @return HasMany
     */
    public function transactions(): HasMany
    {
        return $this->hasMany(Transaction::class);
    }

    /**
     * @return HasMany
     */
    public function reconciliations(): HasMany
    {
        return $this->hasMany(Reconciliation::class);
    }

    /**
     * @return float
     */
    public function getTotalTransactionsAmount(): float
    {
        $total = 0;
        /** @var Reconciliation $reconciliation */
        foreach ($this->reconciliations as $reconciliation) {
            $total += $reconciliation->getTotalTransactionsAmount();
        }

        /** @var Transaction $transaction */
        $transactions = $this->transactions->where('reconciliation_id', null);
        foreach ($transactions as $transaction) {
            $total += $transaction->getCreditOrDebit();
        }

        return $total;
    }

    /**
     * @return float
     */
    public function getVariance(): float
    {
        return $this->transactions->where('reconciliation_id', null)->sum(function (Transaction $transaction) {
            return $transaction->getCreditOrDebit();
        });
    }

    /**
     * Covered in test_account_repository_get_accounts_for_index_page_function.
     *
     * @return Collection
     */
    public function getBatchTableReconciliations(): Collection
    {
        if ($this->batchTableWithPreviousMonths > 0) {
            return $this->reconciliations->where('created_at', '>', now()->subMonths($this->batchTableWithPreviousMonths)->startOfMonth());
        }

        return $this->reconciliations->where('is_fully_reconciled', false);
    }

    /**
     * @return Collection
     */
    public function getUnallocatedTransactionGroups(): Collection
    {
        $transactions = $this->transactions->where('reconciliation_id', null);
        $groups       = [];

        /** @var Transaction $transaction */
        foreach ($transactions as $transaction) {
            $reference_id = $transaction->getReferenceId();
            if (!$reference_id) {
                continue;
            }

            if (!isset($groups[$reference_id])) {
                $groups[$reference_id] = collect([]);
            }

            $groups[$reference_id]->push($transaction);
        }

        $groups = collect($groups)->reject(function ($group) {
            return $group->count() < 2;
        });

        return $groups;
    }

    /**
     * @return Collection
     */
    public function getUnallocatedTransactionsWithoutGrouping(): Collection
    {
        $transactions = $this->transactions->where('reconciliation_id', null);
        $references   = [];

        //Count references, and find the repeating ones.
        // Then filter out the transactions based on that.
        /** @var Transaction $transaction */
        foreach ($transactions as $transaction) {
            $reference_id = $transaction->getReferenceId();

            if (is_null($reference_id)) {
                continue;
            }

            if (!isset($references[$reference_id])) {
                $references[$reference_id] = 0;
            }
            $references[$reference_id]++;
        }

        // remove all transactions that have a reference id and it's count is more than 1,
        // cause that means there is more than one transaction with that reference id
        $transactions = $transactions->reject(function (Transaction $transaction) use ($references) {
            return !is_null($transaction->getReferenceId()) && $references[$transaction->getReferenceId()] > 1;
        });

        return $transactions;
    }

    /**
     * @param int $months
     *
     * @return Account
     */
    public function setBatchTableWithPreviousMonths(int $months = 0): self
    {
        $this->batchTableWithPreviousMonths = $months;

        return $this;
    }
}
