<!DOCTYPE html>  
<html lang="vi">  
<head>  
    <meta charset="UTF-8">  
    <meta name="viewport" content="width=device-width, initial-scale=1.0">  
    <title>Quên Mật Khẩu</title>  
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
    </style>  
</head>  

<body>  
    <div class="container" style="margin-top: 100px;">  
        <div class="row justify-content-center">  
            <div class="col-md-6">  
                <div class="card shadow-sm">  
                    <div class="card-header">  
                        <h1 class="text-center" id="form-title">Quên Mật Khẩu</h1>  
                    </div>  
                    <div class="card-body"> 
                        <!-- Hiển thị thông báo từ Session -->
                        <?php
                        session_start(); // Khởi động session
                        
                        if (isset($_SESSION['error_message'])) {
                            echo '<div class="alert alert-danger" role="alert">'.$_SESSION['error_message'].'</div>';
                            unset($_SESSION['error_message']); // Xóa thông báo sau khi hiển thị
                        }

                        if (isset($_SESSION['success_message'])) {
                            echo '<div class="alert alert-success" role="alert">'.$_SESSION['success_message'].'</div>';
                            unset($_SESSION['success_message']); // Xóa thông báo sau khi hiển thị
                        }
                        ?> 

                        <div id="forgot-password-form">  
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
                                
                                <p align="center"><a href="login_view.php" class="form-toggle text-center mt-3">Quay lại đăng nhập</a></p>
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
</body>  
</html>
