<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {

    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        \Stancl\Tenancy\Middleware\InitializeTenancyByDomain::$onFail = fn () => redirect(config('app.url'));
        \Stancl\Tenancy\Middleware\InitializeTenancyBySubdomain::$onFail = fn () => redirect(config('app.url'));
    }
}
