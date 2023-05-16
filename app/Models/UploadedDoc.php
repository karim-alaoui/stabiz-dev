<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @method static approvedDoc(string $docName)
 * @method static findOrFail(int $id)
 */
class UploadedDoc extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $guarded = [];

    /**
     * These are the only valid doc names
     * @const array
     */
    public const DOC_NAMES = [
        'all_historical_matter_cert',
        'fin_stmt_prev_fy',
        'tax_pymt_prev_period'
    ];

    /**
     * @param Builder $query
     * @return Builder
     */
    public function scopeApproved(Builder $query): Builder
    {
        return $query->whereNotNull('approved_at')
            ->whereNull('rejected_at');
    }

    /**
     * @param Builder $query
     * @param string $docName
     * @return Builder
     */
    public function scopeApprovedDoc(Builder $query, string $docName): Builder
    {
        return $this->scopeApproved($query)
            ->where('doc_name', $docName);
    }

    /**
     * @param Builder $query
     * @return Builder
     */
    public function scopeRejected(Builder $query): Builder
    {
        return $query->whereNotNull('rejected_at')
            ->whereNull('approved_at');
    }

    /**
     * @param Builder $query
     * @param string $docName
     * @return Builder
     */
    public function scopeRejectedDoc(Builder $query, string $docName): Builder
    {
        return $this->scopeRejected($query)
            ->where('doc_name', $docName);
    }

    /**
     * documents that are not approved or rejected
     * basically they were never touched
     * @param Builder $query
     * @return Builder
     */
    public function scopeNotTouched(Builder $query): Builder
    {
        return $query->whereNull('rejected_at')
            ->whereNull('approved_at');
    }

    /**
     * @return BelongsTo
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
