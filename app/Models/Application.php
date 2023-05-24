<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Applying for entrepreneur/founder
 * Class Application
 * @package App\Models
 */
class Application extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'applied_to_user_id',
        'applied_by_user_id',
        'founder_NDA',
        'entrepreneur_NDA',
        'accepted_at',
        'rejected_at'
    ];

    protected $hidden = ['deleted_at', 'updated_at'];

    public function appliedTo(): BelongsTo
    {
        return $this->belongsTo(User::class, 'applied_to_user_id');
    }

    /**
     * @return BelongsTo
     * @noinspection PhpUnused
     */
    public function appliedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'applied_by_user_id');
    }

    /**
     * Active applications
     * @param Builder $q
     * @return Builder
     */
    public function scopeActive(Builder $q): Builder
    {
        return $q->whereNull('rejected_at');
    }

    /**
     * Applications which have not been either rejected or accepted
     * Basically, not responded
     * @param Builder $q
     * @return Builder
     */
    public function scopeNotResponded(Builder $q): Builder
    {
        return $q->whereNull('rejected_at')
            ->whereNull('accepted_at');
    }

    /**
     * @param Builder $q
     * @return Builder
     */
    public function scopeRejected(Builder $q): Builder
    {
        return $q->whereNotNull('rejected_at')
            ->whereNull('accepted_at');
    }

    /**
     * @param Builder $q
     * @return Builder
     */
    public function scopeAccepted(Builder $q): Builder
    {
        return $q->whereNull('rejected_at')
            ->whereNotNull('accepted_at');
    }
}
