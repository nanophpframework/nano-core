<?php

use Nano\Http\Controllers\HomeController;
use Nano\Http\Middlewares\ErrorHandlingMiddleware;
use Nano\Http\Middlewares\SecondMiddleware;
use Nano\Http\Middlewares\ThirdMiddleware;
use Nano\Http\Request;
use Nano\Http\RequestHandler;
use Nano\Http\Router\Route;

require "vendor/autoload.php";

$middlewares = [
    ErrorHandlingMiddleware::class,
    SecondMiddleware::class,
    ThirdMiddleware::class,
];

$first = null;
/** @var \Nano\Http\Middlewares\AbstractMiddleware|null */
$last = null;
foreach ($middlewares as $key => $class) {
    if ($key === 0 && is_null($last)) {
        /** @var \Nano\Http\Middlewares\AbstractMiddleware */
        $instance = app($class);
        $first = $instance;
    } else {
        $instance = app($class);
        $last->setNext($instance);
    }

    $last = $instance;
}
$request = new Request(
    'GET',
    '/controller/1',
    [],
    '{}'
);

Route::post('/example', function() {
    echo "Chegou no action". PHP_EOL;

    return response()->json(["message" => "Ok"]);
});

Route::get('/controller/{id}', "Nano\Http\Controllers\HomeController@index");

$requestHandler = new RequestHandler(
    new Route,
    app()
);

dump($first->process($request, $requestHandler));