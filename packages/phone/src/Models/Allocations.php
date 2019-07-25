<?php

namespace Phone\Models;

use Illuminate\Database\Eloquent\Model;

class Allocations extends Model
{
    public $table = 'allocations';

    protected $dates = [
        'created_at',
        'updated_at',
    ];

    protected $fillable = ['name'];
}
