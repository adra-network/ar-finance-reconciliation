<?php

namespace App;

use App\Traits\Auditable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

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

    /**
     * @return HasMany
     */
    public function monthlySummaries(): HasMany
    {
        return $this->hasMany(AccountMonthlySummary::class);
    }

    /**
     * @return HasMany
     */
    public function transactions(): HasMany
    {
        return $this->hasMany(AccountTransaction::class);
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
    public function getTransactionsTotal(): float
    {
        $total = 0;
        /** @var Reconciliation $reconciliation */
        foreach($this->reconciliations as $reconciliation) {
            $total += $reconciliation->getTransactionsTotal();
        }

        /** @var AccountTransaction $transaction */
        $transactions = $this->transactions->where('reconciliation_id', null);
        foreach($transactions as $transaction) {
            $total += $transaction->getCreditOrDebit();
        }
        return $total;
    }
}
