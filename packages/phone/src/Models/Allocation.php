<?php

namespace Phone\Models;

use Illuminate\Database\Eloquent\Model;

class Allocation extends Model
{
    public $table = 'allocations';

    protected $dates = [
        'created_at',
        'updated_at',
    ];

    protected $fillable = ['name'];
}
