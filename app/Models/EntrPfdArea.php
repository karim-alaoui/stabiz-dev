<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EntrPfdArea extends Model
{
    use HasFactory;
    
    protected $fillable = ['area_id'];

    public function entrepreneurProfile()
    {
        return $this->belongsTo(EntrepreneurProfile::class);
    }

    public function area()
    {
        return $this->belongsTo(Area::class);
    }

}
