<?php
include __DIR__ . '/../../Connect/connect.php';
include __DIR__ . '/../../Account/islogin.php';

// Kiểm tra nếu có lớp học ID
if (isset($_GET['class_id'])) {
    $class_id = $_GET['class_id'];
} else {
    echo 'Không tìm thấy thông tin lớp học.';
    exit;
}

// Truy vấn bảng tin
$sql = "CALL GetAnnouncementsByClassId(?)";
$stmt = $conn->prepare($sql);
$stmt->execute([$class_id]);
$announcements = $stmt->fetchAll(PDO::FETCH_ASSOC);
$stmt->closeCursor();

// Hiển thị bảng tin
if ($announcements) {
    foreach ($announcements as $announcement) {
        ?>
        <div class="card mb-3">
            <div class="card-body">
                <!-- Tiêu đề và ngày tạo -->
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h5 class="card-title mb-0"><?php echo htmlspecialchars($announcement['title']); ?></h5>
                    <div class="d-flex align-items-center">
                        <small class="text-muted ms-3"><?php echo $announcement['created_at']; ?></small>
                    </div>
                </div>

                <hr>

                <!-- Nội dung thông báo -->
                <div class="border p-3 mb-3" style="background-color: #f8f9fa;">
                    <p class="card-text"><?php echo nl2br(htmlspecialchars($announcement['content'])); ?></p>
                </div>
            </div>

            <hr>

            <!-- Kiểm tra nếu có bình luận -->
            <?php
            // Gọi thủ tục
            $comment_sql = "CALL GetCommentCountByAnnouncementId(?, @comment_count)";
            $comment_stmt = $conn->prepare($comment_sql);
            $comment_stmt->execute([$announcement['announcement_id']]);
            
            // Lấy giá trị của biến đầu ra
            $result = $conn->query("SELECT @comment_count AS comment_count");
            $comment_count = $result->fetch(PDO::FETCH_ASSOC)['comment_count']; // comment_count là một số nguyên

            // Kiểm tra số lượng bình luận
            if ($comment_count > 0) { // Sử dụng $comment_count như một số nguyên
                ?>
                <div class="card-body">
                    <h6 class="mb-3" id="commentsTitle_<?php echo $announcement['announcement_id']; ?>" style="cursor: pointer;"
                        onclick="toggleComments(<?php echo $announcement['announcement_id']; ?>)">
                        Bình luận (<?php echo $comment_count; ?>) <!-- Sử dụng $comment_count như một số nguyên -->
                    </h6>
                    <div id="commentsList_<?php echo $announcement['announcement_id']; ?>">
                        <?php
                        // Truy vấn bình luận từ giáo viên và học sinh
                        $comment_sql = "CALL GetCommentsByAnnouncementId(?)";
                        $comment_stmt = $conn->prepare($comment_sql);
                        $comment_stmt->execute([$announcement['announcement_id']]);
                        $comments = $comment_stmt->fetchAll(PDO::FETCH_ASSOC);
                        $comment_stmt->closeCursor();

                        if ($comments) {
                            // Hiển thị tất cả bình luận từ cũ đến mới
                            foreach ($comments as $comment) {
                                echo '<div class="mb-2 pb-2">';
                                echo '<p class="mb-1"><strong>' . htmlspecialchars($comment['lastname']) . ' ' . htmlspecialchars($comment['firstname']) . '</strong> <small class="text-muted">' . $comment['created_at'] . '</small></p>';
                                echo '<p class="mb-0">' . htmlspecialchars($comment['content']) . '</p>';
                                echo '</div>';
                            }
                        }
                        ?>
                    </div>
                </div>
                <hr>
                <?php
            }
            ?>

            <!-- Thanh nhập bình luận -->
            <div class="card-body">
                <form action="../Announcement/add_comment.php?class_id=<?php echo $class_id; ?>" method="POST"
                    class="d-flex align-items-center">
                    <div class="flex-grow-1 me-2">
                        <textarea class="form-control" name="content" rows="2" placeholder="Nhập bình luận của bạn..."
                            required></textarea>
                    </div>
                    <input type="hidden" name="announcement_id"
                        value="<?php echo htmlspecialchars($announcement['announcement_id']); ?>">
                    <button type="submit" class="btn btn-secondary">Bình luận</button>
                </form>
            </div>
        </div>
        <?php
    }
} else {
    echo '<p class="text-center">Chưa có bảng tin nào.</p>';
}
?>

<!-- JavaScript để bật/tắt việc hiển thị các bình luận cũ -->
<script src="../JavaScript/announcement.js"></script>