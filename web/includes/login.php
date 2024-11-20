<?php
require_once __DIR__ . '/config.php';
session_start();

try {
    error_log("로그인 시도 - 시작");
    $conn = getDBConnection();
    
    $json = file_get_contents('php://input');
    error_log("받은 JSON 데이터: " . $json);
    
    $data = json_decode($json, true);
    if (json_last_error() !== JSON_ERROR_NONE) {
        error_log("JSON 디코딩 오류: " . json_last_error_msg());
        throw new Exception("잘못된 요청 형식입니다.");
    }
    
    if (!isset($data['username']) || !isset($data['password'])) {
        error_log("필수 필드 누락: username 또는 password");
        throw new Exception("아이디와 비밀번호를 모두 입력해주세요.");
    }
    
    $user = $data['username'];
    $pass = $data['password'];
    
    $query = "SELECT * FROM users WHERE username='$user' AND password='$pass'";
    error_log("실행 쿼리: " . $query);
    
    $result = $conn->query($query);
    if (!$result) {
        error_log("쿼리 실행 오류: " . $conn->error);
        throw new Exception("데이터베이스 조회 중 오류가 발생했습니다.");
    }
    
    if($result->num_rows > 0) {
        $user = $result->fetch_assoc();
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['username'] = $user['username'];
        error_log("로그인 성공 - 사용자: " . $user['username']);
        
        echo json_encode([
            "success" => true,
            "message" => "로그인 성공!"
        ]);
    } else {
        error_log("로그인 실패 - 일치하는 사용자 없음");
        echo json_encode([
            "success" => false,
            "message" => "아이디 또는 비밀번호가 잘못되었습니다."
        ]);
    }
} catch(Exception $e) {
    error_log("로그인 오류: " . $e->getMessage());
    echo json_encode([
        "success" => false,
        "message" => "로그인 처리 중 오류가 발생했습니다: " . $e->getMessage()
    ]);
}
?> 