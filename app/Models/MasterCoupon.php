<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Venturecraft\Revisionable\RevisionableTrait;

/**
 * Class Coupon
 * @package App\Models
 * @method forEveryone()
 */
class MasterCoupon extends Model
{
    use HasFactory;
    use SoftDeletes;
    use RevisionableTrait;

    protected bool $revisionCleanup = true; //Remove old revisions (works only when used with $historyLimit)
    protected int $historyLimit = 20;
    protected bool $revisionCreationsEnabled = true;
    protected array $dontKeepRevisionOf = ['updated_at', 'created_at'];

    protected $guarded = [];

    /**
     * coupons that are for everyone
     * @param Builder $query
     * @return Builder
     */
    public function scopeForEveryone(Builder $query): Builder
    {
        return $query->where('is_for_everyone', true);
    }
}
