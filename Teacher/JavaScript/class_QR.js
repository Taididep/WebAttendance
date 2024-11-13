// Tạo mã QR với URL chuyển hướng
const detailUrl = '<?php echo $detailUrl; ?>';
const qrCodeContainer = document.getElementById('qrCode');
new QRCode(qrCodeContainer, {
    text: detailUrl,
    width: 300,
    height: 300,
    colorDark: '#000000',
    colorLight: '#ffffff',
    correctLevel: QRCode.CorrectLevel.H
});