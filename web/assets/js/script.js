console.log('스크립트 시작');

function showMessage(message, isError = false) {
    console.log('메시지 표시:', message, isError);
    const messageElement = document.getElementById('message');
    messageElement.textContent = message;
    messageElement.style.color = isError ? 'red' : 'green';
}

// 로그인 폼 이벤트 리스너 등록
console.log('로그인 폼 이벤트 리스너 등록');
document.getElementById('loginForm').addEventListener('submit', async (event) => {
    console.log('폼 제출 이벤트 발생');
    event.preventDefault();
    
    const username = document.getElementById('username').value;
    const password = document.getElementById('password').value;
    console.log('입력값 확인:', { username, password: '***' });
    
    try {
        console.log('로그인 요청 시작');
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
        console.log('서버 응답 수신:', response.status);
        
        // 응답 텍스트 먼저 확인
        const responseText = await response.text();
        console.log('응답 원본:', responseText);
        
        let data;
        try {
            data = JSON.parse(responseText);
            console.log('파싱된 데이터:', data);
        } catch (parseError) {
            console.error('JSON 파싱 오류:', parseError);
            throw new Error('서버 응답을 처리할 수 없습니다.');
        }
        
        if (data.debug) {
            console.group('=== 서버 디버그 로그 ===');
            data.debug.forEach(log => console.log(log));
            console.groupEnd();
        }
        
        if (data.success) {
            console.log('로그인 성공');
            showMessage(data.message, false);
            setTimeout(() => {
                console.log('페이지 이동');
                window.location.href = './upload.html';
            }, 1000);
        } else {
            console.log('로그인 실패');
            showMessage(data.message, true);
        }
    } catch (error) {
        console.error('오류 발생:', error);
        showMessage('오류가 발생했습니다. 다시 시도해주세요.', true);
    }
}); 