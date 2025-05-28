<?php

namespace Modules\Dashboard\Providers;

use Illuminate\Support\ServiceProvider;
use Modules\Dashboard\Abstract\DashboardServiceInterface;
use Modules\Dashboard\Services\DashboardService;

class DashbordServiceProider extends ServiceProvider
{
    /**
     * Register the service provider.
     */
    public function register(): void
    {

        $this->app->bind(DashboardServiceInterface::class, DashboardService::class);

    }

    /**
     * Get the services provided by the provider.
     */
    public function provides(): array
    {
        return [];
    }
}
