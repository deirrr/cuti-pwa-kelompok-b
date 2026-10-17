<?php

namespace App\Providers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;
use Illuminate\View\View as ViewContract;

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
        View::composer(
            ['layouts.pegawai', 'layouts.admin-hr'],
            function (ViewContract $view): void {
                $view->with(
                    'jumlahNotifikasiBelumDibaca',
                    Auth::check() ? Auth::user()->unreadNotifications()->count() : 0,
                );
            },
        );
    }
}
