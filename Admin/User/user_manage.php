<?php
session_start();

$basePath = '../'; // Đường dẫn gốc
include __DIR__ . '/../../Connect/connect.php';
include __DIR__ . '/../../LayoutPages/navbar_admin.php';
include __DIR__ . '/../../Account/islogin.php';

// Số lượng người dùng mỗi trang
$usersPerPage = 10;

// Xác định trang hiện tại
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $usersPerPage;

// Truy vấn SQL để lấy danh sách người dùng và vai trò của họ với phân trang
$sql_users = "SELECT u.user_id, u.username, r.role_name
              FROM users u
              JOIN user_roles ur ON u.user_id = ur.user_id
              JOIN roles r ON ur.role_id = r.role_id
              LIMIT :offset, :limit";

// Thực hiện truy vấn và lấy kết quả
$stmt_users = $conn->prepare($sql_users);
$stmt_users->bindValue(':offset', $offset, PDO::PARAM_INT);
$stmt_users->bindValue(':limit', $usersPerPage, PDO::PARAM_INT);
$stmt_users->execute();
$users = $stmt_users->fetchAll(PDO::FETCH_ASSOC);

// Truy vấn tổng số người dùng để tính tổng số trang
$sql_count = "SELECT COUNT(*) as total_users
              FROM users u
              JOIN user_roles ur ON u.user_id = ur.user_id
              JOIN roles r ON ur.role_id = r.role_id";
$stmt_count = $conn->prepare($sql_count);
$stmt_count->execute();
$totalUsers = $stmt_count->fetch(PDO::FETCH_ASSOC)['total_users'];
$totalPages = ceil($totalUsers / $usersPerPage);

$stmt_users->closeCursor(); // Đóng kết quả của truy vấn

$roleMapping = [
    'admin' => 'Quản trị',
    'teacher' => 'Giảng viên',
    'student' => 'Sinh viên',
];

?>

<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quản lý tài khoản</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <link rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-icons/1.10.0/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="../Css/user_manage.css">
</head>

<body>

    <div class="container">
        <div class="card p-4">
            <!-- Title -->
            <h2 class="mb-2 text-center">Danh sách tài khoản</h2>
            <hr>

            <!-- Thanh tìm kiếm và nút thêm tài khoản -->
            <div class="d-flex justify-content-between mb-3">
                <div class="flex-grow-1">
                    <input type="text" id="searchInput" class="form-control" placeholder="Tìm kiếm tài khoản..." />
                </div>
            </div>

            <!-- Bảng danh sách tài khoản -->
            <div id="AccountList">
                <table class="table">
                    <thead>
                        <tr>
                            <th style="width: 1%;">STT</th>
                            <th>ID tài khoản</th>
                            <th>Tên đăng nhập</th>
                            <th>Vai trò</th>
                            <th style="width: 1%;"></th>
                        </tr>
                    </thead>
                    <tbody id="AccountTable">
                        <?php
                        $stt = $offset + 1;
                        foreach ($users as $user): ?>
                            <tr>
                                <td><?php echo $stt++; ?></td>
                                <td><?php echo htmlspecialchars($user['user_id']); ?></td>
                                <td><?php echo htmlspecialchars($user['username']); ?></td>
                                <td><?php echo isset($roleMapping[$user['role_name']]) ? $roleMapping[$user['role_name']] : htmlspecialchars($user['role_name']); ?></td>
                                <td>
                                    <div class="dropdown">
                                        <button class="btn btn-link dropdown-toggle" type="button" id="dropdownMenuButton" data-bs-toggle="dropdown" aria-expanded="false">
                                            <i class="bi bi-three-dots-vertical"></i>
                                        </button>
                                        <ul class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                                            <li><a class="dropdown-item" href="#" data-bs-toggle="modal" data-bs-target="#editAccountModal<?php echo $user['user_id']; ?>">Sửa</a></li>
                                            <li><a class="dropdown-item" href="delete_account.php?user_id=<?php echo $user['user_id']; ?>" onclick="return confirm('Bạn có chắc chắn muốn xóa tài khoản này không?')">Xóa</a></li>
                                        </ul>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

            <!-- Phân trang -->
            <nav>
                <ul class="pagination justify-content-center">
                    <?php if ($page > 1): ?>
                        <li class="page-item"><a class="page-link" href="?page=<?php echo $page - 1; ?>">Previous</a></li>
                    <?php endif; ?>

                    <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                        <li class="page-item <?php echo $i == $page ? 'active' : ''; ?>">
                            <a class="page-link" href="?page=<?php echo $i; ?>"><?php echo $i; ?></a>
                        </li>
                    <?php endfor; ?>

                    <?php if ($page < $totalPages): ?>
                        <li class="page-item"><a class="page-link" href="?page=<?php echo $page + 1; ?>">Next</a></li>
                    <?php endif; ?>
                </ul>
            </nav>
        </div>
    </div>

    <?php foreach ($users as $user): ?>
        <div class="modal fade" id="editAccountModal<?php echo $user['user_id']; ?>" tabindex="-1" aria-labelledby="editAccountLabel<?php echo $user['user_id']; ?>" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="editAccountLabel<?php echo $user['user_id']; ?>">Chỉnh sửa tài khoản</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <form method="POST" action="edit_account.php">
                        <div class="modal-body">
                            <!-- Hidden input để giữ user_id -->
                            <input type="hidden" name="user_id" value="<?php echo $user['user_id']; ?>" />

                            <!-- Trường sửa tên đăng nhập -->
                            <div class="mb-3">
                                <label for="usernameInput<?php echo $user['user_id']; ?>" class="form-label">Tên đăng nhập</label>
                                <input type="text" class="form-control" id="usernameInput<?php echo $user['user_id']; ?>"
                                    name="username" value="<?php echo htmlspecialchars($user['username']); ?>" required />
                            </div>

                            <!-- Trường sửa vai trò -->
                            <div class="mb-3">
                                <label for="roleSelect<?php echo $user['user_id']; ?>" class="form-label">Vai trò</label>
                                <select class="form-select" name="role_id" id="roleSelect<?php echo $user['user_id']; ?>">
                                    <?php
                                    $sql_roles = "SELECT * FROM roles";
                                    $stmt_roles = $conn->query($sql_roles);
                                    $roles = $stmt_roles->fetchAll(PDO::FETCH_ASSOC);
                                    foreach ($roles as $role): ?>
                                        <option value="<?php echo $role['role_id']; ?>" <?php echo $role['role_name'] === $user['role_name'] ? 'selected' : ''; ?>>
                                            <?php
                                            // Ánh xạ tên vai trò sang tiếng Việt
                                            $roleNameVi = isset($roleMapping[$role['role_name']]) ? $roleMapping[$role['role_name']] : htmlspecialchars($role['role_name']);
                                            echo $roleNameVi;
                                            ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Đóng</button>
                            <button type="submit" class="btn btn-primary">Lưu thay đổi</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    <?php endforeach; ?>

    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.min.js"></script>
    <script src="../JavaScript/user_manage.js"></script>
</body>

</html>