<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Class AssignedUserToStaff
 * @package App\Models
 * @method static findOrFail(int $id)
 */
class AssignedUserToStaff extends Model
{
    use HasFactory;

    protected $fillable = [
        'staff_id',
        'user_id',
        'added_by_staff_id'
    ];

    public function staff(): BelongsTo
    {
        return $this->belongsTo(Staff::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function entrepreneur(): BelongsTo
    {
        return $this->user()->where('type', User::ENTR);
    }

    public function founder(): BelongsTo
    {
        return $this->user()->where('type', User::FOUNDER);
    }

    public $timestamps = false;
}
