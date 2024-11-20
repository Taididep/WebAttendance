document.addEventListener('DOMContentLoaded', function () {
    const searchInput = document.getElementById('searchInput');
    const courseTableBody = document.getElementById('courseTableBody');

    searchInput.addEventListener('input', function () {
        const query = searchInput.value.toLowerCase(); // Lấy giá trị tìm kiếm (chuyển về chữ thường)
        const rows = courseTableBody.getElementsByTagName('tr'); // Lấy tất cả hàng trong bảng

        // Lọc các hàng trong bảng
        Array.from(rows).forEach(row => {
            const cells = row.getElementsByTagName('td'); // Lấy các ô dữ liệu
            const rowText = Array.from(cells).map(cell => cell.textContent.toLowerCase()).join(' ');

            // Hiển thị hoặc ẩn hàng dựa trên kết quả tìm kiếm
            row.style.display = rowText.includes(query) ? '' : 'none';
        });
    });
});



$(document).ready(function () {
    $('#createCourseForm').on('submit', function (e) {
        e.preventDefault(); // Ngừng hành động mặc định của form

        // Lấy dữ liệu từ form
        var formData = $(this).serialize();

        $.ajax({
            type: 'POST',
            url: 'create_course.php', // Đường dẫn xử lý form
            data: formData,
            success: function (response) {
                // Xử lý phản hồi từ server
                if (response.includes("Thêm môn học thành công")) {
                    alert("Thêm môn học thành công!");
                    $('#createCourseModal').modal('hide'); // Đóng modal
                    location.reload(); // Tải lại trang để hiển thị môn học mới
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



