<?php

namespace App\Providers;

use App\Models\Company;
use App\Models\Departement;
use App\Models\User;
use App\Policies\DashboardAccess;
use App\Policies\DepartementPolicy;
use App\Policies\EmployeePolicy;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Vite;
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
        Vite::prefetch(concurrency: 3);
        Gate::policy(Departement::class, DepartementPolicy::class);
        Gate::policy(Company::class, EmployeePolicy::class);
        Gate::policy(Company::class, DashboardAccess::class);
        // URL::forceScheme('https');
    }
}
