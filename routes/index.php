<?php

use App\Http\Controllers\Admin\HomeController as AdminHomeController;
use App\Http\Controllers\User\HomeController as UserHomeController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\HomeController;
use Core\Router;
use Core\Container;


$container = new Container();

// Bind PaymentInterface với CashPayment
$container->bind(App\Services\PaymentInterface::class, function() {
    return new App\Services\CashPayment();
});

// Khởi tạo router với container
$router = new Router($container);

// List router

// khách
$router->add('/guest', [HomeController::class, 'index']);

// login 
$router->add('/login', [AuthController::class, 'showLoginForm']);
$router->add('/login/submit', [AuthController::class, 'login'], 'POST'); // Sử dụng phương thức POST để xử lý đăng nhập
$router->add('/logout', [AuthController::class, 'logout']);

//admin
$router->add('/admin/dashboard', [AdminHomeController::class, 'index']);


//user
$router->add('/', [UserHomeController::class, 'index']);
$router->add('/home', [UserHomeController::class, 'home']);



$uri = $_SERVER['REQUEST_URI'] ?? '/';
$router->dispatch($uri);
