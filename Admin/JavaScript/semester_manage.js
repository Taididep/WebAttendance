$(document).ready(function () {
    $('#createSemesterForm').on('submit', function (e) {
        e.preventDefault(); // Ngừng hành động mặc định của form

        // Lấy dữ liệu từ form
        var formData = $(this).serialize();

        $.ajax({
            type: 'POST',
            url: 'create_semester.php',
            data: formData,
            success: function (response) {
                // Xử lý phản hồi từ server
                if (response.includes("Thêm học kỳ thành công")) {
                    alert("Thêm học kỳ thành công!");
                    $('#createSemesterModal').modal('hide'); // Đóng modal
                } else {
                    alert(response); // Hiển thị thông báo lỗi từ PHP
                }
            },
            error: function () {
                alert("Có lỗi xảy ra khi gửi dữ liệu.");
            }
        });
    });
});

$(document).ready(function () {
    $('#editSemesterForm').on('submit', function (e) {
        e.preventDefault(); // Ngừng hành động mặc định của form

        // Lấy dữ liệu từ form
        var formData = $(this).serialize();

        $.ajax({
            type: 'POST',
            url: $(this).attr('action'), // Lấy URL từ thuộc tính action của form
            data: formData,
            success: function (response) {
                // Xử lý phản hồi từ server
                if (response.includes("Cập nhật học kỳ thành công")) {
                    alert("Cập nhật học kỳ thành công!");
                    $('#editSemesterModal').modal('hide'); // Đóng modal
                    location.reload(); // Tải lại trang để hiển thị dữ liệu mới
                } else {
                    alert(response); // Hiển thị thông báo lỗi từ PHP
                }
            },
            error: function () {
                alert("Có lỗi xảy ra khi gửi dữ liệu.");
            }
        });
    });
});



// Tìm kiếm trong bảng
$(document).ready(function () {
    $('#searchInput').on('keyup', function () {
        var value = $(this).val().toLowerCase();
        $('#semesterTableBody tr').filter(function () {
            $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1)
        });
    });
});