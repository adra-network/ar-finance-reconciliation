<?php

namespace App;

use App\Traits\Auditable;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class AccountMonth extends Model
{
    use SoftDeletes, Auditable;

    public $table = 'account_months';

    protected $dates = [
        'month_date',
        'created_at',
        'updated_at',
        'deleted_at',
        'export_date',
    ];

    protected $fillable = [
        'user_id',
        'month_date',
        'net_change',
        'created_at',
        'updated_at',
        'deleted_at',
        'export_date',
        'ending_balance',
        'beginning_balance',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function getMonthDateAttribute($value)
    {
        return $value ? Carbon::parse($value)->format(config('panel.date_format')) : null;
    }

    public function setMonthDateAttribute($value)
    {
        $this->attributes['month_date'] = $value ? Carbon::createFromFormat(config('panel.date_format'), $value)->format('Y-m-d') : null;
    }

    public function getExportDateAttribute($value)
    {
        return $value ? Carbon::parse($value)->format(config('panel.date_format')) : null;
    }

    public function setExportDateAttribute($value)
    {
        $this->attributes['export_date'] = $value ? Carbon::createFromFormat(config('panel.date_format'), $value)->format('Y-m-d') : null;
    }
}
