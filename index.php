<?php

use Nano\Foundation\Application;
use Nano\Http\Enums\HttpStatusCode;
use Nano\Http\Request;
use Nano\Http\Router\Route;

require "vendor/autoload.php";

Route::get('/', function() {
    // dump("teste");
    return response()->json(
        ["message" => "Olá qualquer coisa."],
        HttpStatusCode::OK
    );
});

$app = Application::create();
$app->handleRequest(Request::capture());