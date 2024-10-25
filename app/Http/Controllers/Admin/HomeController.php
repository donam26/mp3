<?php

namespace App\Http\Controllers\Admin;

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
        if ($this->middleware->handle() !=='admin') {
            return View::make('index');
            return;
        }
        $data = ['title' => 'Welcome to Home'];

        return View::make('admin/index', $data);
    }

    public function home()
    {
        if ($this->middleware->handle() !=='admin') {
            return View::make('index');
            return;
        }
        return View::make('admin/home');
    }
}
