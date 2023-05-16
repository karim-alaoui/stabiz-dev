<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FounderUser extends Model
{
    protected $table = 'founder_user';
    use HasFactory;

    protected $fillable = [
        'founder_id',
        'user_id',
        'role',
    ];
    protected $casts = [
        'founder_id' => 'integer',
    ];

    public function founder()
    {
        return $this->belongsTo(FounderProfile::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function founderProfile()
    {
        return $this->belongsTo(FounderProfile::class, 'founder_id');
    }
}
