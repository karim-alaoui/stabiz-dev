<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Management experiences
 */
class MgmtExp extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = 'management_exps';

    protected $guarded = [];

    public $timestamps = false;
}
