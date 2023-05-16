<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Class EntrIndustry
 * @package App\Models
 */
class EntrExpIndustry extends Model
{
    use HasFactory;

    protected $table = 'entr_exp_industries';

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
