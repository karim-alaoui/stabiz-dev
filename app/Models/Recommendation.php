<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @method static first()
 */
class Recommendation extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $guarded = [];

    /**
     * The user who is recommended
     * @return BelongsTo
     */
    public function recommendedUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'recommended_user_id');
    }

    /**
     * The user to whom the user above user is recommended to
     * @return BelongsTo
     */
    public function recommendedToUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'recommended_to_user_id');
    }

    /**
     * @return BelongsTo
     */
    public function staff(): BelongsTo
    {
        return $this->belongsTo(Staff::class, 'by_staff_id');
    }
}
