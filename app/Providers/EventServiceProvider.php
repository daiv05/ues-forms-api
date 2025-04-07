<?php

namespace App\Providers;

use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Listeners\SendEmailVerificationNotification;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Event;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Auth\Events\Login;
use Illuminate\Auth\Events\Logout;
use Illuminate\Auth\Events\Validated;
use Illuminate\Auth\Events\Verified;

use App\Listeners\LogPasswordResetEvent;
use App\Listeners\LogUserLogoutEvent;
use App\Listeners\LogUserLoginEvent;
use App\Listeners\LogUserRegisteredEvent;
use App\Listeners\LogUserValidatedEvent;
use App\Listeners\LogUserVerifiedEvent;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event to listener mappings for the application.
     *
     * @var array<class-string, array<int, class-string>>
     */
    protected $listen = [
        // Event: Registered
        Registered::class => [
            SendEmailVerificationNotification::class,
            LogUserRegisteredEvent::class,  // Agregar el listener para el evento Registered
        ],

        // Event: Login
        Login::class => [
            LogUserLoginEvent::class, // Registra el listener para Login
        ],

        // Event: Logout
        Logout::class => [
            LogUserLogoutEvent::class, // Registra el listener para Logout
        ],

        // Event: PasswordReset
        PasswordReset::class => [
            LogPasswordResetEvent::class, // Registra el listener para PasswordReset
        ],
        // Event: Validated
        Validated::class => [
            LogUserValidatedEvent::class, // Registra el listener para Validated
        ],

        // Event: Verified
        Verified::class => [
            LogUserVerifiedEvent::class, // Registra el listener para Verified
        ],
    ];

    /**
     * Register any events for your application.
     */
    public function boot(): void
    {
        //
    }

    /**
     * Determine if events and listeners should be automatically discovered.
     */
    public function shouldDiscoverEvents(): bool
    {
        return false;
    }
}
