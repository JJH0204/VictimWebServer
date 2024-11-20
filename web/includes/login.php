<?php
error_reporting(E_ALL);
ini_set('display_errors', 0);

if (ob_get_level()) ob_end_clean();
header('Content-Type: application/json; charset=utf-8');

try {
    require_once __DIR__ . '/config.php';
    session_start();
    
    $raw_data = file_get_contents('php://input');
    $data = json_decode($raw_data, true);
    
    if (!isset($data['username']) || !isset($data['password'])) {
        throw new Exception("아이디와 비밀번호를 모두 입력해주세요.");
    }
    
    $user = $data['username'];
    $pass = $data['password'];
    
    $query = "SELECT * FROM users WHERE username='$user' AND password='$pass'";
    
    $conn = getDBConnection();
    $result = $conn->query($query);
    if (!$result) {
        throw new Exception("데이터베이스 조회 중 오류가 발생했습니다.");
    }
    
    if($result->num_rows > 0) {
        $user = $result->fetch_assoc();
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['username'] = $user['username'];
        
        echo json_encode([
            "success" => true,
            "message" => "로그인 성공!",
            "debug" => ["로그인 성공 - 사용자: " . $user['username']]
        ], JSON_UNESCAPED_UNICODE);
    } else {
        echo json_encode([
            "success" => false,
            "message" => "아이디 또는 비밀번호가 잘못되었습니다.",
            "debug" => ["로그인 실패 - 일치하는 사용자 없음"]
        ], JSON_UNESCAPED_UNICODE);
    }
} catch(Exception $e) {
    http_response_code(500);
    echo json_encode([
        "success" => false,
        "message" => "서버 오류가 발생했습니다: " . $e->getMessage()
    ], JSON_UNESCAPED_UNICODE);
}
?> 