<?php /** @noinspection UnknownColumnInspection */

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @method static where(string|array|array[] $column, string $string = null, string $string = null)
 * @method static create(string[] $array)
 */
class OTP extends Model
{
    use HasFactory;

    protected $table = 'otps';

    protected $fillable = [
        'email',
        'otp',
        'is_invalid',
        'expired_at',
        'verified_at'
    ];

    /**
     * @param Builder $query
     * @return Builder
     */
    public function scopeNotVerified(Builder $query): Builder
    {
        return $query->whereNull('verified_at')
            ->where('expired_at', '>=', now());
    }

    /**
     * @param Builder $query
     * @return Builder
     */
    public function scopeNotUsed(Builder $query): Builder
    {
        return $query->whereNull('verified_at');
    }
}
