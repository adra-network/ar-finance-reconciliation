<?php

namespace Account\Models;

use Carbon\Carbon;
use App\Traits\Auditable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MonthlySummary extends Model
{
    use SoftDeletes, Auditable;

    public $table = 'account_period_summaries';

    protected $dates = [
        'month_date', //SHOULD BE DEPRECATED AFTER WE UNDERSTANG HOW TO USE date_from AND date_to
        'created_at',
        'updated_at',
        'deleted_at',
        'export_date',
        'date_from',
        'date_to',
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
        'account_import_id',
        'date_from',
        'date_to',
        'beginning_balance_in_sync',
    ];

    protected $casts = [
        'beginning_balance_in_sync' => 'bool',
    ];

    /**
     * @return BelongsTo
     */
    public function accountImport(): BelongsTo
    {
        return $this->belongsTo(AccountImport::class);
    }

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

    public function beginningBalanceNotInSync(): void
    {
        $this->beginning_balance_in_sync = false;
        $this->save();
    }
}
