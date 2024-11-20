<?php
session_start();

$basePath = '../'; // Đường dẫn gốc
include __DIR__ . '/../../Connect/connect.php';
include __DIR__ . '/../../LayoutPages/navbar_admin.php';
include __DIR__ . '/../../Account/islogin.php';

$sql_semesters = "CALL GetAllSemesters()"; // Gọi thủ tục để lấy danh sách học kỳ
$stmt_semesters = $conn->prepare($sql_semesters);
$stmt_semesters->execute();
$semesters = $stmt_semesters->fetchAll(PDO::FETCH_ASSOC);
$stmt_semesters->closeCursor(); // Đóng kết quả của truy vấn trước

?>

<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quản lý học kỳ</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <link rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-icons/1.10.0/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="../Css/semester_manage.css">
</head>

<body>

    <div class="container">
        <div class="card p-4">
            <!-- Title -->
            <h2 class="mb-2 text-center">Danh sách học kỳ</h2>
            <hr>

            <!-- Sử dụng d-flex để hiển thị thanh tìm kiếm và nút thêm học kỳ cạnh nhau -->
            <div class="d-flex justify-content-between mb-3">
                <div class="flex-grow-1">
                    <!-- Thanh tìm kiếm -->
                    <input type="text" id="searchInput" class="form-control" placeholder="Tìm kiếm học kỳ..." />
                </div>
                <div>
                    <!-- Nút thêm học kỳ -->
                    <a href="#" class="btn btn-primary ms-2" data-bs-toggle="modal"
                        data-bs-target="#createSemesterModal">
                        <i class="bi bi-plus-circle"></i> Thêm học kỳ
                    </a>
                </div>
            </div>

            <!-- Semester List Table -->
            <div id="SemesterList" class="semester-table">

                <!-- Bảng học kỳ -->
                <table class="semester-table">
                    <thead>
                        <tr>
                            <th>STT</th>
                            <th>Tên học kỳ</th>
                            <th>Ngày bắt đầu</th>
                            <th>Ngày kết thúc</th>
                            <th>Trạng thái</th>
                            <th style="width: 1%;"></th>
                        </tr>
                    </thead>
                    <tbody id="semesterTableBody">
                        <?php
                        $stt = 1; // Khởi tạo số thứ tự
                        foreach ($semesters as $semester): ?>
                            <tr>
                                <td style="padding-left: 25px;"><?php echo $stt++; ?></td>
                                <!-- Hiển thị số thứ tự -->
                                <td><?php echo htmlspecialchars($semester['semester_name']); ?></td>
                                <td><?php echo htmlspecialchars($semester['start_date']); ?></td>
                                <td><?php echo htmlspecialchars($semester['end_date']); ?></td>
                                <td>
                                    <?php echo $semester['is_active'] == 1 ? 'Hoạt động' : 'Không hoạt động'; ?>
                                </td>
                                <td>
                                    <div class="dropdown">
                                        <button class="btn btn-link dropdown-toggle" type="button" id="dropdownMenuButton"
                                            data-bs-toggle="dropdown" aria-expanded="false" style="color: black;">
                                            <i class="bi bi-three-dots-vertical"></i>
                                        </button>
                                        <ul class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                                            <li><a class="dropdown-item"
                                                    href="semester_edit.php?semester_id=<?php echo $semester['semester_id']; ?>">Sửa</a>
                                            </li>
                                            <li><a class="dropdown-item"
                                                    href="semester_delete.php?semester_id=<?php echo $semester['semester_id']; ?>"
                                                    onclick="return confirm('Bạn có chắc chắn muốn xóa học kỳ này không?')">Xóa</a>
                                            </li>
                                        </ul>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Modal Thêm học kỳ -->
    <div class="modal fade" id="createSemesterModal" tabindex="-1" aria-labelledby="createSemesterModalLabel"
        aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="createSemesterModalLabel">Thêm học kỳ</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <!-- Form thêm học kỳ -->
                    <form id="createSemesterForm" method="POST" action="create_semester.php">
                        <div class="mb-3">
                            <label for="semester_name" class="form-label">Tên học kỳ</label>
                            <input type="text" class="form-control" id="semester_name" name="semester_name" required>
                        </div>
                        <div class="mb-3">
                            <label for="start_date" class="form-label">Ngày bắt đầu</label>
                            <input type="date" class="form-control" id="start_date" name="start_date" required>
                        </div>
                        <div class="mb-3">
                            <label for="end_date" class="form-label">Ngày kết thúc</label>
                            <input type="date" class="form-control" id="end_date" name="end_date" required>
                        </div>
                        <div class="mb-3 form-check">
                            <input type="checkbox" class="form-check-input" id="is_active" name="is_active">
                            <label class="form-check-label" for="is_active">Trạng thái hoạt động</label>
                        </div>
                        <button type="submit" class="btn btn-primary">Thêm học kỳ</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
    <script src="../JavaScript/semester_manage.js"></script>

</body>

</html>