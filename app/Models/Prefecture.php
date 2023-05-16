<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Class Prefecture
 * @package App\Models
 */
class Prefecture extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'name_ja', 'area_id'
    ];

    public function area(): BelongsTo
    {
        return $this->belongsTo(Area::class, 'id');
    }

    public $timestamps = false;
}
