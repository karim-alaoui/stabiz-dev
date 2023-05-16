<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Class FdrCompanyIndustry
 * @package App\Models
 */
class FdrCompanyIndustry extends Model
{
    use HasFactory;

    public $incrementing = false;

    protected $fillable = ['founder_profile_id', 'industry_id'];

    public function industry(): BelongsTo
    {
        return $this->belongsTo(Industry::class);
    }

    public $timestamps = false;
}
