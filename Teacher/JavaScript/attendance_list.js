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

    // Hiện cột buổi đã nhập trong tbody
    const tbodyCells = document.querySelectorAll(`#attendanceList tbody td:nth-child(${index + 10})`);
    tbodyCells.forEach(cell => {
        cell.style.display = ''; // Hiện cột tương ứng trong tbody
    });

    // Hiện cột buổi đã nhập trong tfoot
    const tfootCells = document.querySelectorAll(`#attendanceList tfoot td:nth-child(${index + 1})`);
    tfootCells.forEach(cell => {
        cell.style.display = ''; // Hiện cột tương ứng trong tfoot
    });

    // Hiện tiêu đề cột tương ứng trong th
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
        cell.innerHTML = cell.innerHTML;
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

// Lắng nghe sự kiện khi người dùng nhập mã sinh viên
document.getElementById('removeStudentIdInput').addEventListener('input', function () {
    const studentId = this.value;
    const studentInfo = document.getElementById('studentInfo');
    const studentDetails = document.getElementById('studentDetails');
    const removeStudentButton = document.getElementById('removeStudentButton');

    // Tìm sinh viên theo mã
    const student = students.find(s => s.student_id.toString() === studentId.toString());

    if (student) {
        // Hiển thị thông tin sinh viên
        studentDetails.innerHTML = `
            <strong>Mã sinh viên:</strong> ${student.student_id}<br>
            <strong>Họ đệm:</strong> ${student.lastname}<br>
            <strong>Tên:</strong> ${student.firstname}<br>
            <strong>Giới tính:</strong> ${student.gender}<br>
            <strong>Lớp:</strong> ${student.class}<br>
            <strong>Ngày sinh:</strong> ${student.birthday}
        `;
        studentInfo.classList.remove('d-none'); // Hiện phần thông tin
        removeStudentButton.classList.remove('d-none'); // Hiện nút "Đá sinh viên"
    } else {
        // Ẩn phần thông tin và nút nếu không tìm thấy sinh viên
        studentInfo.classList.add('d-none');
        removeStudentButton.classList.add('d-none');
    }
});

// Cài đặt sự kiện cho nút "Đá sinh viên"
document.getElementById('removeStudentButton').addEventListener('click', function () {
    const studentId = document.getElementById('removeStudentIdInput').value;

    // Xác nhận trước khi đá sinh viên
    if (confirm(`Bạn có chắc chắn muốn đá sinh viên với mã ${studentId}?`)) {
        const form = document.getElementById('removeStudentForm');
        const classId = form.class_id.value;

        // Gửi yêu cầu xóa sinh viên qua AJAX
        fetch('remove_student.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: new URLSearchParams({
                student_id: studentId,
                class_id: classId
            })
        })
            .then(response => response.json())
            .then(data => {
                console.log(data);
                if (data.success) {
                    alert(data.message);
                    // Cập nhật giao diện hoặc làm mới danh sách sinh viên
                    location.reload(); // Tải lại trang để làm mới danh sách
                } else {
                    alert(data.message);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Có lỗi xảy ra, vui lòng thử lại!');
            });
    }
});

document.getElementById('uploadStudentForm').addEventListener('submit', function (event) {
    event.preventDefault(); // Ngăn chặn hành vi mặc định của form

    const formData = new FormData(this); // Lấy dữ liệu từ form
    console.log([...formData]); // In dữ liệu form để debug (có thể xóa dòng này khi hoàn thiện)

    fetch('import_excel.php', {
        method: 'POST',
        body: formData
    })
        .then(response => {
            // Kiểm tra phản hồi thô
            return response.text();  // Lấy phản hồi dưới dạng text thay vì JSON
        })
        .then(text => {
            console.log('Phản hồi từ server:', text);  // In ra phản hồi thô từ server
            try {
                const data = JSON.parse(text);  // Thử phân tích JSON từ phản hồi
                if (data.success) {
                    alert(data.message);  // Hiển thị thông báo thành công
                    location.reload();  // Tải lại danh sách sinh viên
                } else {
                    alert(`Thất bại: ${data.message}`);  // Hiển thị thông báo lỗi
                }
            } catch (error) {
                console.error('Lỗi khi phân tích JSON:', error);
                alert('Phản hồi từ server không hợp lệ. Kiểm tra console.');
            }
        })
        .catch(error => {  // Xử lý lỗi trong quá trình gọi fetch
            console.error('Lỗi:', error);
            alert('Đã xảy ra lỗi trong quá trình tải lên. Vui lòng thử lại.');
        });
});




document.addEventListener('DOMContentLoaded', function () {
    const studentsPerPage = 10; // Số sinh viên mỗi trang
    const students = Array.from(document.querySelectorAll('#attendanceList tbody tr')); // Lấy tất cả các sinh viên từ bảng
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
        const pagination = document.getElementById('pagination');
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
