<?php

namespace Nano\Http\Controllers;

use Nano\Http\Enums\HttpStatusCode;

class HomeController
{
    public function index($id)
    {
        return response()->json(
            ["id" => $id],
        );
    }
}
