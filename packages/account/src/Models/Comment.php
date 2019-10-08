<?php

namespace Account\Models;

use App\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class Comment extends Model
{
    const SCOPE_INTERNAL = 'internal';
    const SCOPE_PUBLIC   = 'public';

    const MODAL_TRANSACTION    = 'transaction';
    const MODAL_RECONCILIATION = 'reconciliation';

    protected $fillable = ['comment', 'comentable_id', 'commentable_type', 'user_id', 'scope', 'modal_type'];

    protected $appends = ['created_at_formatted'];

    /**
     * Get the owning commentable model.
     */
    public function commentable()
    {
        return $this->morphTo();
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function scopeIsPublic(Builder $q)
    {
        $q->where('scope', self::SCOPE_PUBLIC);
    }

    public function getCreatedAtFormattedAttribute()
    {
        return $this->created_at->format('m-d-Y');
    }

}
