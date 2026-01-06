<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Gate;

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

        Gate::define('isAdmin', function ($user) {
            // Ensure the user's email is verified
            // if ($user->role === 'admin' && !$user->hasVerifiedEmail()) {
            //     return false;
            // }
            return $user->role === 'admin';
        });

        Gate::define('isApplicant', function ($user) {
            // Ensure the user's email is verified
            if ($user->role === 'applicant' && !$user->hasVerifiedEmail()) {
                return false;
            }
            return $user->role === 'applicant';
        });
    }
}
