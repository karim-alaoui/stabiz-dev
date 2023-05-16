<?php

namespace App\Providers;

use App\Models\Application;
use App\Models\AssignedUserToStaff;
use App\Models\Recommendation;
use App\Models\Staff;
use App\Models\UploadedDoc;
use App\Models\User;
use App\Policies\ApplicationPolicy;
use App\Policies\AssignUserToStaffPolicy;
use App\Policies\RecommendationPolicy;
use App\Policies\StaffPolicy;
use App\Policies\UploadedDocPolicy;
use App\Policies\UserPolicy;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rules\Password;
use Laravel\Passport\Passport;

/**
 * Class AuthServiceProvider
 * @package App\Providers
 */
class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array
     */
    protected $policies = [
        User::class => UserPolicy::class,
        AssignedUserToStaff::class => AssignUserToStaffPolicy::class,
        Staff::class => StaffPolicy::class,
        Application::class => ApplicationPolicy::class,
        Recommendation::class => RecommendationPolicy::class,
        UploadedDoc::class => UploadedDocPolicy::class
    ];

    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerPolicies();

        // default password validation
        Password::defaults(function () {
            return Password::min(8)
                ->letters()
                ->numbers();
        });

        // config for passport
        Passport::routes();

        Passport::tokensExpireIn(now()->addDays(15));
        Passport::refreshTokensExpireIn(now()->addDays(30));
        Passport::personalAccessTokensExpireIn(now()->addMonths(6));

        // super admin can do anything
        // it must return a boolean and not null and true as it's important
        // Read this blog if you don't understand https://murze.be/when-to-use-gateafter-in-laravel
        Gate::after(function ($user) {
            if ($user instanceof Staff) {
                return $user->hasRole(Staff::SUPER_ADMIN_ROLE);
            }
            return false;
        });
    }
}
