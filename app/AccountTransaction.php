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
        'none'    => 'none',
        'matched' => 'matched',
        'hidden'  => 'hidden',
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

    /**
     * Cache for reference_id so we dont pregmatch all the time
     * reach this with $this->getReferenceId();.
     *
     * @var string|null
     */
    protected $reference_id = null;

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
        return $this->credit_amount > 0 ? -$this->credit_amount : $this->debit_amount;
    }

    /**
     * Parses out a transaction id from reference.
     *
     * @param bool $fresh
     *
     * @return null|string
     *
     * Should match all of these references
     * 'TA1234 Testing',
     * 'TA1234AD Test Reference',
     * 'Test TA1234AD Reference',
     * 'Test TA1234 Reference',
     * 'Test Reference TA1234',
     * '<reverse> Test reference',
     * '<reversal> test reference',
     */
    public function getReferenceId(bool $fresh = false): ?string
    {
        if (!is_null($this->reference_id) && !$fresh) {
            return $this->reference_id;
        }
        if (preg_match('/(TA[0-9]+)/', $this->reference, $matches)) {
            return $matches[0];
        }
        if (preg_match('/(\<reverse\>)|(\<reversal\>)/i', $this->reference, $matches)) {
            return 'reverse';
        }

        return null;
    }

    /**
     * @param string $comment
     *
     * @return AccountTransaction
     */
    public function updateComment(string $comment): self
    {
        $this->comment = $comment;
        $this->save();

        return $this;
    }
}
