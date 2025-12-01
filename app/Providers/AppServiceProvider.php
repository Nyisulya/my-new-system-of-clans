<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Gate;
use App\Models\Member;
use App\Policies\MemberPolicy;

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
        // Register policies
        Gate::policy(Member::class, MemberPolicy::class);

        // Define Gates
        Gate::define('admin-only', function ($user) {
            return $user->isAdmin();
        });

        // Register Sidebar Menu Listener
        \Illuminate\Support\Facades\Event::listen(
            \JeroenNoten\LaravelAdminLte\Events\BuildingMenu::class,
            [\App\Listeners\BuildSidebarMenu::class, 'handle']
        );
    }
}
