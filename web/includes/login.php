<?php
require_once __DIR__ . '/config.php';
session_start();

try {
    $conn = getDBConnection();
    
    $json = file_get_contents('php://input');
    $data = json_decode($json, true);
    
    $user = $data['username'];
    $pass = $data['password'];
    
    $query = "SELECT * FROM users WHERE username='$user' AND password='$pass'";
    $result = $conn->query($query);
    
    if($result->rowCount() > 0) {
        $user = $result->fetch(PDO::FETCH_ASSOC);
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['username'] = $user['username'];
        
        echo json_encode([
            "success" => true,
            "message" => "로그인 성공!"
        ]);
    } else {
        echo json_encode([
            "success" => false,
            "message" => "아이디 또는 비밀번호가 잘못되었습니다."
        ]);
    }
} catch(PDOException $e) {
    echo json_encode([
        "success" => false,
        "message" => "데이터베이스 오류: " . $e->getMessage()
    ]);
}
?> 