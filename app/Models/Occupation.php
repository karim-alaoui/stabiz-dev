<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Class Position
 * @package App\Models
 */
class Occupation extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = ['name', 'occupation_category_id'];

    /**
     * @return BelongsTo
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(OccupationCategory::class, 'occupation_category_id');
    }

    protected $hidden = ['deleted_at'];

    public $timestamps = false;
}
