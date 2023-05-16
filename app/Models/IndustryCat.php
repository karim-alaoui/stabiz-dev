<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Class IndustryCat
 * @package App\Models
 */
class IndustryCat extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = 'industry_categories';

    protected $fillable = ['name'];

    public $timestamps = false;
}
