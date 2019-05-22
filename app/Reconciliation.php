<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Reconciliation extends Model
{

    protected $fillable = ['account_id', 'is_fully_reconciled', 'comment'];

    protected $casts = [
        'is_fully_reconciled' => 'bool',
    ];

    protected static function boot()
    {
        parent::boot();
        parent::updating(function (Reconciliation $reconciliation) {
            $reconciliation->cacheIsFullyReconciledAttribute(false);
        });
        parent::creating(function (Reconciliation $reconciliation) {
            $reconciliation->cacheIsFullyReconciledAttribute(false);
        });
        parent::deleting(function (Reconciliation $reconciliation) {
            $reconciliation->transactions()->update(['reconciliation_id' => null]);
        });
    }

    public function account()
    {
        return $this->belongsTo(Account::class);
    }

    public function transactions()
    {
        return $this->hasMany(AccountTransaction::class);
    }

    /**
     * @return bool
     */
    public function isFullyReconciled(): bool
    {
        //should check here if relations are loaded, but sometimes it seems to be a false-positive and i don't know why
        //this can cause n+1, so use with caution
//        if (!$this->relationLoaded('transactions')) {
        $this->load('transactions');
//        }

        $total = 0;

        foreach ($this->transactions as $transaction) {

            //debit amount needs to be covered by a credit
            //credit is displayed with a minus `-` in front
            if ($transaction->debit_amount > 0) {
                $total -= $transaction->debit_amount;
            }
            if ($transaction->credit_amount > 0) {
                $total += $transaction->credit_amount;
            }

        }

        return $total === 0 || $total === 0.0;
    }

    /**
     * Checks if reconciliation is fully reconciled and caches that value to the database
     *
     * @param bool $save
     */
    public function cacheIsFullyReconciledAttribute(bool $save = true): void
    {
        $reconciled = $this->isFullyReconciled();
        if ($reconciled !== (bool)$this->is_fully_reconciled) {
            $this->is_fully_reconciled = $reconciled;
            if ($save) {
                $this->save();
            }
        }
    }

}
