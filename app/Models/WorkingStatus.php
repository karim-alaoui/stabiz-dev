<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Class WorkingStatus
 * @package App\Models
 */
class WorkingStatus extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'label',
    ];

    public $timestamps = false;
}
