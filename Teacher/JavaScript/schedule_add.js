
document.getElementById('addDate').addEventListener('click', function () {
    const scheduleFields = document.getElementById('scheduleFields');
    const scheduleRows = document.querySelectorAll('.schedule-row');
    const newIndex = scheduleRows.length + 1;

    // Lấy ngày cuối cùng hiện tại
    const lastDateInput = scheduleRows[scheduleRows.length - 1].querySelector('input[name="dates[]"]');
    const lastDate = lastDateInput ? new Date(lastDateInput.value) : new Date();

    // Tính toán ngày mới (tăng thêm 7 ngày)
    lastDate.setDate(lastDate.getDate() + 7);

    const newField = document.createElement('div');
    newField.classList.add('row', 'mb-3', 'align-items-end', 'schedule-row');
    newField.innerHTML = `
    <div class="col-md-5">
        <div class="input-group">
            <span class="input-group-text">${newIndex}</span>
            <input type="date" class="form-control" name="dates[]" required value="${lastDate.toISOString().split('T')[0]}">
        </div>
    </div>
    <div class="col-md-3">
        <input type="number" class="form-control" name="start_time[]" required min="1" max="17" placeholder="Tiết bắt đầu" value="<?php echo htmlspecialchars($startPeriod); ?>">
    </div>
    <div class="col-md-3">
        <input type="number" class="form-control" name="end_time[]" required min="1" max="17" placeholder="Tiết kết thúc" value="<?php echo htmlspecialchars($endPeriod); ?>">
    </div>
    <div class="col-md-1">
        <button type="button" class="btn btn-danger btn-lg remove-date"><i class="bi bi-trash fs-5"></i></button>
    </div>
`;

    scheduleFields.appendChild(newField);
});

document.addEventListener('click', function (event) {
    if (event.target.classList.contains('remove-date')) {
        event.target.closest('.schedule-row').remove();
    }
});
