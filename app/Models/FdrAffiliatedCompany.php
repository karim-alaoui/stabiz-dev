<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Class FDRAffiliatedCompany
 * @package App\Models
 */
class FdrAffiliatedCompany extends Model
{
    use HasFactory;

    public $incrementing = false;

    protected $table = 'fdr_affiliated_companies';

    protected $fillable = ['founder_profile_id', 'company_name'];

    public $timestamps = false;
}
