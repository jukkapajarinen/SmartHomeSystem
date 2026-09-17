<?php

use App\Jobs\CaptureCameras;
use App\Jobs\ReadSensors;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->redirectGuestsTo(fn (Request $request) => $request->expectsJson() ? null : route('login'));
        $middleware->redirectUsersTo('/dashboard');

        $middleware->trimStrings(except: [
            'current_password',
            'password',
            'password_confirmation',
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })
    ->withSchedule(function (Schedule $schedule) {
        $schedule->job(new CaptureCameras())->everyMinute()->withoutOverlapping();
        $schedule->job(new ReadSensors())->everyMinute()->withoutOverlapping();
    })
    ->create();
