<?php
define('UPLOAD_DIR', __DIR__ . '/../uploads/');

function getDBConnection() {
    $host = '127.0.0.1';  // 데이터베이스 호스트
    $dbname = 'vulnerable_db';  // 데이터베이스 이름
    $username = 'root';  // 데이터베이스 사용자
    $password = '1q2w3e4r!@#$';  // 데이터베이스 비밀번호
    
    try {
        $conn = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        return $conn;
    } catch(PDOException $e) {
        throw new PDOException($e->getMessage());
    }
}
?>
