// 안전한 메시지 표시 함수
function showMessage(message, isError = false) {
    const messageElement = document.getElementById('message');
    messageElement.textContent = message;
    messageElement.style.color = isError ? 'red' : 'green';
}

// 로그인 폼 제출 처리
document.getElementById('loginForm').addEventListener('submit', async (event) => {
    event.preventDefault();
    const username = document.getElementById('username').value;
    const password = document.getElementById('password').value;
    
    fetch('../includes/login.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: `username=${encodeURIComponent(username)}&password=${encodeURIComponent(password)}`,
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showMessage(data.message, false);
            setTimeout(() => {
                window.location.href = './upload.html';
            }, 1000);
        } else {
            showMessage(data.message, true);
        }
    })
    .catch(error => {
        showMessage('오류가 발생했습니다. 다시 시도해주세요.', true);
    });
    // try {
    //     const formData = new FormData(e.target);
    //     const data = {
    //         username: formData.get('username'),
    //         password: formData.get('password')
    //     };
        
    //     const response = await fetch('../includes/login.php', {
    //         method: 'POST',
    //         headers: {
    //             'Content-Type': 'application/json'
    //         },
    //         body: JSON.stringify(data)
    //     });
        
    //     const responseData = await response.json();
        
    //     if (responseData.success) {
    //         showMessage(responseData.message, false);
    //         setTimeout(() => {
    //             window.location.href = './upload.html';
    //         }, 1000);
    //     } else {
    //         showMessage(responseData.message, true);
    //     }
    // } catch (error) {
    //     showMessage('오류가 발생했습니다. 다시 시도해주세요.', true);
    // }
}); 