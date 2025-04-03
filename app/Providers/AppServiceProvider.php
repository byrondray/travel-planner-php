<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Blade;

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
        Blade::component('label', \App\View\Components\Label::class);
        Blade::component('input', \App\View\Components\Input::class);
        Blade::component('button', \App\View\Components\Button::class);
    }
}
