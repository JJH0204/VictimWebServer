<?php
define('DB_HOST', 'localhost');
define('DB_NAME', 'vulnerable_db');
define('DB_USER', 'root');
define('DB_PASS', '');
define('UPLOAD_DIR', __DIR__ . '/../uploads/');

// PDO 연결 설정
function getDBConnection() {
    try {
        $conn = new PDO(
            "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME,
            DB_USER,
            DB_PASS,
            [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
        );
        return $conn;
    } catch(PDOException $e) {
        die("데이터베이스 연결 실패: " . $e->getMessage());
    }
} 