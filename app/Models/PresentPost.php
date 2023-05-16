<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Class PresentPost
 * @package App\Models
 */
class PresentPost extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'name',
    ];

    /**
     * @param $query
     * @return mixed
     */
    public function scopeOther($query): mixed
    {
        return $query->where('name', 'other');
    }

    public $timestamps = false;
}
