<?php
ob_start(); // Bật bộ đệm đầu ra

// Kiểm tra xem người dùng đã đăng nhập chưa
if (!isset($_SESSION['user_id'])) {
    $isLoggedIn = false;
} else {
    $isLoggedIn = true;
    // Lấy thông tin người dùng từ phiên
    $user_id = $_SESSION['user_id'];

    // Chuẩn bị câu lệnh SQL để lấy thông tin sinh viên
    $sql = "SELECT * FROM students WHERE student_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->execute([$user_id]);

    // Lấy kết quả thông tin sinh viên
    $studentData = $stmt->fetchObject();
    $stmt->closeCursor();  // Đóng kết quả của truy vấn trước

    if ($studentData) {
        $greeting = htmlspecialchars($studentData->lastname) . " " . htmlspecialchars($studentData->firstname);
    } else {
        $greeting = "Thông tin sinh viên không tìm thấy.";
    }
}
?>

<nav class="navbar navbar-expand-lg navbar-light bg-light border-bottom">
    <div class="container-fluid">
        <a href="<?php echo $basePath; ?>index.php" class="navbar-brand">
            <h2><i class="bi bi-journal-medical">TLT</i></h2>
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav me-auto">
                <li class="nav-item">
                    <a class="nav-link" href="<?php echo $basePath; ?>index.php">Trang Chủ</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="<?php echo $basePath; ?>Class/class_manage.php">Lớp Học</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="<?php echo $basePath; ?>Schedule/schedule_view.php">Lịch Học</a>
                </li>
            </ul>

            <ul class="navbar-nav ms-auto">
                <!-- Nút tham gia Lớp -->
                <li class="nav-item">
                    <button class="btn btn-success" data-bs-toggle="modal" data-bs-target="#joinClassModal" style="margin-right: 10px;">Tham gia lớp học</button>
                </li>

                <?php if ($isLoggedIn): ?>
                    <li class="nav-item">
                        <div class="btn-group">
                            <button type="button" class="btn btn-secondary dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
                                <i class="bi bi-person-badge-fill"></i>
                                <?php echo $greeting; ?>
                            </button>
                            <ul class="dropdown-menu dropdown-menu-end">
                                <li><a class="dropdown-item" href="<?php echo $basePath; ?>Information/information.php">Thông tin cá nhân</a></li>
                                <li>
                                    <hr class="dropdown-divider">
                                </li>
                                <li><a class="dropdown-item" href="<?php echo $basePath; ?>../Account/change-password.php">Đổi mật khẩu</a></li>
                                <li>
                                    <hr class="dropdown-divider">
                                </li>
                                <li><a class="dropdown-item" href="<?php echo $basePath; ?>../Account/logout.php">Đăng xuất</a></li>
                            </ul>
                        </div>
                    </li>
                <?php else: ?>
                    <li class="nav-item">
                        <a class="btn btn-primary" href="<?php echo $basePath; ?>login_view.php">Đăng nhập</a>
                    </li>
                <?php endif; ?>
            </ul>
        </div>
    </div>
</nav>

<!-- Modal Nhập Mã Lớp Học -->
<div class="modal fade" id="joinClassModal" tabindex="-1" aria-labelledby="joinClassModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="joinClassModalLabel">Tham gia lớp học</h5>
            </div>

            <div class="modal-body">
                <!-- Phần này hiển thị thông báo lỗi hoặc thành công -->
                <div id="joinClassMessage" class="alert d-none"></div>

                <form id="joinClassForm">
                    <div class="mb-3">
                        <input type="text" class="form-control" id="classId" name="class_id" placeholder="Mã lớp" required>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Đóng</button>
                        <button type="submit" class="btn btn-primary">Tham gia</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>


<script>
    document.getElementById("joinClassForm").addEventListener("submit", function(event) {
        event.preventDefault(); // Ngăn chặn gửi form theo cách thông thường

        // Lấy dữ liệu class_id từ input
        const classId = document.getElementById("classId").value;
        const joinClassMessage = document.getElementById("joinClassMessage");

        // Gửi yêu cầu AJAX tới join_class.php
        fetch("<?php echo $basePath; ?>Class/join_class.php", {
                method: "POST",
                headers: {
                    "Content-Type": "application/x-www-form-urlencoded"
                },
                body: `class_id=${encodeURIComponent(classId)}`
            })
            .then(response => response.text())
            .then(data => {
                joinClassMessage.classList.remove("d-none");
                if (data.includes("thành công")) {
                    joinClassMessage.classList.add("alert-success");
                    joinClassMessage.classList.remove("alert-danger");
                    joinClassMessage.innerText = data;
                    // Reset form sau khi tham gia thành công
                    document.getElementById("joinClassForm").reset();
                } else {
                    joinClassMessage.classList.add("alert-danger");
                    joinClassMessage.classList.remove("alert-success");
                    joinClassMessage.innerText = data;
                }
            })
            .catch(error => {
                joinClassMessage.classList.remove("d-none");
                joinClassMessage.classList.add("alert-danger");
                joinClassMessage.classList.remove("alert-success");
                joinClassMessage.innerText = "Có lỗi xảy ra. Vui lòng thử lại.";
            });
    });

    // Làm mới trang khi đóng modal nếu có thông báo
    document.getElementById("joinClassModal").addEventListener("hidden.bs.modal", function() {
        const joinClassMessage = document.getElementById("joinClassMessage");
        if (!joinClassMessage.classList.contains("d-none")) {
            location.reload();
        }
    });
</script>




<style>
    nav .dropdown-toggle::after {
        display: none;
        /* Ẩn mũi tên mặc định của Bootstrap */
    }
</style>