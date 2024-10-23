<?php
session_start();

$basePath = '../'; // Đường dẫn gốc
include __DIR__ . '/../../Connect/connect.php';
include __DIR__ . '/../../LayoutPages/navbar.php';
include __DIR__ . '/../../Account/islogin.php';


$sql_semesters = "CALL GetAllSemesters()"; // Gọi thủ tục
$stmt_semesters = $conn->prepare($sql_semesters);
$stmt_semesters->execute();
$semesters = $stmt_semesters->fetchAll(PDO::FETCH_ASSOC);
$stmt_semesters->closeCursor(); // Đóng kết quả của truy vấn trước

// Lấy semester_id của học kỳ đầu tiên trong danh sách
$defaultSemesterId = !empty($semesters) ? $semesters[0]['semester_id'] : null;
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Trang giáo viên</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-icons/1.10.0/font/bootstrap-icons.min.css">
</head>
<body>

    <div class="container mt-4">
        <!-- Tiêu đề -->
        <h2 class="mb-4 text-center">Quản lý danh sách lớp học</h2>

        <!-- Form chọn học kỳ -->
        <form id="semesterForm" class="d-flex justify-content-between align-items-center mb-3">
            <div class="mb-0 me-2" style="flex: 1;">
                <select class="form-select" id="semester" name="semester_id" required>
                    <option value="" disabled selected>Chọn học kỳ</option>
                    <?php foreach ($semesters as $semester): ?>
                        <option value="<?php echo $semester['semester_id']; ?>" <?php echo $semester['semester_id'] == $defaultSemesterId ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($semester['semester_name']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div> 
            <a href="<?php echo $basePath; ?>Semester/semester_create.php" class="btn btn-success d-flex align-items-center" style="height: 100%;">
                <i class="bi bi-plus-lg fs-5" ></i>
            </a>
        </form>


        <!-- Bảng lớp học -->
        <div id="classList" class="mt-4">
            <!-- Danh sách lớp sẽ được tải ở đây -->
        </div> 
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        $(document).ready(function() {
            // Tự động gọi AJAX khi trang được tải
            var semesterId = $('#semester').val() || "<?php echo $defaultSemesterId; ?>"; // Lấy semester_id đã chọn hoặc mặc định
            if (semesterId) {
                $.ajax({
                    url: 'get_classes.php', // URL đến file xử lý AJAX
                    type: 'POST',
                    data: { semester_id: semesterId },
                    success: function(data) {
                        $('#classList').html(data); // Hiển thị danh sách lớp học
                    },
                    error: function() {
                        $('#classList').html('<div class="alert alert-danger">Có lỗi xảy ra khi tải dữ liệu.</div>');
                    }
                });
            }

            // Gọi AJAX khi học kỳ thay đổi
            $('#semester').change(function() {
                var semesterId = $(this).val();
                if (semesterId) {
                    $.ajax({
                        url: 'get_classes.php', // URL đến file xử lý AJAX
                        type: 'POST',
                        data: { semester_id: semesterId },
                        success: function(data) {
                            $('#classList').html(data); // Hiển thị danh sách lớp học
                        },
                        error: function() {
                            $('#classList').html('<div class="alert alert-danger">Có lỗi xảy ra khi tải dữ liệu.</div>');
                        }
                    });
                } else {
                    $('#classList').empty(); // Xóa danh sách lớp học nếu không có học kỳ được chọn
                }
            });
        });

        // Tải danh sách lớp học
        function loadClasses(semesterId) {
            $.ajax({
                url: 'get_classes.php', // URL đến file xử lý AJAX
                type: 'POST',
                data: { semester_id: semesterId },
                success: function(data) {
                    $('#classList').html(data); // Hiển thị danh sách lớp học
                },
                error: function() {
                    $('#classList').html('<div class="alert alert-danger">Có lỗi xảy ra khi tải dữ liệu.</div>');
                }
            });
        }

        // Xử lý sự kiện nhấp vào nút "Hủy"
        $(document).on('click', '.btn-cancel', function(e) {
            e.preventDefault(); // Ngăn chặn hành động mặc định
            var classId = $(this).data('class-id'); // Lấy class_id từ thuộc tính data
            if (confirm('Bạn có chắc chắn muốn hủy lớp này không?')) {
                $.ajax({
                    url: 'delete_class.php', // Đường dẫn đến tệp xử lý
                    type: 'POST',
                    data: { class_id: classId }, // Gửi class_id
                    success: function(response) {
                        console.log(response); // Ghi lại phản hồi để kiểm tra
                        if (response.success) {
                            alert('Lớp đã được xóa thành công.');
                            $('#semester').change(); // Gọi lại sự kiện change để tải lại lớp
                        } else {
                            alert('Có lỗi xảy ra: ' + response.message);
                        }
                    },
                    error: function() {
                        alert('Có lỗi xảy ra khi xóa lớp.');
                    }
                });
            }
        });
    </script>
</body>
</html>
