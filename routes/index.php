<?php

use App\Http\Controllers\Admin\AdminAlbumController;
use App\Http\Controllers\Admin\AdminArtistController;
use App\Http\Controllers\Admin\AdminSongController;
use App\Http\Controllers\Admin\AdminUserController;
use App\Http\Controllers\Api\ApiSongController;
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

$router->add('/admin/songs', [AdminSongController::class, 'index']);
$router->add('/admin/songs/create', [AdminSongController::class, 'create']);
$router->add('/admin/songs/delete', [AdminSongController::class, 'delete']);
$router->add('/admin/songs/edit', [AdminSongController::class, 'edit']);

$router->add('/admin/artists', [AdminArtistController::class, 'index']);
$router->add('/admin/artists/create', [AdminArtistController::class, 'create']);
$router->add('/admin/artists/delete', [AdminArtistController::class, 'delete']);
$router->add('/admin/artists/edit', [AdminArtistController::class, 'edit']);

$router->add('/admin/albums', [AdminAlbumController::class, 'index']);
$router->add('/admin/albums/create', [AdminAlbumController::class, 'create']);
$router->add('/admin/albums/delete', [AdminAlbumController::class, 'delete']);
$router->add('/admin/albums/edit', [AdminAlbumController::class, 'edit']);

$router->add('/admin/users', [AdminUserController::class, 'index']);
$router->add('/admin/users/create', [AdminUserController::class, 'create']);
$router->add('/admin/users/delete', [AdminUserController::class, 'delete']);
$router->add('/admin/users/edit', [AdminUserController::class, 'edit']);

//user
$router->add('/', [UserHomeController::class, 'index']);
$router->add('/register', [AuthController::class, 'register']);
$router->add('//register/submit', [AuthController::class, 'registerSubmit']);
$router->add('/home', [UserHomeController::class, 'home']);

// music
$router->add('/', [UserHomeController::class, 'index']);
$router->add('/discover', [UserHomeController::class, 'discover']);
$router->add('/hot-music', [UserHomeController::class, 'hotMusic']);
$router->add('/recently', [UserHomeController::class, 'recently']);

// Quên mật khẩu
$router->add('/forgot-password', [AuthController::class, 'forgotPassword']); 
$router->add('/forgot-password-submit', [AuthController::class, 'forgotPasswordSubmit'], 'POST');

// Đặt lại mật khẩu

$router->add('/reset-password', [AuthController::class, 'resetPassword']);
$router->add('/reset-password-submit', [AuthController::class, 'resetPasswordSubmit'], 'POST'); 

// api
$router->add('/api/songs', [ApiSongController::class, 'index']);
$router->add('/api/hotmusic', [ApiSongController::class, 'hotmusic']);
$router->add('/api/recently', [ApiSongController::class, 'recently']);
$router->add('/api/discover', [ApiSongController::class, 'discover']);
$router->add('/api/user-history', [ApiSongController::class, 'recordHistory'], 'POST'); 

$router->add('/api/song-random', [ApiSongController::class, 'random']);




$uri = $_SERVER['REQUEST_URI'] ?? '/';
$router->dispatch($uri);
