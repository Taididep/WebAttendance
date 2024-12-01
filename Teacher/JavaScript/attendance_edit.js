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

    // Ẩn tất cả các cột và dữ liệu
    document.querySelectorAll('#attendanceEdit .edit-data, #attendanceEdit .edit-column').forEach(cell => {
        cell.style.display = 'none';
    });

    // Hiện cột buổi đã nhập trong tbody
    const tbodyCells = document.querySelectorAll(`#attendanceEdit tbody td:nth-child(${index + 10})`);
    tbodyCells.forEach(cell => {
        cell.style.display = ''; // Hiện cột tương ứng trong tbody
    });

    // Hiện cột buổi đã nhập trong tfoot
    const tfootCells = document.querySelectorAll(`#attendanceEdit tfoot td:nth-child(${index + 1})`);
    tfootCells.forEach(cell => {
        cell.style.display = ''; // Hiện cột tương ứng trong tfoot
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

// Lấy ngày hiện tại với giờ, phút, giây
var currentDate = new Date().toISOString().split('.')[0];  // Định dạng 'Y-m-dTH:i:s'

// Lấy tất cả các dropdown attendance
var attendanceSelects = document.querySelectorAll('.attendance-select');

// Duyệt qua từng ô nhập liệu (dropdown)
attendanceSelects.forEach(function (select) {
    var scheduleDate = select.getAttribute('data-date');  // Lấy ngày điểm danh từ thuộc tính data-date

    // So sánh ngày điểm danh với ngày hiện tại
    if (scheduleDate + 'T' + currentDate.split('T')[1] > currentDate) {
        // Đặt giá trị của select thành rỗng
        select.value = '';

        // Disable select nếu ngày điểm danh chưa đến
        select.disabled = true;
    }
});



document.addEventListener('DOMContentLoaded', function () {
    const studentsPerPage = 10; // Số sinh viên mỗi trang
    const students = Array.from(document.querySelectorAll('#attendanceEdit tbody tr')); // Lấy tất cả các sinh viên từ bảng
    const totalStudents = students.length;
    const totalPages = Math.ceil(totalStudents / studentsPerPage); // Tính tổng số trang
    let currentPage = 1; // Mặc định là trang 1

    function showPage(page) {
        const start = (page - 1) * studentsPerPage;
        const end = start + studentsPerPage;

        // Ẩn tất cả sinh viên
        students.forEach(student => {
            student.style.display = 'none';
        });

        // Hiển thị sinh viên của trang hiện tại
        for (let i = start; i < end && i < totalStudents; i++) {
            students[i].style.display = ''; // Hiển thị sinh viên
        }

        // Cập nhật phân trang
        updatePagination(page);
    }

    function updatePagination(page) {
        const pagination = document.getElementById('paginationEdit');
        pagination.innerHTML = ''; // Xóa các nút phân trang hiện tại

        // Tạo nút "Previous"
        const prevButton = document.createElement('button');
        prevButton.classList.add('btn', 'btn-secondary', 'me-2');
        prevButton.textContent = 'Trước';
        prevButton.disabled = page === 1;
        prevButton.addEventListener('click', () => {
            if (page > 1) {
                showPage(page - 1);
            }
        });
        pagination.appendChild(prevButton);

        // Tạo các nút trang
        for (let i = 1; i <= totalPages; i++) {
            const pageButton = document.createElement('button');
            pageButton.classList.add('btn', 'btn-secondary', 'me-2');
            pageButton.textContent = i;
            pageButton.disabled = i === page;
            pageButton.addEventListener('click', () => showPage(i));
            pagination.appendChild(pageButton);
        }

        // Tạo nút "Next"
        const nextButton = document.createElement('button');
        nextButton.classList.add('btn', 'btn-secondary', 'me-2');
        nextButton.textContent = 'Sau';
        nextButton.disabled = page === totalPages;
        nextButton.addEventListener('click', () => {
            if (page < totalPages) {
                showPage(page + 1);
            }
        });
        pagination.appendChild(nextButton);
    }

    // Hiển thị trang 1 ban đầu
    showPage(currentPage);
});
