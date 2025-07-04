<?php

namespace Nano\Foundation;

use Nano\Http\Contracts\RouterInterface;
use Nano\Http\Router\Route;

class RouteServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(RouterInterface::class, Route::class);
    }
}
