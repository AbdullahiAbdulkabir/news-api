<?php

use App\Console\Commands\FetchNewsCommand;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__ . '/../routes/web.php',
        api: __DIR__ . '/../routes/api.php',
        commands: __DIR__ . '/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        //
    })->withSchedule(function (Schedule $schedule) {
//        Can be scheduled to run every 30minn
//        $schedule->call(FetchNewsCommand::class)->everyThirtyMinutes();
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();
