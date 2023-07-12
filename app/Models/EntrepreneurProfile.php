<?php

namespace App\Models;

use Database\Factories\EntrFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;

/**
 * Each user of type entrepreneur can have an entrepreneur profile
 * Class EntrepreneurProfile
 * @package App\Models
 */
class EntrepreneurProfile extends Model
{
    use HasFactory;

    protected $guarded = [];

    /**
     * For the transfer column, either of these values are accepted.
     */
    public const TRANSFER_VAL = ['yes', 'no', 'only domestic', 'only overseas'];

    public function prefecture(): BelongsTo
    {
        return $this->belongsTo(Prefecture::class);
    }

    public function area(): BelongsTo
    {
        return $this->belongsTo(Area::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id')
            ->where('type', User::ENTR);
    }

    public function eduBg(): BelongsTo
    {
        return $this->belongsTo(EducationBackground::class, 'education_background_id');
    }

    public function workingStatus(): BelongsTo
    {
        return $this->belongsTo(WorkingStatus::class, 'working_status_id');
    }

    public function presentPost(): BelongsTo
    {
        return $this->belongsTo(PresentPost::class, 'present_post_id');
    }

    public function occupation(): BelongsTo
    {
        return $this->belongsTo(Occupation::class, 'occupation_id');
    }

    /**
     * Daily language used by the entrepreneur
     * @return BelongsTo
     */
    public function lang(): BelongsTo
    {
        return $this->belongsTo(Language::class, 'lang_id');
    }

    /**
     * Language level of the entrepreneur.
     * This refers to the lang level of *value of land_id column
     */
    public function langAbility(): BelongsTo
    {
        return $this->belongsTo(LangLevel::class, 'lang_level_id');
    }

    /**
     * English language level of the entrepreneur
     */
    public function engLangAbility(): BelongsTo
    {
        return $this->belongsTo(LangLevel::class, 'en_lang_level_id');
    }

    /**
     * Factory attached to this model
     * @return EntrFactory
     */
    protected static function newFactory(): EntrFactory
    {
        return EntrFactory::new();
    }

    /**
     * Expected income range of the entrepreneur
     * @return BelongsTo
     */
    public function expectedIncome(): BelongsTo
    {
        return $this->belongsTo(IncomeRange::class, 'expected_income_range_id');
    }

    /**
     * Industry where the entr has worked in the past
     * @return HasManyThrough
     * @noinspection PhpUnused
     */
    public function industriesExp(): HasManyThrough
    {
        return $this->hasManyThrough(
            Industry::class,
            EntrExpIndustry::class,
            secondKey: 'id',
            secondLocalKey: 'industry_id'
        );
    }

    public function expIndustries(): HasMany
    {
        return $this->hasMany(EntrExpIndustry::class);
    }

    public function pfdIndustries(): HasMany
    {
        return $this->hasMany(EntrPfdIndustry::class);
    }

    /** @noinspection PhpUnused */
    public function industriesPfd(): HasManyThrough
    {
        return $this->hasManyThrough(
            Industry::class,
            EntrPfdIndustry::class,
            secondKey: 'id',
            secondLocalKey: 'industry_id'
        );
    }

    public function pfdPrefectures(): HasMany
    {
        return $this->hasMany(EntrPfdPrefecture::class);
    }

    public function prefecturesPfd(): HasManyThrough
    {
        return $this->hasManyThrough(
            Prefecture::class,
            EntrPfdPrefecture::class,
            secondKey: 'id',
            secondLocalKey: 'prefecture_id'
        );
    }

    /**
     * @return BelongsTo
     * @noinspection PhpUnused
     */
    public function managementExp(): BelongsTo
    {
        return $this->belongsTo(MgmtExp::class, 'management_exp_id');
    }

    /**
     * Preferred positions of the entrepreneur
     * @return HasMany
     */
    public function pfdPositions(): HasMany
    {
        return $this->hasMany(EntrPfdPosition::class);
    }

    /**
     * Use this so that you don't need to have nested relationship to get the positions
     * @return HasManyThrough
     * @noinspection PhpUnused
     */
    public function positionsPfd(): HasManyThrough
    {
        return $this->hasManyThrough(
            Position::class,
            EntrPfdPosition::class,
            secondKey: 'id',
            secondLocalKey: 'position_id'
        );
    }
    public function occupationsPfd(): HasManyThrough
    {
        return $this->hasManyThrough(
            Occupation::class,
            EntrPfdOccupation::class,
            'entrepreneur_profile_id', // Foreign key on EntrPfdArea table
            'id', // Local key on EntrepreneurProfile table
            'id', // Local key on Area table
            'occupation_id' // Foreign key on EntrPfdArea table
        );
    }
    public function pfdOccupations(): HasMany
    {
        return $this->hasMany(EntrPfdOccupation::class);
    }
    public function areasPfd(): HasManyThrough
    {
        return $this->hasManyThrough(
            Area::class,
            EntrPfdArea::class,
            'entrepreneur_profile_id', // Foreign key on EntrPfdArea table
            'id', // Local key on EntrPfdOccupation table
            'id', // Local key on Area table
            'area_id' // Foreign key on EntrPfdArea table
        );
    }
    public function pfdAreas(): HasMany
    {
        return $this->hasMany(EntrPfdArea::class);
    }
}
