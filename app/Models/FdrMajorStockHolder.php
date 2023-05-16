<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Class FdrMajorStockHolder
 * @package App\Models
 */
class FdrMajorStockHolder extends Model
{
    use HasFactory;

    public $incrementing = false;

    protected $fillable = ['name', 'founder_profile_id'];

    public $timestamps = false;
}
