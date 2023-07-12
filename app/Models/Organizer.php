<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Validation\Rules\Password;
use Laravel\Passport\HasApiTokens;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Foundation\Auth\User as Authenticatable;

class Organizer extends Authenticatable
{
    use HasFactory;
    use HasApiTokens;


    protected $fillable = [
        'email',
        'password',
        'professional_corporation_name',
        'name_of_person_in_charge',
        'phone_number',
        'square_one_members_id',
    ];
    protected $hidden = [
        'password',
        'confirmation_code',
    ];
    public function setPasswordAttribute($value)
    {
        $this->attributes['password'] = Hash::make($value);
    }
    /**
     * Query by email and user type
     * @param Builder $q
     * @param string $email
     * @return Builder
     */
    public function scopeEmailAndType(Builder $q, string $email): Builder
    {
        return $q->where([
            ['email', 'ilike', $email]
        ]);
    }
}
