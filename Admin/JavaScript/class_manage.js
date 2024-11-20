
$(document).ready(function () {
    var semesterId = $('#semester').val() || "<?php echo $defaultSemesterId; ?>";
    if (semesterId) {
        loadClasses(semesterId);
    }

    $('#semester').change(function () {
        var semesterId = $(this).val();
        if (semesterId) {
            loadClasses(semesterId);
        } else {
            $('#classList').empty();
        }
    });
});

function loadClasses(semesterId) {
    $.ajax({
        url: 'get_classes.php',
        type: 'POST',
        data: {
            semester_id: semesterId
        },
        success: function (data) {
            $('#classList').html(data);
        },
        error: function () {
            $('#classList').html('<div class="alert alert-danger">Có lỗi xảy ra khi tải dữ liệu.</div>');
        }
    });
}

$(document).on('click', '.btn-cancel', function (e) {
    e.preventDefault();
    var classId = $(this).data('class-id');
    if (confirm('Bạn có chắc chắn muốn hủy lớp này không?')) {
        $.ajax({
            url: 'delete_class.php',
            type: 'POST',
            data: {
                class_id: classId
            },
            success: function (response) {
                if (response.success) {
                    alert('Lớp đã được xóa thành công.');
                    $('#semester').change();
                } else {
                    alert('Có lỗi xảy ra: ' + response.message);
                }
            },
            error: function () {
                alert('Có lỗi xảy ra khi xóa lớp.');
            }
        });
    }
});


// Đảm bảo rằng modal đóng sau khi form được gửi thành công
$('#addSemesterModal').on('hidden.bs.modal', function () {
    location.reload(); // Tải lại trang sau khi đóng modal (nếu cần)
});
