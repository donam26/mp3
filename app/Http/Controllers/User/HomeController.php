<?php

namespace App\Http\Controllers\User;

use App\Http\Middleware\AuthAdminMiddleware;
use Core\View;

class HomeController
{
    protected $middleware;

    public function __construct()
    {
        $this->middleware = new AuthAdminMiddleware();
    }
    public function index()
    {
        if ($this->middleware->handle() !=='user') {
            return View::make('index');
            return;
        }
        return View::make('user/index');
    }

    public function home()
    {
        if ($this->middleware->handle() !=='user') {
            return View::make('index');
            return;
        }
        return View::make('user/home');
    }

    public function discover()
    {
        if ($this->middleware->handle() !=='user') {
            return View::make('index');
            return;
        }
        return View::make('user/discover');
    }

    public function hotMusic()
    {
        if ($this->middleware->handle() !=='user') {
            return View::make('index');
            return;
        }
        return View::make('user/hotmusic');
    }

    public function recently()
    {
        if ($this->middleware->handle() !=='user') {
            return View::make('index');
            return;
        }
        return View::make('user/recently');
    }

    
}

