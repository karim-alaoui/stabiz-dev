<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Preferred position that the founder is looking for in entrepreneurs
 * Class FdrPfdPosition
 * @package App\Models
 */
class FdrPfdPosition extends Model
{
    use HasFactory;

    public $incrementing = false;

    protected $fillable = ['founder_profile_id', 'position_id'];

    public $timestamps = false;

    public function founder(): BelongsTo
    {
        return $this->belongsTo(FounderProfile::class);
    }

    public function position(): BelongsTo
    {
        return $this->belongsTo(Position::class);
    }
}
