<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đặt Lại Mật Khẩu</title>
    <link rel="stylesheet" href="/assets/admin/css/sb-admin-2.min.css">
</head>
<body class="bg-gradient-primary">
    <div class="container">
        <div class="card o-hidden border-0 shadow-lg my-5">
            <div class="card-body p-0">
                <div class="row justify-content-center">
                    <div class="col-lg-7">
                        <div class="p-5">
                            <div class="text-center">
                                <h1 class="h4 text-gray-900 mb-4">Đặt Lại Mật Khẩu</h1>
                            </div>
                            <!-- Form đặt lại mật khẩu -->
                            <form action="/reset-password-submit" method="POST" class="user">
                                <input type="hidden" name="token" value="<?php echo htmlspecialchars($_GET['token']); ?>">

                                <div class="form-group">
                                    <label for="password">Mật khẩu mới:</label>
                                    <input type="password" name="password" id="password" class="form-control form-control-user" placeholder="Nhập mật khẩu mới" required>
                                </div>
                                
                                <div class="form-group">
                                    <label for="confirm_password">Xác nhận mật khẩu mới:</label>
                                    <input type="password" name="confirm_password" id="confirm_password" class="form-control form-control-user" placeholder="Xác nhận mật khẩu mới" required>
                                </div>

                                <button type="submit" class="btn btn-primary btn-user btn-block">Đặt Lại Mật Khẩu</button>
                            </form>
                            <hr>
                            <div class="text-center">
                                <a class="small" href="/login">Quay lại đăng nhập</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script src="/assets/admin/jquery/jquery.min.js"></script>
    <script src="/assets/admin/bootstrap/js/bootstrap.bundle.min.js"></script>
</body>
</html>
