<?php
// 오류 로깅 설정
error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('log_errors', 1);
ini_set('error_log', __DIR__ . '/error.log');

// 디버깅을 위한 로그 파일 생성
file_put_contents(__DIR__ . '/debug.log', "로그인 요청 시작: " . date('Y-m-d H:i:s') . "\n", FILE_APPEND);

if (ob_get_level()) ob_end_clean();
header('Content-Type: application/json; charset=utf-8');

try {
    require_once __DIR__ . '/config.php';
    session_start();
    
    // POST 데이터 로깅
    $raw_data = file_get_contents('php://input');
    file_put_contents(__DIR__ . '/debug.log', "받은 데이터: " . $raw_data . "\n", FILE_APPEND);
    
    $data = json_decode($raw_data, true);
    if (json_last_error() !== JSON_ERROR_NONE) {
        throw new Exception("JSON 파싱 오류: " . json_last_error_msg());
    }
    
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
    // 오류 로깅
    error_log("로그인 오류: " . $e->getMessage());
    file_put_contents(__DIR__ . '/debug.log', "오류 발생: " . $e->getMessage() . "\n", FILE_APPEND);
    
    http_response_code(500);
    echo json_encode([
        "success" => false,
        "message" => "서버 오류가 발생했습니다: " . $e->getMessage()
    ], JSON_UNESCAPED_UNICODE);
}
?> 