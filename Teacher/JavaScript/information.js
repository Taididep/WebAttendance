function handleFileSelect(event) {
    // Kích hoạt gửi biểu mẫu chỉ khi tệp đã được chọn
    if (event.target.files.length > 0) {
        document.getElementById('avatarForm').submit(); // Gửi biểu mẫu
    }
}