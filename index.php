<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đăng Nhập / Đăng Ký</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>

<body>
    <div class="container">
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
                                    <input type="password" class="form-control" name="password" id="password" required>
                                </div>
                                <button type="submit" class="btn btn-primary btn-block">Đăng Nhập</button>
                                <p class="form-toggle" id="toggle-to-register">Bạn chưa có tài khoản? Đăng ký</p>
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
                                    <input type="password" class="form-control" name="password" id="new_password" required>
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
                                <p class="form-toggle" id="toggle-to-login">Bạn đã có tài khoản? Đăng nhập</p>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal thông báo -->
    <div class="modal fade" id="successModal" tabindex="-1" role="dialog" aria-labelledby="successModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="successModalLabel">Thông báo</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <?php if (isset($_SESSION['success_message'])): ?>
                        <?php echo $_SESSION['success_message']; ?>
                        <?php unset($_SESSION['success_message']); // Xóa thông báo sau khi hiển thị ?>
                    <?php endif; ?>
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
    </script>
</body>

</html>