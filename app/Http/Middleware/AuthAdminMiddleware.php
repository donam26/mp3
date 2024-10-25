<?php

namespace App\Http\Middleware;

use Core\Database;

class AuthAdminMiddleware
{
    protected $connection;

    public function __construct()
    {
        $this->connection = Database::getConnection();
    }

    public function handle()
    {
        
        // if (!isset($_SESSION['user_id'])) {
        //     // Chuyển hướng đến route đăng nhập thay vì dùng trực tiếp URL
        //     $this->redirectToRoute('/login');
        //     return false;
        // }

        // Truy vấn cơ sở dữ liệu để kiểm tra vai trò của người dùng
        $stmt = $this->connection->prepare("SELECT role FROM users WHERE id = :user_id");
        $stmt->bindParam(':user_id', $_SESSION['user_id']);
        $stmt->execute();
        $user = $stmt->fetch();

        // Kiểm tra quyền của người dùng
        if ($user && $user['role'] === 'admin') {
            return 'admin';
        }
        else if ($user && $user['role'] === 'user') {
            return 'user';
        } else {
            return 'guest';
        }
    }

    protected function redirectToRoute($route)
    {
        // Hàm chuyển hướng tới route mà bạn chỉ định
        header("Location: $route");
        exit();
    }
}
