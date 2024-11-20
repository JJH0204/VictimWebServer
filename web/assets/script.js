// XSS 취약점이 있는 메시지 표시 함수
function showMessage(message) {
    document.getElementById('message').innerHTML = message;
} 