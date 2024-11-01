<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="">
    <meta name="author" content="">
    <title>SB Admin 2 - Register</title>
    <link href="/assets/admin/vendor/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css">
    <link href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i" rel="stylesheet">
    <link href="/assets/admin/css/sb-admin-2.min.css" rel="stylesheet">
</head>

<body class="bg-gradient-primary">
    <div class="container">
        <div class="row justify-content-center">

            <div class="col-xl-10 col-lg-12 col-md-9">

                <div class="card o-hidden border-0 shadow-lg my-5">
                    <div class="card-body p-0">
                        <!-- Nested Row within Card Body -->
                        <div class="row">
                            <div class="col-lg-6 d-none d-lg-block bg-password-image"></div>
                            <div class="col-lg-6">
                                <div class="p-5">
                                    <div class="text-center">
                                        <h1 class="h4 text-gray-900 mb-2">Bạn quên mật khẩu?</h1>
                                        <p class="mb-4">Chúng tôi hiểu rồi, mọi thứ sẽ xảy ra. Chỉ cần nhập địa chỉ email của bạn dưới đây và chúng tôi sẽ gửi cho bạn một liên kết để đặt lại mật khẩu của bạn!</p>
                                    </div>
                                    <form class="user" action="/forgot-password-submit" method="POST">
                                        <div class="form-group">
                                            <input type="email" class="form-control form-control-user"
                                                name="email"
                                                id="exampleInputEmail" aria-describedby="emailHelp"
                                                placeholder="Nhập Username...">
                                        </div>
                                        <button type="submit" class="btn btn-primary btn-user btn-block">Đặt lại mật khẩu</button>
                                        <?php
                                        // Hiển thị thông báo lỗi
                                        if (isset($_SESSION['error'])) {
                                            echo '<p style="color: red;">' . $_SESSION['error'] . '</p>';
                                            unset($_SESSION['error']); // Xóa thông báo sau khi hiển thị
                                        }

                                        // Hiển thị thông báo thành công
                                        if (isset($_SESSION['success'])) {
                                            echo '<p style="color: green;">' . $_SESSION['success'] . '</p>';
                                            unset($_SESSION['success']); // Xóa thông báo sau khi hiển thị
                                        }
                                        ?>
                                    </form>
                                    <hr>
                                    <div class="text-center">
                                        <a class="small" href="/register">Tạo tài khoản!</a>
                                    </div>
                                    <div class="text-center">
                                        <a class="small" href="/">Đã có tài khoản? Đăng nhập!</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            </div>

        </div>
    </div>
    <script src="/assets/admin/jquery/jquery.min.js"></script>
    <script src="/assets/admin/bootstrap/js/bootstrap.bundle.min.js"></script>
    <script src="/assets/admin/jquery-easing/jquery.easing.min.js"></script>
    <script src="/assets/admin/js/sb-admin-2.min.js"></script>
</body>

</html>