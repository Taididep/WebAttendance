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
            background: url('Image/Index.jpg') no-repeat center center fixed;   
            background-size: cover;   
        }  

        .login-container {  
            background-color: rgba(255, 255, 255, 0.8);
            padding: 30px;  
            border-radius: 10px;  
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);  
            margin-top: 100px; 
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
                            <!-- Tab điều hướng giữa giáo viên và sinh viên -->
                            <ul class="nav nav-tabs" id="register-tab" role="tablist">
                                <li class="nav-item">
                                    <a class="nav-link active" id="teacher-tab" data-toggle="tab" href="#teacher" role="tab">Đăng ký Giáo viên</a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" id="student-tab" data-toggle="tab" href="#student" role="tab">Đăng ký Sinh viên</a>
                                </li>
                            </ul>

                            <!-- Nội dung của các tab -->
                            <div class="tab-content" id="register-tab-content">
                                <!-- Form đăng ký Giáo viên -->
                                <div class="tab-pane fade show active" id="teacher" role="tabpanel">
                                    <form action="Account/register_teacher.php" method="post">
                                        <div class="form-group">
                                            <label for="teacher_id">Mã số giáo viên:</label>
                                            <input type="text" class="form-control" name="teacher_id" id="teacher_id" required>
                                        </div>
                                        <div class="form-group">
                                            <label for="teacher_username">Username:</label>
                                            <input type="text" class="form-control" name="username" id="teacher_username" required>
                                        </div>
                                        <div class="form-group">
                                            <label for="teacher_password">Password:</label>
                                            <input type="password" class="form-control" name="password" id="teacher_password" required>
                                        </div>
                                        <div class="form-group">
                                            <label for="teacher_lastname">Họ:</label>
                                            <input type="text" class="form-control" name="lastname" id="teacher_lastname" required>
                                        </div>
                                        <div class="form-group">
                                            <label for="teacher_firstname">Tên:</label>
                                            <input type="text" class="form-control" name="firstname" id="teacher_firstname" required>
                                        </div>
                                        <div class="form-group">
                                            <label for="teacher_email">Email:</label>
                                            <input type="email" class="form-control" name="email" id="teacher_email" required>
                                        </div>
                                        <div class="form-group">
                                            <label for="teacher_phone">Điện thoại:</label>
                                            <input type="text" class="form-control" name="phone" id="teacher_phone" required>
                                        </div>
                                        <div class="form-group">
                                            <label for="teacher_birthday">Ngày sinh:</label>
                                            <input type="date" class="form-control" name="birthday" id="teacher_birthday" required>
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
                                        <button type="submit" class="btn btn-primary btn-block">Đăng Ký Giáo viên</button>
                                        <p class="form-toggle text-center mt-3" id="toggle-to-login-from-forgot">Quay lại đăng nhập</p>  
                                    </form>
                                </div>

                                <!-- Form đăng ký Sinh viên -->
                                <div class="tab-pane fade" id="student" role="tabpanel">
                                    <form action="Account/register_student.php" method="post">
                                        <div class="form-group">
                                            <label for="student_id">Mã số sinh viên:</label>
                                            <input type="text" class="form-control" name="student_id" id="student_id" required>
                                        </div>
                                        <div class="form-group">
                                            <label for="student_username">Username:</label>
                                            <input type="text" class="form-control" name="username" id="student_username" required>
                                        </div>
                                        <div class="form-group">
                                            <label for="student_password">Password:</label>
                                            <input type="password" class="form-control" name="password" id="student_password" required>
                                        </div>
                                        <div class="form-group">
                                            <label for="student_lastname">Họ:</label>
                                            <input type="text" class="form-control" name="lastname" id="student_lastname" required>
                                        </div>
                                        <div class="form-group">
                                            <label for="student_firstname">Tên:</label>
                                            <input type="text" class="form-control" name="firstname" id="student_firstname" required>
                                        </div>
                                        <div class="form-group">
                                            <label for="student_email">Email:</label>
                                            <input type="email" class="form-control" name="email" id="student_email" required>
                                        </div>
                                        <div class="form-group">
                                            <label for="student_class">Lớp:</label>
                                            <input type="text" class="form-control" name="class" id="student_class" required>
                                        </div>
                                        <div class="form-group">
                                            <label for="student_phone">Điện thoại:</label>
                                            <input type="text" class="form-control" name="phone" id="student_phone" required>
                                        </div>
                                        <div class="form-group">
                                            <label for="student_birthday">Ngày sinh:</label>
                                            <input type="date" class="form-control" name="birthday" id="student_birthday" required>
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
                                        <button type="submit" class="btn btn-primary btn-block">Đăng Ký Sinh viên</button>
                                        <p class="form-toggle text-center mt-3" id="toggle-to-login-from-forgot">Quay lại đăng nhập</p>  
                                    </form>
                                </div>
                            </div>

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
        $(document).ready(function () {  
            // Chuyển đổi giữa các form đăng nhập, đăng ký và quên mật khẩu  
            $('#toggle-to-register').click(function () {  
                $('#login-form').hide();  
                $('#forgot-password-form').hide();  
                $('#register-form').show();  
                $('#form-title').text('Đăng Ký');  
            });  

            $('#toggle-to-login, #toggle-to-login-from-forgot, #toggle-to-login-teacher').click(function () {  
                $('#register-form').hide();  
                $('#forgot-password-form').hide();  
                $('#login-form').show();  
                $('#form-title').text('Đăng Nhập');  
            });  

            $('#toggle-to-forgot-password').click(function () {  
                $('#login-form').hide();  
                $('#register-form').hide();  
                $('#forgot-password-form').show();  
                $('#form-title').text('Quên Mật Khẩu');  
            });  

            // Toggle password visibility  
            $('.toggle-password').click(function () {  
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
        });  
    </script>  
</body>  
</html>