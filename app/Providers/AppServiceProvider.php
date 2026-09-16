<?php

namespace App\Providers;

use App\Models\Sensor;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

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
        View::composer('layouts.app', function ($view) {
            $view->with('sensorCount', Sensor::count());
            $view->with('cameraCount', empty(config('services.onvif.stream_url')) ? 0 : 1);
        });
    }
}
