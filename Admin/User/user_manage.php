<?php
session_start();
$basePath = '../';
include __DIR__ . '/../../Connect/connect.php';
include __DIR__ . '/../../LayoutPages/navbar.php';
include __DIR__ . '/../../Account/islogin.php';

// Lấy danh sách admin
$sql = "CALL GetAllAdmins()"; // Thay bằng thủ tục của bạn
$stmt = $conn->prepare($sql);
$stmt->execute();
$admins = $stmt->fetchAll(PDO::FETCH_ASSOC);
$stmt->closeCursor();
?>

<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quản lý tài khoản</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-icons/1.10.0/font/bootstrap-icons.min.css">
</head>

<body>
    <div class="container mt-5">
        <h2>Quản lý tài khoản</h2>
        <button class="btn btn-primary mb-3" data-bs-toggle="modal" data-bs-target="#addAdminModal">
            Thêm tài khoản
        </button>

        <table class="table table-striped">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Họ</th>
                    <th>Tên</th>
                    <th>Email</th>
                    <th>Điện thoại</th>
                    <th>Hành động</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($admins as $admin): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($admin['admin_id']); ?></td>
                        <td><?php echo htmlspecialchars($admin['lastname']); ?></td>
                        <td><?php echo htmlspecialchars($admin['firstname']); ?></td>
                        <td><?php echo htmlspecialchars($admin['email']); ?></td>
                        <td><?php echo htmlspecialchars($admin['phone']); ?></td>
                        <td>
                            <button class="btn btn-warning" data-bs-toggle="modal" data-bs-target="#editAdminModal<?php echo $admin['admin_id']; ?>">Sửa</button>
                            <a href="../Admin/delete_admin.php?id=<?php echo $admin['admin_id']; ?>" class="btn btn-danger" onclick="return confirm('Bạn có chắc chắn muốn xóa không?');">Xóa</a>
                        </td>
                    </tr>

                    <!-- Modal chỉnh sửa admin -->
                    <div class="modal fade" id="editAdminModal<?php echo $admin['admin_id']; ?>" tabindex="-1" aria-labelledby="editAdminModalLabel" aria-hidden="true">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="editAdminModalLabel">Chỉnh sửa thông tin admin</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                <div class="modal-body">
                                    <form action="../Admin/update_admin.php" method="POST">
                                        <input type="hidden" name="admin_id" value="<?php echo $admin['admin_id']; ?>">
                                        <div class="mb-3">
                                            <label for="lastname" class="form-label">Họ</label>
                                            <input type="text" class="form-control" id="lastname" name="lastname" value="<?php echo htmlspecialchars($admin['lastname']); ?>" required>
                                        </div>
                                        <div class="mb-3">
                                            <label for="firstname" class="form-label">Tên</label>
                                            <input type="text" class="form-control" id="firstname" name="firstname" value="<?php echo htmlspecialchars($admin['firstname']); ?>" required>
                                        </div>
                                        <div class="mb-3">
                                            <label for="email" class="form-label">Email</label>
                                            <input type="email" class="form-control" id="email" name="email" value="<?php echo htmlspecialchars($admin['email']); ?>" required>
                                        </div>
                                        <div class="mb-3">
                                            <label for="phone" class="form-label">Điện thoại</label>
                                            <input type="text" class="form-control" id="phone" name="phone" value="<?php echo htmlspecialchars($admin['phone']); ?>" required>
                                        </div>
                                        <button type="submit" class="btn btn-primary">Cập nhật</button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <!-- Modal thêm admin -->
    <div class="modal fade" id="addAdminModal" tabindex="-1" aria-labelledby="addAdminModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addAdminModalLabel">Thêm tài khoản admin</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form action="../Admin/create_admin.php" method="POST">
                        <div class="mb-3">
                            <label for="lastname" class="form-label">Họ</label>
                            <input type="text" class="form-control" id="lastname" name="lastname" required>
                        </div>
                        <div class="mb-3">
                            <label for="firstname" class="form-label">Tên</label>
                            <input type="text" class="form-control" id="firstname" name="firstname" required>
                        </div>
                        <div class="mb-3">
                            <label for="email" class="form-label">Email</label>
                            <input type="email" class="form-control" id="email" name="email" required>
                        </div>
                        <div class="mb-3">
                            <label for="phone" class="form-label">Điện thoại</label>
                            <input type="text" class="form-control" id="phone" name="phone" required>
                        </div>
                        <button type="submit" class="btn btn-primary">Thêm tài khoản</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script src="../JavaScript/admin_detail.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.min.js"></script>

</body>

</html>