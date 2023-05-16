<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Class Occupation
 * @package App\Models
 */
class Position extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = ['name'];

    public $timestamps = false;
}
