<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * This package is equivalent to product on Stripe
 * Class Package
 * @package App\Models
 */
class Package extends Model
{
    use HasFactory;
    use SoftDeletes;

    /**
     * Premium package name
     * Keep it like this and use it everywhere to avoid case insensitive issue or any misspelling issue
     * @var string
     */
    public const PREMIUM = 'Premium';

    protected $fillable = ['name', 'stripe_product_id'];

    protected $hidden = ['deleted_at'];

    public function plans(): HasMany
    {
        return $this->hasMany(Plan::class);
    }

    /**
     * @return HasOne
     * @noinspection PhpUnused
     */
    public function monthlyPlan(): HasOne
    {
        return $this->hasOne(Plan::class)
            ->where('interval', 'month');
    }

    public $timestamps = false;
}
