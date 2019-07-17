<?php

namespace Account\Models;

use App\Traits\Auditable;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class MonthlySummary extends Model
{
    use SoftDeletes, Auditable;

    public $table = 'account_monthly_summaries';

    protected $dates = [
        'month_date',
        'created_at',
        'updated_at',
        'deleted_at',
        'export_date',
    ];

    protected $fillable = [
        'account_id',
        'month_date',
        'net_change',
        'created_at',
        'updated_at',
        'deleted_at',
        'export_date',
        'ending_balance',
        'beginning_balance',
    ];

    public function account()
    {
        return $this->belongsTo(Account::class, 'account_id');
    }

    public function getMonthDateAttribute($value)
    {
        return $value ? Carbon::parse($value)->format(config('panel.date_format')) : null;
    }

    public function setMonthDateAttribute($value)
    {
        $this->attributes['month_date'] = $value ? Carbon::parse($value)->format('Y-m-d') : null;
    }
}
