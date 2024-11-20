// 안전한 메시지 표시 함수
function showMessage(message) {
    const messageElement = document.getElementById('message');
    messageElement.textContent = message; // innerHTML 대신 textContent 사용
}

// 로그인 폼 제출 처리
document.getElementById('loginForm').addEventListener('submit', async (e) => {
    e.preventDefault();
    
    try {
        const formData = new FormData(e.target);
        const response = await fetch('/includes/login.php', {
            method: 'POST',
            body: formData
        });
        
        const data = await response.json();
        showMessage(data.message);
        
        if (data.success) {
            window.location.href = '/dashboard.php';
        }
    } catch (error) {
        showMessage('오류가 발생했습니다. 다시 시도해주세요.');
    }
}); 