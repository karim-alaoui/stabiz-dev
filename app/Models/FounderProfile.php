<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Class FounderProfile
 * @package App\Models
 */
class FounderProfile extends Model
{
    use HasFactory;

    protected $guarded = [];

    /**
     * The industries where the founder companies belong from
     * The company can belong from multiple industries
     * Max 3 industries can be chosen
     * @return HasMany
     */
    public function companyIndustries(): HasMany
    {
        return $this->hasMany(FdrCompanyIndustry::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id')
            ->where('type', User::FOUNDER);
    }

    public function area(): BelongsTo
    {
        return $this->belongsTo(Area::class, 'area_id');
    }

    public function prefecture(): BelongsTo
    {
        return $this->belongsTo(Prefecture::class, 'prefecture_id');
    }

    public function affiliatedCompanies(): HasMany
    {
        return $this->hasMany(FdrAffiliatedCompany::class);
    }

    public function majorStockHolders(): HasMany
    {
        return $this->hasMany(FdrMajorStockHolder::class);
    }

    /** @noinspection PhpUnused */
    public function pfdPrefectures(): HasMany
    {
        return $this->hasMany(FdrPfdPrefecture::class);
    }

    /**
     * preferred industries from which the founder would like entrepreneurs
     * @return HasMany
     */
    public function pfdIndustries(): HasMany
    {
        return $this->hasMany(FdrPfdIndustry::class)
            ->take(3); // one can have max 3
    }
    public function industries()
    {
        return $this->belongsToMany(Industry::class, 'fdr_pfd_industries', 'founder_profile_id', 'industry_id');
    }
    
    /**
     * Preferred position of entrepreneurs that the founder is looking for
     * @return HasMany
     */
    public function pfdPositions(): HasMany
    {
        return $this->hasMany(FdrPfdPosition::class)
            ->take(3); // max 3
    }

    /**
     * Income that the founder is willing to offer to entrepreneur
     * @return BelongsTo
     */
    public function offeredIncome(): BelongsTo
    {
        return $this->belongsTo(IncomeRange::class, 'offered_income_range_id');
    }
}
