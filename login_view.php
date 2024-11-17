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
                                <p class="text-center mt-3"><a href="Account/register.php">Bạn chưa có tài khoản? Đăng ký</a></p>
                                <p class="text-center mt-3"><a href="Account/forgot-pass.php" class="form-toggle text-center mt-3" id="toggle-to-forgot-password">Quên mật khẩu?</a>  </p>
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