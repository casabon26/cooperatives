<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Gate;
use Illuminate\Pagination\Paginator;
use App\Models\Cooperative;
use App\Policies\CooperativePolicy;


class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Gate::define('access-admin', function ($user = null) {
            $role = $user ? ($user->role ?? 'public') : 'public';
            return in_array($role, ['gov_admin']);
        });

        // Register model policy
        Gate::policy(Cooperative::class, CooperativePolicy::class);

        // Use Bootstrap 5 pagination view
        Paginator::useBootstrapFive();
    }
}
