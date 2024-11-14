// Xác nhận buổi học chỉnh sửa
document.getElementById('confirmAttendanceBtnEdit').addEventListener('click', function () {
    const input = document.getElementById('attendanceInputEdit');
    const index = parseInt(input.value); // Lấy giá trị buổi nhập vào
    const totalSchedules = totalDatesEdit; // Sử dụng biến truyền từ PHP

    // Kiểm tra nếu không nhập giá trị hoặc nhập "0", hiển thị tất cả
    if (!input.value || index === 0) {
        document.querySelectorAll('#attendanceEdit .edit-data').forEach(cell => {
            cell.style.display = ''; // Hiện tất cả các dữ liệu
        });
        document.querySelectorAll('#attendanceEditt .edit-column').forEach(column => {
            column.style.display = ''; // Hiện tất cả các cột
        });
        return;
    }

    // Ẩn tất cả các cột và tiêu đề
    document.querySelectorAll('#attendanceEdit .edit-data, #attendanceEdit .edit-column').forEach(cell => {
        cell.style.display = 'none';
    });

    // Hiện cột buổi đã nhập
    const cells = document.querySelectorAll(`#attendanceEdit td:nth-child(${index + 10})`); // Cột thứ index (cột 11 là buổi đầu tiên)
    cells.forEach(cell => {
        cell.style.display = ''; // Hiện cột tương ứng
    });

    // Cập nhật tiêu đề cột
    const headerCells = document.querySelectorAll(`#attendanceEdit th.edit-column`);
    headerCells.forEach((headerCell, idx) => {
        if (idx === index - 1) {
            headerCell.style.display = ''; // Hiện tiêu đề cột tương ứng
        } else {
            headerCell.style.display = 'none'; // Ẩn các tiêu đề cột khác
        }
    });
});

// Lấy ngày hiện tại
var currentDate = new Date().toISOString().split('T')[0];  // Lấy định dạng 'Y-m-d'

// Lấy tất cả các dropdown attendance
var attendanceSelects = document.querySelectorAll('.attendance-select');

// Duyệt qua từng ô nhập liệu (dropdown)
attendanceSelects.forEach(function (select) {
    var scheduleDate = select.getAttribute('data-date');  // Lấy ngày điểm danh từ thuộc tính data-date

    // So sánh ngày điểm danh với ngày hiện tại
    if (scheduleDate > currentDate) {
        // Đặt giá trị của select thành rỗng
        select.value = '';

        // Disable select nếu ngày điểm danh chưa đến
        select.disabled = true;
    }
});

