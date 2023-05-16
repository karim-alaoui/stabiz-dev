<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Class Lang
 * @package App\Models
 */
class Language extends Model
{
    use HasFactory;

    protected $table = 'languages';

    protected $fillable = [
        'name'
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
