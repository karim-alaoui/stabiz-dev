<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Class EntrPfdIndustry
 * @package App\Models
 */
class EntrPfdIndustry extends Model
{
    use HasFactory;

    public $incrementing = false;

    protected $fillable = ['entrepreneur_profile_id', 'industry_id'];

    /**
     * @return BelongsTo
     */
    public function entrProfile(): BelongsTo
    {
        return $this->belongsTo(EntrepreneurProfile::class);
    }

    public function industry(): BelongsTo
    {
        return $this->belongsTo(Industry::class);
    }

    public $timestamps = false;
}
