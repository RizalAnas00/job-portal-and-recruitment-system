<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Models\Role\Contracts\RoleRepositoryInterface;
use App\Models\Role\Repositories\RoleRepository;
use App\Models\Role\Contracts\RoleServiceInterface;
use App\Models\Role\Services\RoleService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Blade;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(RoleRepositoryInterface::class, RoleRepository::class);
        $this->app->bind(RoleServiceInterface::class, RoleService::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Blade::if('can', function ($permission) {
            /** @var \App\Models\User $user */
            $user = Auth::user();
            return $user && $user->hasPermission($permission);
        });
    }
}
