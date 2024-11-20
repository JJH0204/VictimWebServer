<?php
require_once 'auth.php';
requireLogin();

// 파일 업로드 디렉토리 설정
define('UPLOAD_DIR', '/var/www/share/');

// 기본 설정
$max_size = 5 * 1024 * 1024; // 5MB

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    try {
        if (!isset($_FILES["fileToUpload"])) {
            throw new Exception("파일이 선택되지 않았습니다.");
        }

        $file = $_FILES["fileToUpload"];
        
        // 파일 크기만 검사 (취약점 1: 파일 타입 검사 없음)
        if ($file["size"] > $max_size) {
            throw new Exception("파일 크기가 너무 큽니다.");
        }

        // 취약점 2: 원본 파일명 그대로 사용
        $target_file = UPLOAD_DIR . basename($file["name"]);

        // 취약점 3: 파일 이동만 수행
        if (!move_uploaded_file($file["tmp_name"], $target_file)) {
            throw new Exception("파일 업로드 실패");
        }

        echo json_encode([
            "success" => true, 
            "message" => "파일이 업로드되었습니다.",
            "filepath" => $target_file // 취약점 4: 파일 경로 노출
        ]);
    } catch (Exception $e) {
        http_response_code(400);
        echo json_encode(["success" => false, "message" => $e->getMessage()]);
    }
}
?> 