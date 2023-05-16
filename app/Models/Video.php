<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Class Video
 * @package App\Models
 */
class Video extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'title',
        'link',
        'description',
        'staff_id'
    ];
}
