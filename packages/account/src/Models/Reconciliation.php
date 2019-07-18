<?php

namespace Account\Models;

use Webpatser\Uuid\Uuid;
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
        parent::updating(function (self $reconciliation) {
            $reconciliation->cacheIsFullyReconciledAttribute(false);
        });
        parent::creating(function (self $reconciliation) {
            $reconciliation->cacheIsFullyReconciledAttribute(false);
            $reconciliation->uuid = (string) Uuid::generate(4);
        });
        parent::deleting(function (self $reconciliation) {
            $reconciliation->transactions()->update(['reconciliation_id' => null]);
        });
    }

    public function account()
    {
        return $this->belongsTo(Account::class);
    }

    public function transactions()
    {
        return $this->hasMany(Transaction::class);
    }

    /**
     * @return float
     */
    public function getTotalTransactionsAmount(): float
    {
        $total = 0;

        /** @var Transaction $transaction */
        foreach ($this->transactions as $transaction) {
            $total += $transaction->getCreditOrDebit();
        }

        return $total;
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

        $total = $this->getTotalTransactionsAmount();

        return $total === 0.0;
    }

    /**
     * Checks if reconciliation is fully reconciled and caches that value to the database.
     *
     * @param bool $save
     */
    public function cacheIsFullyReconciledAttribute(bool $save = true): void
    {
        $reconciled = $this->isFullyReconciled();
        if ($reconciled !== (bool) $this->is_fully_reconciled) {
            $this->is_fully_reconciled = $reconciled;
            if ($save) {
                $this->save();
            }
        }
    }
}