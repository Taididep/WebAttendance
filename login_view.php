<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đăng Nhập / Đăng Ký</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-icons/1.10.0/font/bootstrap-icons.min.css">
    <style>
        .input-group-text {
            cursor: pointer;
        }
    </style>
</head>

<body>
    <div class="container" style="margin-top: 100px;">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card">
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
                                            <span class="input-group-text" id="togglePassword">
                                                <i class="bi bi-eye-slash" id="togglePasswordIcon"></i>
                                            </span>
                                        </div>
                                    </div>
                                </div>
                                <button type="submit" class="btn btn-primary btn-block">Đăng Nhập</button>
                                <p class="form-toggle text-center mt-3" id="toggle-to-register">Bạn chưa có tài khoản? Đăng ký</p>
                            </form>
                        </div>

                        <div id="register-form" style="display:none;">
                            <form action="Account/register.php" method="post">
                                <div class="form-group">
                                    <label for="student_id">Mã số sinh viên:</label>
                                    <input type="text" class="form-control" name="student_id" id="student_id" required>
                                </div>
                                <div class="form-group">
                                    <label for="new_username">Username:</label>
                                    <input type="text" class="form-control" name="username" id="new_username" required>
                                </div>
                                <div class="form-group">
                                    <label for="new_password">Password:</label>
                                    <div class="input-group">
                                        <input type="password" class="form-control" name="password" id="new_password" required>
                                        <div class="input-group-append">
                                            <span class="input-group-text" id="toggleNewPassword">
                                                <i class="bi bi-eye-slash" id="toggleNewPasswordIcon"></i>
                                            </span>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label for="lastname">Họ:</label>
                                    <input type="text" class="form-control" name="lastname" id="lastname" required>
                                </div>
                                <div class="form-group">
                                    <label for="firstname">Tên:</label>
                                    <input type="text" class="form-control" name="firstname" id="firstname" required>
                                </div>
                                <div class="form-group">
                                    <label for="email">Email:</label>
                                    <input type="email" class="form-control" name="email" id="email" required>
                                </div>
                                <div class="form-group">
                                    <label for="phone">Điện thoại:</label>
                                    <input type="text" class="form-control" name="phone" id="phone" required>
                                </div>
                                <div class="form-group">
                                    <label for="class">Lớp:</label>
                                    <input type="text" class="form-control" name="class" id="class" required>
                                </div>
                                <div class="form-group">
                                    <label for="birthday">Ngày sinh:</label>
                                    <input type="date" class="form-control" name="birthday" id="birthday" required>
                                </div>
                                <div class="form-group">
                                    <label>Giới tính:</label>
                                    <div>
                                        <label class="mr-2">
                                            <input type="radio" name="gender" value="Nam" required> Nam
                                        </label>
                                        <label>
                                            <input type="radio" name="gender" value="Nữ"> Nữ
                                        </label>
                                    </div>
                                </div>
                                <button type="submit" class="btn btn-primary btn-block">Đăng Ký</button>
                                <p class="form-toggle text-center mt-3" id="toggle-to-login">Bạn đã có tài khoản? Đăng nhập</p>
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
        // Chuyển đổi giữa form đăng nhập và đăng ký
        document.getElementById('toggle-to-register').addEventListener('click', function () {
            document.getElementById('login-form').style.display = 'none';
            document.getElementById('register-form').style.display = 'block';
            document.getElementById('form-title').innerText = 'Đăng Ký';
        });

        document.getElementById('toggle-to-login').addEventListener('click', function () {
            document.getElementById('register-form').style.display = 'none';
            document.getElementById('login-form').style.display = 'block';
            document.getElementById('form-title').innerText = 'Đăng Nhập';
        });

        // Hiển thị modal thông báo
        $(document).ready(function() {
            <?php if (isset($_SESSION['success_message'])): ?>
                $('#successModal').modal('show');
                setTimeout(function() {
                    $('#successModal').modal('hide');
                }, 2000); // Tự động tắt sau 2 giây
            <?php endif; ?>
        });

        // Toggle password visibility for login form
        document.getElementById('togglePassword').addEventListener('click', function () {
            const passwordInput = document.getElementById('password');
            const passwordIcon = document.getElementById('togglePasswordIcon');

            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                passwordIcon.classList.remove('bi-eye-slash');
                passwordIcon.classList.add('bi-eye');
            } else {
                passwordInput.type = 'password';
                passwordIcon.classList.remove('bi-eye');
                passwordIcon.classList.add('bi-eye-slash');
            }
        });

        // Toggle password visibility for register form
        document.getElementById('toggleNewPassword').addEventListener('click', function () {
            const newPasswordInput = document.getElementById('new_password');
            const newPasswordIcon = document.getElementById('toggleNewPasswordIcon');

            if (newPasswordInput.type === 'password') {
                newPasswordInput.type = 'text';
                newPasswordIcon.classList.remove('bi-eye-slash');
                newPasswordIcon.classList.add('bi-eye');
            } else {
                newPasswordInput.type = 'password';
                newPasswordIcon.classList.remove('bi-eye');
                newPasswordIcon.classList.add('bi-eye-slash');
            }
        });
    </script>

</body>

</html>
