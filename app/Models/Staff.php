<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Passport\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;

/**
 * Class Staff
 * @package App\Models
 * @method static assignRole(string[]|string|array $attributes)
 * @method static findOrFail(int $id)
 * @method role(string $role)
 */
class Staff extends Authenticatable
{
    use SoftDeletes;
    use HasFactory;
    use HasApiTokens;
    use Notifiable;
    use HasRoles;

    /**
     * Adding these as constants so that it's easier to access
     * from anywhere. This way there is no chance of any spelling or case mistake
     * since it's case sensitive (postgresql is case sensitive by default)
     */
    public const SUPER_ADMIN_ROLE = 'super-admin';
    public const MATCH_MAKER_ROLE = 'matchmaker';

    protected $fillable = [
        'first_name',
        'last_name',
        'email',
        'password',
        'phone',
    ];

    /**
     * Assigned users to a staff
     * @return HasMany
     */
    public function assignedUsers(): HasMany
    {
        return $this->hasMany(AssignedUserToStaff::class);
    }

    protected $hidden = ['password'];
}
