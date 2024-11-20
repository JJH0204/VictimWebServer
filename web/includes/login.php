<?php
require_once 'config.php';
session_start();

try {
    $conn = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    
    // SQL 인젝션 취약점이 있는 쿼리
    $user = $_POST['username'];
    $pass = $_POST['password'];
    
    $query = "SELECT * FROM users WHERE username='$user' AND password='$pass'";
    $result = $conn->query($query);
    
    if($result->rowCount() > 0) {
        echo "로그인 성공!";
    } else {
        echo "로그인 실패!";
    }
} catch(PDOException $e) {
    echo "에러: " . $e->getMessage();
}
?> 