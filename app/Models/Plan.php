<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Class Plan
 * @package App\Models
 */
class Plan extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = ['package_id', 'price', 'currency', 'interval', 'stripe_plan_id'];

    public function package(): BelongsTo
    {
        return $this->belongsTo(Package::class);
    }

    public $timestamps = false;

    protected $hidden = ['deleted_at'];
}
