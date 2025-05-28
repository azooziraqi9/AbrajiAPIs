<?php

namespace Modules\Authentication\Providers;

use Illuminate\Auth\Events\Registered;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use Modules\Verification\Listeners\SendOtpForEmailVerification;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event handler mappings for the application.
     *
     * @var array<string, array<int, string>>
     */
    protected $listen = [];

    /**
     * Indicates if events should be discovered.
     *
     * @var bool
     */
    protected static $shouldDiscoverEvents = true;

    /**
     * Configure the proper event listeners for email verification.
     *
     * @return void
     */
    protected function configureEmailVerification(): void
    {
        $this->listen[Registered::class] = [
            SendOtpForEmailVerification::class,
        ];
    }
}
