<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use App\Services\BorrowNotificationService;
class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */


    public function boot(): void
    {
        // Composer pour injecter les notifications dans la navbar
        View::composer('FrontOffice.navbar', function ($view) {
            $user = Auth::user();
            $notifications = $user ? $user->notifications()->latest()->take(5)->get() : collect();
            $view->with('notifications', $notifications);
        });
         view()->composer('*', function ($view) {
        BorrowNotificationService::handle();
    });
    }

    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
}
