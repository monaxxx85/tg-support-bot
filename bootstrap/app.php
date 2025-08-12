<?php

use App\Telegram\Formatters\HtmlFormatter;
use App\Telegram\Presenters\ErrorPresenter;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use DefStudio\Telegraph\Facades\Telegraph;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__ . '/../routes/web.php',
        commands: __DIR__ . '/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        //
    })
    ->withExceptions(function (Exceptions $exceptions): void {

        $exceptions->report(function (Throwable $e) {

            $errorPresenter = new ErrorPresenter($e);
            $message = (new HtmlFormatter())->render($errorPresenter);

            Telegraph::chat(config('telegraph.support_group_id'))
                 ->message($message)
                 ->send();
        });

    })->create();
