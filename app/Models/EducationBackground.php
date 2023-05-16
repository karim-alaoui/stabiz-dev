<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Class EducationBackground
 * @package App\Models
 */
class EducationBackground extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'label',
    ];

    public $timestamps = false;
}
