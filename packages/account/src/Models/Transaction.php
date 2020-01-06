<?php

namespace Account\Models;

use Account\Traits\CommentsHandler;
use App\User;
use Carbon\Carbon;
use App\Traits\Auditable;
use App\Traits\Cacheable;
use Illuminate\Database\Eloquent\Model;
use Account\DTO\TransactionReferenceIdData;
use Account\TransactionAlertSystem\Interval;
use Account\TransactionAlertSystem\Intervals;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Class Transaction.
 * @property Carbon $transaction_date
 */
class Transaction extends Model
{
    use SoftDeletes, Auditable, Cacheable, CommentsHandler;

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

    public static $searchable = [
        'reference',
        'journal',
    ];

    protected $casts = [
        'credit_amount' => 'float',
        'debit_amount' => 'float',
    ];

    /**
     * Cache for reference_id so we dont pregmatch all the time
     * reach this with $this->getReferenceId();.
     *
     * @var TransactionReferenceIdData|null
     */
    protected $reference_id = null;

    /**
     * @return MorphMany
     */
    public function comments(): MorphMany
    {
        return $this->morphMany(Comment::class, 'commentable');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function account()
    {
        return $this->belongsTo(Account::class, 'account_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function reconciliation()
    {
        return $this->belongsTo(Reconciliation::class);
    }

    /**
     * @return Carbon
     */
    public function getTransactionDate(): Carbon
    {
        return Carbon::parse($this->transaction_date);
    }

    /**
     * @param string $value
     *
     * @return null|string
     */
    public function getTransactionDateAttribute($value)
    {
        return $value ? Carbon::parse($value)->format(config('panel.date_format')) : null;
    }

    /**
     * @param string $value
     */
    public function setTransactionDateAttribute($value)
    {
        $this->attributes['transaction_date'] = $value ? Carbon::parse($value)->format('Y-m-d') : null;
    }

    /**
     * @return float
     */
    public function getCreditOrDebit(): float
    {
        return (float) ($this->credit_amount > 0.0 ? -$this->credit_amount : $this->debit_amount);
    }

    /**
     * Parses out a transaction id from reference.
     *
     * @param bool $fresh
     *
     * @return null|TransactionReferenceIdData
     *
     * Should match all of these references
     * 'TA1234 Testing',
     * 'TA1234AD Test Reference',
     * 'Test TA1234AD Reference',
     * 'Test TA1234 Reference',
     * 'Test Reference TA1234',
     * '<reverse> Test reference',
     * '<reversal> test reference',
     * "Duffy feb '19 CC Testing",
     * "FEB CC: Duffy",
     */
    public function getReferenceId(bool $fresh = false): ?TransactionReferenceIdData
    {
        if (is_null($this->reference_id) || $fresh) {
            $this->reference_id = TransactionReferenceIdData::make($this->reference);
        }

        return $this->reference_id;
    }

    /**
     * @param string $comment
     *
     * @return Transaction
     */
    public function updateComment(string $comment = null): self
    {
        $this->comment = $comment;
        $this->save();

        return $this;
    }

    /**
     * @return Interval
     * //TODO TEST
     */
    public function getInterval(): Interval
    {
        return $this->cache('lateInterval', function () {
            return (new Intervals())->getIntervalByTransaction($this);
        });
    }

    /**
     * @return int
     */
    public function getDaysSinceTransaction(): int
    {
        return $this->getTransactionDate()->diffInDays(now());
    }

}
