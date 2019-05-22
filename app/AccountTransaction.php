<?php

namespace App;

use App\Traits\Auditable;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class AccountTransaction extends Model
{
    use SoftDeletes, Auditable;

    public $table = 'account_transactions';

    protected $dates = [
        'updated_at',
        'created_at',
        'deleted_at',
        'transaction_date',
    ];

    const STATUS_SELECT = [
        'none' => 'none',
        'matched' => 'matched',
        'hidden' => 'hidden',
    ];

    protected $fillable = [
        'code',
        'status',
        'journal',
        'comment',
        'reference',
        'account_id',
        'created_at',
        'updated_at',
        'deleted_at',
        'debit_amount',
        'credit_amount',
        'transaction_date',
    ];

    public function account()
    {
        return $this->belongsTo(Account::class, 'account_id');
    }

    public function reconciliation()
    {
        return $this->belongsTo(Reconciliation::class);
    }

    public function getTransactionDateAttribute($value)
    {
        return $value ? Carbon::parse($value)->format(config('panel.date_format')) : null;
    }

    public function setTransactionDateAttribute($value)
    {
        $this->attributes['transaction_date'] = $value ? Carbon::parse($value)->format('Y-m-d') : null;
    }

    public function scopeIsOppositeTo($query, $transaction)
    {
        if ($transaction->debit_amount > 0) {
            return $query->where('credit_amount', '>', 0);
        }
        if ($transaction->credit_amount > 0) {
            return $query->where('debit_amount', '>', 0);
        }
    }


}
