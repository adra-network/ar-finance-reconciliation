<?php

namespace Account\Models;

use Account\DTO\TransactionReconciliationGroupData;
use App\Traits\Auditable;
use App\User;
use Carbon\CarbonInterface;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Collection;

/**
 * Class Account
 * @package Account\Models
 * @property string $name_formatted //from getter getNameFormattedAttribute()
 */
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

    protected $appends = [
        'name_formatted',
    ];

    public static $searchable = [
        'name',
    ];

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
     * @return string
     */
    public function getNameFormattedAttribute()
    {
        $name = $this->getNameOnly();
        preg_match('/(.+)\-(\d+)/', $this->code, $codeMatches);
        if (isset($codeMatches[2])) {
            return $name . ' - (' . $codeMatches[2] . ')';
        }

        return $name;
    }

    /**
     * @return string
     */
    public function getNameOnly()
    {
        return ltrim(
            str_replace([$this->code, '(', ')', '-', 'A/R'], '', $this->name)
        );
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
        //DEAL WITH EVERYTHING IN CENTS (x100) AND JUST RETURN THE FLOAT VALUE!!!
        //DEAL WITH EVERYTHING IN CENTS (x100) AND JUST RETURN THE FLOAT VALUE!!!
        //DEAL WITH EVERYTHING IN CENTS (x100) AND JUST RETURN THE FLOAT VALUE!!!

        $unreconciledTransactionsSum = $this->transactions->where('reconciliation_id', null)->sum(function (Transaction $transaction) {
            return $transaction->getCreditOrDebit() * 100;
        });

        $lastMonthlySummary = $this->monthlySummaries->sortBy('id')->last();
        $endingBalance = ($lastMonthlySummary) ? $lastMonthlySummary->ending_balance * 100 : 0;

        return ($endingBalance - $unreconciledTransactionsSum) / 100;

    }

    /**
     * @return float
     */
    public function getUnreconciledTransactionsSubtotal(): float
    {
        return $this->transactions->where('reconciliation_id', null)->sum(function (Transaction $transaction) {
            return $transaction->getCreditOrDebit();
        });
    }

    /**
     * Covered in test_account_repository_get_accounts_for_index_page_function.
     * @param bool $showFullyReconciled
     * @param array $dateFilter
     * @return Collection
     */
    public function getBatchTableReconciliations(bool $showFullyReconciled = false, array $dateFilter = [null, null]): Collection
    {
        [$from, $to] = $dateFilter;

        /** @var Collection $reconciliations */
        $reconciliations = $this->reconciliations;

        if ($from instanceof CarbonInterface) {
            $reconciliations = $reconciliations->filter(function (Reconciliation $reconciliation) use ($from) {
                return $reconciliation->created_at->gte($from) || !$reconciliation->is_fully_reconciled;
            });
        }
        if ($to instanceof CarbonInterface) {
            $reconciliations = $reconciliations->filter(function (Reconciliation $reconciliation) use ($to) {
                return $reconciliation->created_at->lte($to) || !$reconciliation->is_fully_reconciled;
            });
        }
        if (!$showFullyReconciled) {
            $reconciliations = $reconciliations->where('is_fully_reconciled', false);
        }

        return $reconciliations;
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
            if (!$reference) {
                continue;
            }

            $dateReference = $reference->getDate();
            if (!is_null($dateReference)) {
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
        if ($this->unallocatedTransactionsWithoutGrouping && !$fresh) {
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

            if (!isset($references[$reference])) {
                $references[$reference] = 0;
            }
            $references[$reference]++;
        }

        // remove all transactions that have a reference id and it's count is more than 1,
        // because that means there is more than one transaction with that reference id
        $transactions = $transactions->reject(function (Transaction $transaction) use ($references) {
            return !is_null($transaction->getReferenceId()->getDateString()) && $references[$transaction->getReferenceId()->getDateString()] > 1;
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
}
