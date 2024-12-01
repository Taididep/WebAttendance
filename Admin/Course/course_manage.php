<?php
session_start();

$basePath = '../'; // Đường dẫn gốc
include __DIR__ . '/../../Connect/connect.php';
include __DIR__ . '/../../LayoutPages/navbar_admin.php';
include __DIR__ . '/../../Account/islogin.php';

// Số bản ghi mỗi trang
$records_per_page = 10;

// Trang hiện tại (mặc định là 1)
$page = isset($_GET['page']) && is_numeric($_GET['page']) ? (int)$_GET['page'] : 1;

// Tính toán OFFSET
$offset = ($page - 1) * $records_per_page;

// Truy vấn tổng số khóa học
$stmt = $conn->prepare("CALL GetTotalCoursesCount(@total)");
$stmt->execute();
$result = $conn->query("SELECT @total AS total");
$total_records = $result->fetch(PDO::FETCH_ASSOC)['total'];

// Tính tổng số trang
$total_pages = ceil($total_records / $records_per_page);

// Truy vấn danh sách khóa học với giới hạn số lượng
$stmt_courses = $conn->prepare("CALL GetCourses(:limit, :offset)");
$stmt_courses->bindValue(':limit', $records_per_page, PDO::PARAM_INT);
$stmt_courses->bindValue(':offset', $offset, PDO::PARAM_INT);

try {
    // Execute the procedure
    $stmt_courses->execute();
    $courses = $stmt_courses->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    echo "Lỗi khi lấy danh sách khóa học: " . $e->getMessage();
}

// Close the cursor
$stmt_courses->closeCursor();
?>

<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quản lý khóa học</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <link rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-icons/1.10.0/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="../Css/course_manage.css">
</head>

<body>

    <div class="container">
        <div class="card p-4">
            <!-- Title -->
            <h2 class="mb-2 text-center">Danh sách môn học</h2>
            <hr>

            <!-- Thanh tìm kiếm và nút thêm khóa học -->
            <div class="d-flex justify-content-between mb-3">
                <div class="flex-grow-1">
                    <!-- Thanh tìm kiếm -->
                    <input type="text" id="searchInput" class="form-control" placeholder="Tìm kiếm khóa học..." />
                </div>
                <div>
                    <!-- Nút thêm môn học -->
                    <a href="#" class="btn btn-primary ms-2" data-bs-toggle="modal"
                        data-bs-target="#createCourseModal">
                        <i class="bi bi-plus-circle"></i> Thêm môn học
                    </a>
                </div>
            </div>

            <!-- Course List Table -->
            <div id="CourseList" class="course-table">

                <!-- Bảng khóa học -->
                <table class="table">
                    <thead>
                        <tr>
                            <th style="text-align: center;">STT</th>
                            <th style="text-align: center;">Mã môn học</th>
                            <th style="width: 30%;">Tên môn học</th>
                            <th style="width: 17%;">Loại môn học</th>
                            <th style="text-align: center;">Số tín chỉ</th>
                            <th style="text-align: center;">Số tiết LT</th>
                            <th style="text-align: center;">Số tiết TH</th>
                            <th style="width: 1%;"></th>
                        </tr>
                    </thead>
                    <tbody id="courseTableBody">
                        <?php
                        $stt = 1; // Khởi tạo số thứ tự
                        ?>
                        <?php foreach ($courses as $course): ?>
                            <tr>
                                <td style="padding-left: 25px;"><?php echo $stt++; ?></td>
                                <td style="text-align: center;"><?php echo htmlspecialchars($course['course_id']); ?></td>
                                <td><?php echo htmlspecialchars($course['course_name']); ?></td>
                                <td><?php echo htmlspecialchars($course['course_type_name']); ?></td>
                                <td style="text-align: center;"><?php echo htmlspecialchars($course['credits']); ?></td>
                                <td style="text-align: center;"><?php echo htmlspecialchars($course['theory_periods']); ?></td>
                                <td style="text-align: center;"><?php echo htmlspecialchars($course['practice_periods']); ?></td>
                                <td>
                                    <div class="dropdown">
                                        <button class="btn btn-link dropdown-toggle" type="button" id="dropdownMenuButton"
                                            data-bs-toggle="dropdown" aria-expanded="false" style="color: black;">
                                            <i class="bi bi-three-dots-vertical"></i>
                                        </button>
                                        <ul class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                                            <li>
                                                <a class="dropdown-item" href="#" data-bs-toggle="modal" data-bs-target="#editCourseModal">Sửa</a>
                                            </li>
                                            <li>
                                                <a class="dropdown-item"
                                                    href="delete_course.php?course_id=<?php echo urlencode($course['course_id']); ?>"
                                                    onclick="return confirm('Bạn có chắc chắn muốn xóa khóa học này không?')">Xóa</a>
                                            </li>
                                        </ul>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>

            </div>

            <!-- Pagination -->
            <nav aria-label="Page navigation">
                <ul class="pagination justify-content-center">
                    <?php if ($page > 1): ?>
                        <li class="page-item">
                            <a class="page-link" href="?page=<?php echo $page - 1; ?>">Trước</a>
                        </li>
                    <?php endif; ?>

                    <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                        <li class="page-item <?php echo $i == $page ? 'active' : ''; ?>">
                            <a class="page-link" href="?page=<?php echo $i; ?>"><?php echo $i; ?></a>
                        </li>
                    <?php endfor; ?>

                    <?php if ($page < $total_pages): ?>
                        <li class="page-item">
                            <a class="page-link" href="?page=<?php echo $page + 1; ?>">Tiếp</a>
                        </li>
                    <?php endif; ?>
                </ul>
            </nav>
        </div>
    </div>


    <!-- Modal Thêm môn học -->
    <div class="modal fade" id="createCourseModal" tabindex="-1" aria-labelledby="createCourseModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="createCourseModalLabel">Thêm môn học</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <!-- Form thêm môn học -->
                    <form id="createCourseForm" method="POST" action="create_course.php">
                        <div class="mb-3">
                            <label for="course_id" class="form-label">Mã môn học</label>
                            <input type="text" class="form-control" id="course_id" name="course_id" required>
                        </div>
                        <div class="mb-3">
                            <label for="course_name" class="form-label">Tên môn học</label>
                            <input type="text" class="form-control" id="course_name" name="course_name" required>
                        </div>
                        <div class="mb-3">
                            <label for="course_type_id" class="form-label">Loại môn học</label>
                            <select class="form-select" id="course_type_id" name="course_type_id" required>
                                <option value="">Chọn loại môn học</option>
                                <?php
                                // Lấy danh sách loại môn học từ cơ sở dữ liệu
                                include __DIR__ . '/../../Connect/connect.php';
                                $sql = "SELECT course_type_id, course_type_name, credits, theory_periods, practice_periods FROM course_types";
                                $stmt = $conn->prepare($sql);
                                $stmt->execute();
                                $course_types = $stmt->fetchAll(PDO::FETCH_ASSOC);

                                foreach ($course_types as $type) {
                                    // Thêm thông tin credits, theory_periods và practice_periods vào tên loại môn học trong ngoặc
                                    echo "<option value='{$type['course_type_id']}'>
                                            {$type['course_type_name']} 
                                            ({$type['credits']} tín chỉ, 
                                            {$type['theory_periods']} tiết LT, 
                                            {$type['practice_periods']} tiết TH)
                                        </option>";
                                }
                                ?>
                            </select>

                        </div>
                        <button type="submit" class="btn btn-primary">Thêm môn học</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Sửa khóa học -->
    <div class="modal fade" id="editCourseModal" tabindex="-1" aria-labelledby="editCourseModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editCourseModalLabel">Sửa khóa học</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <!-- Form sửa khóa học -->
                    <form id="editCourseForm" method="POST" action="edit_course.php?course_id=<?php echo urlencode($course['course_id']); ?>">
                        <div class="mb-3">
                            <label for="course_name" class="form-label">Tên khóa học</label>
                            <input type="text" class="form-control" id="course_name" name="course_name" value="<?php echo htmlspecialchars($course['course_name']); ?>" required>
                        </div>
                        <div class="mb-3">
                            <label for="course_type_id" class="form-label">Loại khóa học</label>
                            <select class="form-select" id="course_type_id" name="course_type_id" required>
                                <option value="">Chọn loại khóa học</option>
                                <?php
                                // Lấy tất cả loại khóa học từ cơ sở dữ liệu
                                $sql = "SELECT * FROM course_types";
                                $stmt = $conn->prepare($sql);
                                $stmt->execute();
                                $course_types = $stmt->fetchAll(PDO::FETCH_ASSOC);
                                foreach ($course_types as $type) {
                                    $selected = ($type['course_type_id'] == $course['course_type_id']) ? 'selected' : '';
                                    echo "<option value='{$type['course_type_id']}' {$selected}>{$type['course_type_name']} (Credits: {$type['credits']}, Theory: {$type['theory_periods']}, Practice: {$type['practice_periods']})</option>";
                                }
                                ?>
                            </select>
                        </div>
                        <button type="submit" class="btn btn-primary">Cập nhật khóa học</button>
                    </form>
                </div>
            </div>
        </div>
    </div>


    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
    <script src="../JavaScript/course_manage.js"></script>

</body>

</html>