function openEditModal(schedule_id, date, start_time, end_time) {
    document.getElementById('schedule_id').value = schedule_id;

    // Định dạng ngày thủ công để tránh lệch múi giờ
    const dateObj = new Date(date);
    const formattedDate = `${dateObj.getFullYear()}-${(dateObj.getMonth() + 1).toString().padStart(2, '0')}-${dateObj.getDate().toString().padStart(2, '0')}`;
    document.getElementById('date').value = formattedDate;

    document.getElementById('start_time').value = start_time;
    document.getElementById('end_time').value = end_time;
    new bootstrap.Modal(document.getElementById('editScheduleModal')).show();
}

