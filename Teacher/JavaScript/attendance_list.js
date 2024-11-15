document.getElementById("addStudentForm").addEventListener("submit", function (event) {
    event.preventDefault(); // Ngăn chặn gửi form theo cách thông thường

    const classId = document.querySelector("input[name='class_id']").value;
    const studentId = document.getElementById("studentIdInput").value;
    const joinClassMessage = document.getElementById("joinClassMessage");

    // Gửi yêu cầu AJAX tới add_student.php
    fetch(basePath + "Class/add_student.php", {
        method: "POST",
        headers: {
            "Content-Type": "application/x-www-form-urlencoded"
        },
        body: `class_id=${encodeURIComponent(classId)}&student_id=${encodeURIComponent(studentId)}`
    })
        .then(response => response.json())
        .then(data => {
            joinClassMessage.classList.remove("d-none");
            if (data.success) {
                joinClassMessage.classList.add("alert-success");
                joinClassMessage.classList.remove("alert-danger");
                joinClassMessage.innerText = data.message;
                // Reset form sau khi thêm sinh viên thành công
                document.getElementById("addStudentForm").reset();
            } else {
                joinClassMessage.classList.add("alert-danger");
                joinClassMessage.classList.remove("alert-success");
                joinClassMessage.innerText = data.message;
            }
        })
        .catch(error => {
            joinClassMessage.classList.remove("d-none");
            joinClassMessage.classList.add("alert-danger");
            joinClassMessage.classList.remove("alert-success");
            joinClassMessage.innerText = "Có lỗi xảy ra. Vui lòng thử lại.";
        });
});

// Lắng nghe sự kiện 'hidden.bs.modal' để tải lại trang khi modal đóng
document.querySelector('#addStudentModal').addEventListener('hidden.bs.modal', function () {
    location.reload(); // Tải lại trang khi modal đóng
});

document.getElementById('confirmAttendanceBtnList').addEventListener('click', function () {
    const input = document.getElementById('attendanceInputList');
    const index = parseInt(input.value); // Lấy giá trị buổi nhập vào
    const totalSchedules = totalDatesList; // Sử dụng biến toàn cục

    // Kiểm tra nếu không nhập giá trị hoặc nhập "0", hiển thị tất cả
    if (!input.value || index === 0) {
        document.querySelectorAll('#attendanceList .list-data').forEach(cell => {
            cell.style.display = ''; // Hiện tất cả các dữ liệu
        });
        document.querySelectorAll('#attendanceList .list-column').forEach(column => {
            column.style.display = ''; // Hiện tất cả các cột
        });
        return;
    }

    // Ẩn tất cả các cột và dữ liệu
    document.querySelectorAll('#attendanceList .list-data, #attendanceList .list-column').forEach(cell => {
        cell.style.display = 'none';
    });

    // Hiện cột buổi đã nhập
    const cells = document.querySelectorAll(`#attendanceList td:nth-child(${index + 10})`); // Cột thứ index (cột 11 là buổi đầu tiên)
    cells.forEach(cell => {
        cell.style.display = ''; // Hiện cột tương ứng
    });

    // Hiện tiêu đề cột tương ứng
    const headerCells = document.querySelectorAll(`#attendanceList th.list-column`);
    headerCells.forEach((headerCell, idx) => {
        if (idx === index - 1) {
            headerCell.style.display = ''; // Hiện tiêu đề cột tương ứng
        } else {
            headerCell.style.display = 'none'; // Ẩn các tiêu đề cột khác
        }
    });
});






// khoa
const scheduleCells = document.querySelectorAll('.list-column');

scheduleCells.forEach(cell => {
    const dateText = cell.querySelector('small').innerText;  // Lấy ngày tháng từ ô điểm danh
    const [day, month, year] = dateText.split('/').map(Number);  // Tách ngày, tháng, năm
    const scheduleDate = new Date(year, month - 1, day);  // Tạo đối tượng Date từ ngày, tháng, năm

    // So sánh ngày điểm danh với thời gian hiện tại
    if (scheduleDate.toDateString() === currentDateTime.toDateString()) {
        // Nếu ngày là hôm nay
        cell.classList.add('today', 'unlocked');
    }
    else if (scheduleDate < currentDateTime) {
        // Nếu ngày đã qua
        cell.classList.add('table-secondary');
        cell.innerHTML = cell.innerHTML;  // Không thêm icon ổ khóa
        const link = cell.querySelector('a');
        if (link) link.style.pointerEvents = 'none';  // Tắt link
        cell.style.pointerEvents = 'none';  // Tắt ô điểm danh
    }
    else {
        // Kiểm tra nếu buổi học chưa diễn ra
        const scheduleStartTime = new Date(scheduleDate.getFullYear(), scheduleDate.getMonth(), scheduleDate.getDate(), 0, 0, 0);
        const endTime = new Date(scheduleStartTime.getTime() + 24 * 60 * 60 * 1000); // Cộng 24 giờ để tính giờ kết thúc

        if (currentDateTime >= scheduleStartTime && currentDateTime < endTime) {
            // Mở khóa cho buổi học hiện tại
            cell.classList.add('unlocked');
            const link = cell.querySelector('a');
            if (link) link.style.pointerEvents = '';  // Bật link
            cell.style.pointerEvents = '';  // Bật ô điểm danh
        } else {
            // Nếu buổi học chưa tới hoặc đã qua
            cell.classList.add('table-secondary');
            cell.innerHTML = cell.innerHTML;  // Không thêm icon ổ khóa
            const link = cell.querySelector('a');
            if (link) link.style.pointerEvents = 'none';  // Tắt link
            cell.style.pointerEvents = 'none';  // Tắt ô điểm danh
        }
    }
});
