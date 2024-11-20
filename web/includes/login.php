<?php
$debug = [];  // 디버그 로그를 저장할 배열

try {
    $debug[] = "로그인 시도 시작";
    $conn = getDBConnection();
    $debug[] = "DB 연결 성공";
    
    $json = file_get_contents('php://input');
    $debug[] = "받은 JSON 데이터: " . $json;
    
    $data = json_decode($json, true);
    $debug[] = "디코딩된 데이터: " . print_r($data, true);
    
    $user = $data['username'];
    $pass = $data['password'];
    $debug[] = "사용자명: " . $user;
    
    $query = "SELECT * FROM users WHERE username='$user' AND password='$pass'";
    $debug[] = "실행될 쿼리: " . $query;
    
    $result = $conn->query($query);
    $debug[] = "쿼리 실행 완료";
    
    if($result->rowCount() > 0) {
        $user = $result->fetch(PDO::FETCH_ASSOC);
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['username'] = $user['username'];
        
        echo json_encode([
            "success" => true,
            "message" => "로그인 성공!",
            "debug" => $debug
        ]);
    } else {
        echo json_encode([
            "success" => false,
            "message" => "아이디 또는 비밀번호가 잘못되었습니다.",
            "debug" => $debug
        ]);
    }
} catch(PDOException $e) {
    echo json_encode([
        "success" => false,
        "message" => "데이터베이스 오류: " . $e->getMessage(),
        "debug" => $debug
    ]);
}
?> 