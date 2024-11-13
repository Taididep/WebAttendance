$(document).ready(function() {
    // Tự động gọi AJAX khi trang được tải
    var semesterId = $('#semester').val() || "<?php echo $defaultSemesterId; ?>"; // Lấy semester_id đã chọn hoặc mặc định
    if (semesterId) {
        $.ajax({
            url: 'get_classes.php',
            type: 'POST',
            data: {
                semester_id: semesterId
            },
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
                url: 'get_classes.php',
                type: 'POST',
                data: {
                    semester_id: semesterId
                },
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