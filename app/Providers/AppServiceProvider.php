<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Models\Role\Contracts\RoleRepositoryInterface;
use App\Models\Role\Repositories\RoleRepository;
use App\Models\Role\Contracts\RoleServiceInterface;
use App\Models\Role\Services\RoleService;

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
        //
    }
}
