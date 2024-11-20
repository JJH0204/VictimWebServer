// 안전한 메시지 표시 함수
function showMessage(message, isError = false) {
    const messageElement = document.getElementById('message');
    messageElement.textContent = message;
    messageElement.style.color = isError ? 'red' : 'green';
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
        
        if (data.success) {
            showMessage(data.message, false);
            setTimeout(() => {
                window.location.href = '/public/upload.html';
            }, 1000);
        } else {
            showMessage(data.message, true);
        }
    } catch (error) {
        showMessage('오류가 발생했습니다. 다시 시도해주세요.', true);
    }
}); 