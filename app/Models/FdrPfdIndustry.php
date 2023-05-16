<?php

namespace App\Models;

use Database\Factories\FdrPfdIndustryFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Class Fdr_pfrd_industry
 * @package App\Models
 */
class FdrPfdIndustry extends Model
{
    use HasFactory;

    public $incrementing = false;

    protected $table = 'fdr_pfd_industries';

    protected $fillable = [
        'founder_profile_id',
        'industry_id'
    ];

    public function industry(): BelongsTo
    {
        return $this->belongsTo(Industry::class, 'industry_id');
    }

    /**
     * Factory attached to this model
     * @return FdrPfdIndustryFactory
     */
    protected static function newFactory(): FdrPfdIndustryFactory
    {
        return FdrPfdIndustryFactory::new();
    }

    public $timestamps = false;
}
