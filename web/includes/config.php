<?php
define('DB_HOST', '127.0.0.1');
define('DB_NAME', 'vulnerable_db');
define('DB_USER', 'root');
define('DB_PASS', '');
define('UPLOAD_DIR', __DIR__ . '/../uploads/');

function getDBConnection() {
    try {
        $conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
        
        if ($conn->connect_error) {
            error_log("DB 연결 실패: " . $conn->connect_error);
            throw new Exception("데이터베이스 연결에 실패했습니다.");
        }
        
        error_log("DB 연결 성공 - Host: " . DB_HOST . ", Database: " . DB_NAME);
        return $conn;
        
    } catch(Exception $e) {
        error_log("DB 연결 오류: " . $e->getMessage());
        throw new Exception("데이터베이스 연결 실패: " . $e->getMessage());
    }
}
