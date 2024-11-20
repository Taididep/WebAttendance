
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
                    alert("Có lỗi xảy ra!");
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