<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

use App\Http\Responses\LoginResponse;
use App\Http\Responses\RegistrationResponse;
use App\Http\Responses\AdminLogoutResponse;

use Laravel\Fortify\Contracts\LoginResponse as LoginResponseContract;
use Laravel\Fortify\Contracts\RegisterResponse;
use App\Services\ActivityLogger;
use Illuminate\Auth\Events\Login;
use Illuminate\Auth\Events\Logout;
use Illuminate\Support\Facades\Event;
use Filament\Support\Facades\FilamentView;
use Filament\View\PanelsRenderHook;

use Filament\Auth\Http\Responses\Contracts\LogoutResponse as AdminLogoutResponseContract;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // Response setelah login pengguna.
        $this->app->singleton(
            LoginResponseContract::class,
            LoginResponse::class
        );

        // Response setelah registrasi pengguna.
        $this->app->singleton(
            RegisterResponse::class,
            RegistrationResponse::class
        );

        // Response setelah admin logout dari Filament.
        $this->app->singleton(
            AdminLogoutResponseContract::class,
            AdminLogoutResponse::class
        );
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Event::listen(Login::class, function (Login $event) {
        ActivityLogger::log(
            'login',
            'Pengguna berhasil login ke dalam sistem.',
            $event->user
        );
    });

    Event::listen(Logout::class, function (Logout $event) {
        ActivityLogger::log(
            'logout',
            'Pengguna berhasil logout dari sistem.',
            $event->user
        );
    });

    FilamentView::registerRenderHook(
    PanelsRenderHook::TOPBAR_END,
    fn () => view('components.notification-bell')
);
    }
}
