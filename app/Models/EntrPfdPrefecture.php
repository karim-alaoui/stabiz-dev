<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Class EntrPfdPrefecture
 * @package App\Models
 */
class EntrPfdPrefecture extends Model
{
    use HasFactory;

    public $incrementing = false;

    protected $fillable = ['entrepreneur_profile_id', 'prefecture_id'];

    public $timestamps = false;
}
