<?php

namespace App\Http\Controllers;

use App\Http\Middleware\AuthAdminMiddleware;
use Core\View;

class HomeController
{
    public function index()
    {
        return View::make('index');
    }
}
