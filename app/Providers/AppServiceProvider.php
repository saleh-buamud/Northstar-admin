<?php

namespace App\Providers;

use App\Models\Product;
use App\Models\User;
use App\Policies\ProductPolicy;
use App\Policies\UserPolicy;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

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
        Gate::policy(Product::class, ProductPolicy::class);
        Gate::policy(User::class, UserPolicy::class);
        Gate::before(function (User $user, string $ability): ?bool {
            if (str_contains($ability, '.')) {
                return $user->hasPermissionTo($ability) ? true : null;
            }

            return $user->hasRole('super-admin') ? null : ($user->hasPermissionTo($ability) ? true : null);
        });
    }
}
