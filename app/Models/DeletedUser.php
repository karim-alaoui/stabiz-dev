<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Class DeletedUser
 * @package App\Models
 */
class DeletedUser extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'email'
    ];

    public $timestamps = false;
}
