<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Class IncomeRange
 * @package App\Models
 */
class IncomeRange extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = 'income_ranges';

    protected $guarded = [];

    protected $hidden = ['deleted_at'];

    public $timestamps = false;
}
