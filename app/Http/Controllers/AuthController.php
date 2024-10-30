<?php

namespace App\Http\Controllers;

use Core\View;
use Core\Database;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;


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
        if ($_SESSION['user_id']) {
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
                } else if ($user['role'] === 'user') {
                    header('Location: /');
                } else {
                    header('Location: /guest');
                }
                exit;
            } else {
                // Mật khẩu không chính xác
                $_SESSION['error'] = "Mật khẩu không chính xác!";
                header('Location: /');
                exit;
            }
        } else {
            // Tên đăng nhập không tồn tại
            $_SESSION['error'] = "Tên đăng nhập không tồn tại!";
            header('Location: /');
            exit;
        }
    }


    // Đăng xuất
    public function logout()
    {
        session_unset();
        session_destroy();

        // Chuyển hướng tới trang đăng nhập
        header('Location: /');
        exit;
    }

    public function register()
    {
        return View::make('register');
    }
    public function registerSubmit()
    {
        $missing_fields = [];

        if (empty($_POST['username'])) {
            $missing_fields[] = "Tên đăng nhập";
        }

        if (empty($_POST['email'])) {
            $missing_fields[] = "Email";
        }

        if (empty($_POST['password'])) {
            $missing_fields[] = "Mật khẩu";
        }

        if (empty($_POST['confirm_password'])) {
            $missing_fields[] = "Xác nhận mật khẩu";
        }

        if (!empty($missing_fields)) {
            // Ghép các trường còn thiếu thành chuỗi để hiển thị
            $_SESSION['error'] = "Vui lòng điền đầy đủ thông tin: " . implode(", ", $missing_fields) . ".";
            header('Location: /register');
            exit;
        }


        // Lấy dữ liệu từ form
        $username = $_POST['username'];
        $email = $_POST['email'];
        $password = $_POST['password'];
        $confirm_password = $_POST['confirm_password'];

        // Kiểm tra xem mật khẩu và xác nhận mật khẩu có khớp không
        if ($password !== $confirm_password) {
            $_SESSION['error'] = "Mật khẩu không khớp.";
            header('Location: /register');
            exit;
        }

        // Kiểm tra xem email đã tồn tại trong hệ thống chưa
        $stmt = $this->connection->prepare("SELECT id FROM users WHERE email = :email");
        $stmt->bindParam(':email', $email);
        $stmt->execute();
        if ($stmt->fetch()) {
            $_SESSION['error'] = "Email đã được sử dụng.";
            header('Location: /register');
            exit;
        }

        // Băm mật khẩu trước khi lưu vào cơ sở dữ liệu
        $hashed_password = $password;

        // Thêm người dùng mới vào cơ sở dữ liệu
        $stmt = $this->connection->prepare("INSERT INTO users (username, address, email, password, role) VALUES (:username, 'Hà Nội', :email, :password,'user')");
        $stmt->bindParam(':username', $username);
        $stmt->bindParam(':email', $email);
        $stmt->bindParam(':password', $hashed_password);

        if ($stmt->execute()) {
            $_SESSION['success'] = "Đăng ký thành công. Vui lòng đăng nhập.";
            header('Location: /');
            exit;
        } else {
            $_SESSION['error'] = "Đã xảy ra lỗi trong quá trình đăng ký. Vui lòng thử lại.";
            header('Location: /register');
            exit;
        }
    }

    public function forgotPasswordSubmit()
    {
        if (!isset($_POST['email'])) {
            $_SESSION['error'] = "Vui lòng nhập địa chỉ email.";
            header('Location: /forgot-password');
            exit;
        }

        $email = $_POST['email'];
        $stmt = $this->connection->prepare("SELECT id FROM users WHERE email = :email");
        $stmt->bindParam(':email', $email);
        $stmt->execute();
        $user = $stmt->fetch();

        if ($user) {
            // Tạo mã token và thời gian hết hạn
            $token = bin2hex(random_bytes(32));
            $expiry = date("Y-m-d H:i:s", strtotime('+1 hour'));

            // Cập nhật token vào cơ sở dữ liệu
            $stmt = $this->connection->prepare("UPDATE users SET reset_token = :token, token_expiry = :expiry WHERE email = :email");
            $stmt->bindParam(':token', $token);
            $stmt->bindParam(':expiry', $expiry);
            $stmt->bindParam(':email', $email);
            $stmt->execute();

            // Gửi email chứa liên kết đặt lại mật khẩu
            $resetLink = "http://localhost:8000/reset-password?token=$token";
            $subject = "Yêu cầu đặt lại mật khẩu";
            $message = "Xin chào,\n\nBạn đã yêu cầu đặt lại mật khẩu. Vui lòng nhấp vào liên kết sau để đặt lại mật khẩu của bạn:\n$resetLink\n\nLiên kết này sẽ hết hạn trong 1 giờ.\n\nNếu bạn không yêu cầu điều này, vui lòng bỏ qua email này.";

            // Sử dụng PHPMailer để gửi email
            $mail = new PHPMailer(true);

            try {
                $mail->SMTPDebug = 3; // Bật debug chi tiết
                $mail->Debugoutput = function ($str, $level) {
                    echo "Debug level $level; message: $str<br>";
                };
                $mail->isSMTP();
                $mail->Host = 'smtp.gmail.com';
                $mail->SMTPAuth = true;
                $mail->Username = 'abcnam26@gmail.com';
                $mail->Password = 'jojgxsvdicvpjtpu';
                $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
                $mail->Port = 587;

                $mail->setFrom('no-reply@yourwebsite.com', 'Zing Mp3');
                $mail->addAddress($email);

                $mail->isHTML(false);
                $mail->Subject = $subject;
                $mail->Body = $message;

                // Thực hiện gửi email
                $mail->send();
                $_SESSION['success'] = "Email đặt lại mật khẩu đã được gửi.";
                header('Location: /forgot-password');
                exit;
            } catch (Exception $e) {
                $_SESSION['error'] = "Không thể gửi email. Lỗi: {$mail->ErrorInfo}";
                header('Location: /forgot-password');
                exit;
            }
        } else {
            $_SESSION['error'] = "Email không tồn tại.";
            header('Location: /forgot-password');
            exit;
        }
    }



    public function resetPasswordSubmit()
    {
        if (!isset($_POST['token'], $_POST['password'], $_POST['confirm_password'])) {
            $_SESSION['error'] = "Vui lòng điền đầy đủ thông tin.";
            header('Location: /reset-password');
            exit;
        }

        $token = $_POST['token'];
        $password = $_POST['password'];
        $confirm_password = $_POST['confirm_password'];

        if ($password !== $confirm_password) {
            $_SESSION['error'] = "Mật khẩu không khớp.";
            header('Location: /reset-password?token=' . $token);
            exit;
        }

        // Lấy người dùng từ token và kiểm tra thời gian hết hạn
        $stmt = $this->connection->prepare("SELECT id FROM users WHERE reset_token = :token");
        $stmt->bindParam(':token', $token);
        $stmt->execute();
        $user = $stmt->fetch();

        if ($user) {
            // Cập nhật mật khẩu mới và xóa token
            $hashed_password = $password;
            $stmt = $this->connection->prepare("UPDATE users SET password = :password, reset_token = NULL, token_expiry = NULL WHERE id = :id");
            $stmt->bindParam(':password', $hashed_password);
            $stmt->bindParam(':id', $user['id']);
            $stmt->execute();

            $_SESSION['success'] = "Mật khẩu của bạn đã được đặt lại thành công. Vui lòng đăng nhập.";
            header('Location: /');
        } else {
            $_SESSION['error'] = "Liên kết đặt lại mật khẩu không hợp lệ hoặc đã hết hạn.";
            header('Location: /reset-password');
        }
    }


    public function forgotPassword()
    {
        return View::make('forgetpassword');
    }

    public function resetPassword()
    {
        if (!isset($_GET['token'])) {
            $_SESSION['error'] = "Yêu cầu không hợp lệ.";
            header('Location: /forgot-password');
            exit;
        }
        $token = $_GET['token'];
        return View::make('resetpassword', ['token' => $token]);
    }
}
