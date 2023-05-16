<?php

namespace App\Models;

use App\Actions\CreateStripeCustIfNotExist;
use App\Traits\RelationshipTrait;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\App;
use Laravel\Cashier\Billable;
use Laravel\Passport\HasApiTokens;
use function Illuminate\Events\queueable;

/**
 * Class User
 * @package App\Models
 * @method can(string $ability, Model $model)
 * @method cant(string $ability, Model $model)
 * @method static findOrFail(int $id)
 * @method createOrGetStripeCustomer(array $options = [])
 * @method static find(mixed $userId)
 * @method static entrepreneurs()
 * @method static founders()
 * @method static emailAndType(string $email, string $userType)
 */
class User extends Authenticatable
{
    use HasFactory;
    use Notifiable;
    use HasApiTokens;
    use RelationshipTrait;
    use SoftDeletes;
    use Billable;

    /**
     * User types coded as constants
     * This way there's no chance of spelling mistake or case mistake
     *
     * ---------------------------
     * PLEASE DON'T CHANGE THE CASING
     * OR SPELLING OF THESE VALUES
     * OR ANYTHING. IT WILL CAUSE ERROR
     * FOR THE REST OF THE APPLICATION AS
     * THEY HEAVILY RELY ON THESE VALUES
     * ---------------------------
     */
    public const ENTR = 'entrepreneur';
    public const FOUNDER = 'founder';
    public const ORGANIZER = 'organizer';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'first_name',
        'last_name',
        'type',
        'dob',
        'gender',
        'income_range_id',
        'email',
        'password',
        'dp_full_path',
        'dp_disk',
        'first_name_cana',
        'last_name_cana'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    /**
     * Get a subscription instance by name.
     *
     * This method was defined in ManageSubscription trait under
     * Billable trait. However, since Postgres is case sensitive by default,
     * by mistake we can sometimes get undesired result even after providing the right value
     * just because it's case sensitive. Thus redefining the method here
     * with slight change.
     * @param string $name
     * @return Subscription|null
     */
    public function subscription($name = 'default'): ?Subscription
    {
        return $this->subscriptions->where('name', 'ilike', $name)->first();
    }

    /**
     * User's income
     * @return BelongsTo
     */
    public function income(): BelongsTo
    {
        return $this->belongsTo(IncomeRange::class, 'income_range_id');
    }

    /**
     * Entrepreneur profile for entrepreneur type user
     * @return HasOne
     */
    public function entrProfile(): HasOne
    {
        return $this->hasOne(EntrepreneurProfile::class, 'user_id')
            ->withDefault();
    }

    /**
     * Load the entrepreneur profile with relations as well
     * @return HasOne
     */
    public function entrProfileWithRelations(): HasOne
    {
        return $this->entrProfile()
            ->with(self::entrProfileRelations());
    }

    /**
     * Founder profile for founder type user
     * @return HasOne
     */
    public function fdrProfile(): HasOne
    {
        return $this->hasOne(FounderProfile::class, 'user_id')->withDefault();
    }


    /**
     * Load founder profile with relations
     * @return HasOne
     * @noinspection PhpUnused
     */
    public function fdrProfileWithRelations(): HasOne
    {
        return $this->fdrProfile()
            ->with(self::fdrProfileRelations());
    }

    /**
     * Filter entrepreneurs
     * @param Builder $query
     * @return Builder
     */
    public function scopeEntrepreneurs(Builder $query): Builder
    {
        return $query->where('type', self::ENTR);
    }

    /**
     * Filer founders
     * @param Builder $q
     * @return Builder
     */
    public function scopeFounders(Builder $q): Builder
    {
        return $q->where('type', self::FOUNDER);
    }

    /**
     * Query by email and user type
     * @param Builder $q
     * @param string $email
     * @param string $type
     * @return Builder
     */
    public function scopeEmailAndType(Builder $q, string $email, string $type): Builder
    {
        return $q->where([
            ['email', 'ilike', $email],
            ['type', 'ilike', $type]
        ]);
    }

    public function subscriptions(): HasMany
    {
        return $this->hasMany(Subscription::class);
    }

    /**
     * @return HasOne
     */
//    public function activeSubscription(): HasOne
//    {
//        return $this->hasOne(Subscription::class)
//            ->active();
//    }

//    public function activePlan(): HasOneThrough
//    {
//        return $this->hasOneThrough(
//            Plan::class,
//            UserSubscription::class,
//            secondKey: 'id',
//            secondLocalKey: 'plan_id'
//        );
//    }

    /**
     * Applied applications
     * @return HasMany
     */
    public function apl(): HasMany
    {
        return $this->hasMany(Application::class, 'applied_by_user_id');
    }

    /**
     * Received applications
     * @return HasMany
     */
    public function recvdApl(): HasMany
    {
        return $this->hasMany(Application::class, 'applied_to_user_id');
    }

    /**
     * @return HasMany
     */
    public function articles(): HasMany
    {
        return $this->hasMany(Article::class);
    }

    /**
     * @return HasMany
     */
    public function docs(): HasMany
    {
        return $this->hasMany(UploadedDoc::class);
    }

    /**
     * Staff which were assigned to this user
     * @return HasMany
     */
    public function assignedStaff(): HasMany
    {
        return $this->hasMany(AssignedUserToStaff::class, 'user_id')
            ->select(['id', 'staff_id', 'user_id', 'created_at']);
    }

    /**
     * We make some stripe related requests using Laravel Cashier here
     * @return void
     */
    protected static function booted()
    {
        /**
         * don't run on testing env.
         * Otherwise it will keep on making these requests
         * to Stripe on each and every test since almost every test
         * creates user using UserFactory
         */
        if (App::environment() == 'testing') return;

        /** @noinspection PhpParamsInspection */
        static::created(queueable(function ($user) {
            CreateStripeCustIfNotExist::execute($user);
        }));

        /** @noinspection PhpParamsInspection */
        static::updated(queueable(function ($user) {
            CreateStripeCustIfNotExist::execute($user); // this action updates the user as well
        }));
    }
}
