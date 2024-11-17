<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đăng Ký</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-icons/1.10.0/font/bootstrap-icons.min.css">
    <style>
        body {
            background: url('../Image/Index.jpg') no-repeat center center fixed;
            background-size: cover;
        }

        .register-container {
            background-color: rgba(255, 255, 255, 0.8);
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
            margin-top: 100px;
        }
    </style>
</head>
<body>
    <div class="container" style="margin-top: 100px;">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card shadow-sm">
                    <div class="card-header">
                        <h1 class="text-center">Đăng Ký</h1>
                    </div>
                    <div class="card-body">
                    <?php
                    session_start();
                    if (isset($_SESSION['error_message'])) {
                        echo '<div class="alert alert-danger" role="alert">'.$_SESSION['error_message'].'</div>';
                        unset($_SESSION['error_message']);
                    }

                    if (isset($_SESSION['success_message'])) {
                        echo '<div class="alert alert-success" role="alert">'.$_SESSION['success_message'].'</div>';
                        unset($_SESSION['success_message']);
                    }
                    ?>
                        <ul class="nav nav-tabs" id="register-tab" role="tablist">
                            <li class="nav-item">
                                <a class="nav-link active" id="teacher-tab" data-toggle="tab" href="#teacher" role="tab">Đăng ký Giáo viên</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" id="student-tab" data-toggle="tab" href="#student" role="tab">Đăng ký Sinh viên</a>
                            </li>
                        </ul>

                        <div class="tab-content" id="register-tab-content">
                            <!-- Đăng ký Giáo viên -->
                            <div class="tab-pane fade show active" id="teacher" role="tabpanel">
                            <form action="./register_teacher.php" method="post">
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
                                          
                                    </form>
                            </div>

                            <!-- Đăng ký Sinh viên -->
                            <div class="tab-pane fade" id="student" role="tabpanel">
                                <form action="./register_student.php" method="post">
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
                                         
                                    </form>
                            </div>
                        </div>
                        <p align="center"><a href="/login_view.php" class="form-toggle text-center mt-3" id="toggle-to-login-from-forgot">Quay lại đăng nhập</a> </p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.3/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    
</body>
</html>
