<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EntrPfdOccupation extends Model
{
    use HasFactory;
    
    protected $fillable = ['occupation_id'];
    
    public function entrepreneurProfile()
    {
        return $this->belongsTo(EntrepreneurProfile::class);
    }

    public function occupation()
    {
        return $this->belongsTo(Occupation::class);
    }

}
