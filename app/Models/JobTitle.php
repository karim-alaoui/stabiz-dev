<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Class PreferredJobTitle
 * @package App\Models
 */
class JobTitle extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = 'job_titles';

    protected $fillable = [
        'label',
    ];

    protected $hidden = ['deleted_at'];

    public $timestamps = false;
}
