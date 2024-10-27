<?php

namespace App\Http\Controllers;

use Core\View;
use Core\Database;

class AuthController
{
    protected $connection;

    public function __construct()
    {
        $this->connection = Database::getConnection();
    }

    // Hiển thị trang đăng nhập
    public function showLoginForm()
    {

        // Kiểm tra nếu người dùng đã đăng nhập
        // if (isset($_SESSION['user_id'])) {
        //     // Nếu người dùng đã đăng nhập, chuyển hướng đến trang chính
        //     header('Location: /');
        //     exit;
        // }

        // Nếu chưa đăng nhập, hiển thị form đăng nhập
        return View::make('login');
    }

    // Xử lý đăng nhập
    public function login()
    {
        if($_SESSION['user_id']) {
            header('Location: /');
        }

        if (!isset($_POST['username']) || !isset($_POST['password'])) {
            $_SESSION['error'] = "Vui lòng nhập tên đăng nhập và mật khẩu.";
            header('Location: /login');
            exit;
        }

        // Lấy dữ liệu từ form đăng nhập
        $username = $_POST['username'];
        $password = $_POST['password'];

        // Truy vấn người dùng từ cơ sở dữ liệu
        $stmt = $this->connection->prepare("SELECT id, password, role,username FROM users WHERE username = :username");
        $stmt->bindParam(':username', $username);
        $stmt->execute();
        $user = $stmt->fetch();

        if ($user) {
            // Kiểm tra mật khẩu (nên dùng `password_verify()` để kiểm tra với mật khẩu đã băm)
            if ($user['password'] === $password) {
                // Lưu thông tin vào session
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['role'] = $user['role'];
                $_SESSION['username'] = $user['username'];

                // Chuyển hướng đến trang dashboard hoặc trang chính
                if ($user['role'] === 'admin') {
                    header('Location: /admin/dashboard');
                } else if($user['role'] === 'user') {
                    header('Location: /');
                }
                else {
                    header('Location: /guest');
                }
                exit;
            } else {
                // Mật khẩu không chính xác
                $_SESSION['error'] = "Mật khẩu không chính xác!";
                header('Location: /login');
                exit;
            }
        } else {
            // Tên đăng nhập không tồn tại
            $_SESSION['error'] = "Tên đăng nhập không tồn tại!";
            header('Location: /login');
            exit;
        }
    }


    // Đăng xuất
    public function logout()
    {
        session_unset();
        session_destroy();

        // Chuyển hướng tới trang đăng nhập
        header('Location: /login');
        exit;
    }
}
