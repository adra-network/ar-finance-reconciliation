<?php

namespace Account\Models;

use App\User;
use App\Traits\Auditable;
use Illuminate\Support\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\SoftDeletes;
use Account\DTO\TransactionReconciliationGroupData;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Account extends Model
{
    use SoftDeletes, Auditable, Notifiable;

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
        'user_id',
    ];

    public static $searchable = [
        'name',
    ];

    /** @var int */
    public $batchTableWithPreviousMonths = 0;

    /**
     * Cache for unallocated transactions to prevent n+1.
     * @var Collection|null
     */
    private $unallocatedTransactionsWithoutGrouping;

    /**
     * @return BelongsTo
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

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
        $groups = collect([]);

        /** @var Transaction $transaction */
        foreach ($transactions as $transaction) {
            $reference = $transaction->getReferenceId();
            if (! $reference) {
                continue;
            }

            $dateReference = $reference->getDate();
            if (! is_null($dateReference)) {
                $dateReferenceString = $dateReference->format(TransactionReconciliationGroupData::DATE_FORMAT);

                $group = $groups->where('referenceString', $dateReferenceString)->first();
                if (is_null($group)) {
                    $group = new TransactionReconciliationGroupData();
                    $group->setDate($dateReference);
                    $groups->push($group);
                }

                $group->push($transaction);
            }
        }

        $groups = $groups->reject(function (Collection $group) {
            return $group->count() < 2;
        });

        return $groups;
    }

    /**
     * @param bool $fresh
     * @return Collection
     */
    public function getUnallocatedTransactionsWithoutGrouping(bool $fresh = false): Collection
    {
        if ($this->unallocatedTransactionsWithoutGrouping && ! $fresh) {
            return $this->unallocatedTransactionsWithoutGrouping;
        }
        $transactions = $this->transactions->where('reconciliation_id', null);
        $references = [];

        //Count references, and find the repeating ones.
        // Then filter out the transactions based on that.
        /** @var Transaction $transaction */
        foreach ($transactions as $transaction) {
            $reference = $transaction->getReferenceId()->getDateString();

            if (is_null($reference)) {
                continue;
            }

            if (! isset($references[$reference])) {
                $references[$reference] = 0;
            }
            $references[$reference]++;
        }

        // remove all transactions that have a reference id and it's count is more than 1,
        // because that means there is more than one transaction with that reference id
        $transactions = $transactions->reject(function (Transaction $transaction) use ($references) {
            return ! is_null($transaction->getReferenceId()->getDateString()) && $references[$transaction->getReferenceId()->getDateString()] > 1;
        });

        $this->unallocatedTransactionsWithoutGrouping = $transactions;

        return $this->unallocatedTransactionsWithoutGrouping;
    }

    /**
     * @return float
     */
    public function getUnallocatedTransactionsWithoutGroupingTotal(): float
    {
        return $this->getUnallocatedTransactionsWithoutGrouping()->sum(function (Transaction $transaction) {
            return $transaction->getCreditOrDebit();
        });
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
