<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Reconciliation extends Model
{

    protected $fillable = ['account_id', 'is_fully_reconciled', 'comment'];

    public function account()
    {
        return $this->belongsTo(Account::class);
    }

    public function transactions()
    {
        return $this->hasMany(Transaction::class);
    }

}
