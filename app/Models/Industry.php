<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Class Industry
 * @package App\Models
 */
class Industry extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = 'industries';

    protected $fillable = ['name', 'industry_category_id'];

    /**
     * @return BelongsTo
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(IndustryCat::class, 'industry_category_id');
    }

    protected $hidden = ['deleted_at'];

    public $timestamps = false;
}
