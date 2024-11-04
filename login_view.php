<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đăng Nhập / Đăng Ký</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-icons/1.10.0/font/bootstrap-icons.min.css">
    <style>
        body {
            background: linear-gradient(to right, #4b6cb7, #182848);
            font-family: 'Montserrat', sans-serif;
        }

        .input-group-text {
            cursor: pointer;
        }
    </style>
</head>

<body>
    <div class="container" style="margin-top: 100px;">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card shadow-sm">
                    <div class="card-header">
                        <h1 class="text-center" id="form-title">Đăng Nhập</h1>
                    </div>
                    <div class="card-body">
                        <div id="login-form">
                            <form action="Account/login.php" method="post">
                                <div class="form-group">
                                    <label for="username">Username:</label>
                                    <input type="text" class="form-control" name="username" id="username" required>
                                </div>
                                <div class="form-group">
                                    <label for="password">Password:</label>
                                    <div class="input-group">
                                        <input type="password" class="form-control" name="password" id="password" required>
                                        <div class="input-group-append">
                                            <span class="input-group-text toggle-password" data-target="#password">
                                                <i class="bi bi-eye-slash"></i>
                                            </span>
                                        </div>
                                    </div>
                                </div>
                                <button type="submit" class="btn btn-primary btn-block">Đăng Nhập</button>
                                <p class="form-toggle text-center mt-3" id="toggle-to-register">Bạn chưa có tài khoản? Đăng ký</p>
                                <p class="form-toggle text-center mt-3" id="toggle-to-forgot-password">Quên mật khẩu?</p>
                            </form>
                        </div>

                        <div id="register-form" style="display:none;">
                            <form action="Account/register.php" method="post">
                                <!-- Các trường trong form đăng ký -->
                                <button type="submit" class="btn btn-primary btn-block">Đăng Ký</button>
                                <p class="form-toggle text-center mt-3" id="toggle-to-login">Bạn đã có tài khoản? Đăng nhập</p>
                            </form>
                        </div>

                        <div id="forgot-password-form" style="display:none;">
                            <form action="Account/reset-password.php" method="post">
                                <div class="form-group">
                                    <label for="forgot-username">Username:</label>
                                    <input type="text" class="form-control" name="username" id="forgot-username" required>
                                </div>
                                <div class="form-group">
                                    <label for="forgot-password-email">Email:</label>
                                    <input type="email" class="form-control" name="email" id="forgot-password-email" required>
                                </div>
                                <button type="submit" class="btn btn-primary btn-block">Gửi yêu cầu đặt lại mật khẩu</button>
                                <p class="form-toggle text-center mt-3" id="toggle-to-login-from-forgot">Quay lại đăng nhập</p>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.3/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <script>
        $(document).ready(function() {
            // Chuyển đổi giữa các form đăng nhập, đăng ký và quên mật khẩu
            $('#toggle-to-register').click(function() {
                $('#login-form').hide();
                $('#forgot-password-form').hide();
                $('#register-form').show();
                $('#form-title').text('Đăng Ký');
            });

            $('#toggle-to-login, #toggle-to-login-from-forgot').click(function() {
                $('#register-form').hide();
                $('#forgot-password-form').hide();
                $('#login-form').show();
                $('#form-title').text('Đăng Nhập');
            });

            $('#toggle-to-forgot-password').click(function() {
                $('#login-form').hide();
                $('#register-form').hide();
                $('#forgot-password-form').show();
                $('#form-title').text('Quên Mật Khẩu');
            });

            // Toggle password visibility
            $('.toggle-password').click(function() {
                const target = $($(this).data('target'));
                const icon = $(this).find('i');
                if (target.attr('type') === 'password') {
                    target.attr('type', 'text');
                    icon.removeClass('bi-eye-slash').addClass('bi-eye');
                } else {
                    target.attr('type', 'password');
                    icon.removeClass('bi-eye').addClass('bi-eye-slash');
                }
            });

            // Hiển thị modal thông báo nếu có
            <?php if (isset($_SESSION['success_message'])): ?>
                $('#successModal').modal('show');
                setTimeout(function() {
                    $('#successModal').modal('hide');
                }, 2000);
            <?php endif; ?>
        });
    </script>
</body>

</html>