<?php
define('DB_HOST', '127.0.0.1');
define('DB_NAME', 'vulnerable_db');
define('DB_USER', 'root');
define('DB_PASS', '');
define('UPLOAD_DIR', __DIR__ . '/../uploads/');

function getDBConnection() {
    $host = 'DB_HOST';  // 데이터베이스 호스트
    $dbname = 'DB_NAME';  // 데이터베이스 이름
    $username = 'DB_USER';  // 데이터베이스 사용자
    $password = 'DB_PASS';  // 데이터베이스 비밀번호
    
    try {
        $conn = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        return $conn;
    } catch(PDOException $e) {
        throw new PDOException($e->getMessage());
    }
}
?>
