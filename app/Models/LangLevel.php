<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Class LangLevel
 * @package App\Models
 */
class LangLevel extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = ['level'];

    public $timestamps = false;
}
