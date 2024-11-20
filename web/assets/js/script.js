document.getElementById('loginForm').addEventListener('submit', async (event) => {
    event.preventDefault();
    
    const username = document.getElementById('username').value;
    const password = document.getElementById('password').value;
    
    try {
        const response = await fetch('../includes/login.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({
                username: username,
                password: password
            })
        });
        
        const data = await response.json();
        
        if (data.success) {
            showMessage(data.message, false);
            setTimeout(() => {
                window.location.href = './upload.php';
            }, 1000);
        } else {
            showMessage(data.message, true);
        }
    } catch (error) {
        showMessage('오류가 발생했습니다. 다시 시도해주세요.', true);
    }
});

function showMessage(message, isError = false) {
    const messageElement = document.getElementById('message');
    messageElement.textContent = message;
    messageElement.style.color = isError ? 'red' : 'green';
} 