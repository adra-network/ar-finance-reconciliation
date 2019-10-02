<?php

namespace Account\Models;

use Illuminate\Database\Eloquent\Model;

class AccountImport extends Model
{
    protected $fillable = [
        'user_id',
        'title',
        'filename',
        'date_from',
        'date_to',
    ];

    protected $dates = [
        'date_from',
        'date_to',
    ];
}
