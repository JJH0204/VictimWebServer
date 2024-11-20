<?php
require_once 'auth.php';
requireLogin();

// 허용된 파일 타입
$allowed_types = ['image/jpeg', 'image/png', 'image/gif'];
$max_size = 5 * 1024 * 1024; // 5MB

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    try {
        if (!isset($_FILES["fileToUpload"])) {
            throw new Exception("파일이 선택되지 않았습니다.");
        }

        $file = $_FILES["fileToUpload"];
        
        // 파일 검증
        if (!in_array($file["type"], $allowed_types)) {
            throw new Exception("허용되지 않는 파일 형식입니다.");
        }
        
        if ($file["size"] > $max_size) {
            throw new Exception("파일 크기가 너무 큽니다.");
        }

        // 안전한 파일명 생성
        $file_ext = pathinfo($file["name"], PATHINFO_EXTENSION);
        $new_filename = uniqid() . '.' . $file_ext;
        $target_file = UPLOAD_DIR . $new_filename;

        if (!move_uploaded_file($file["tmp_name"], $target_file)) {
            throw new Exception("파일 업로드 실패");
        }

        echo json_encode(["success" => true, "message" => "파일이 성공적으로 업로드되었습니다."]);
    } catch (Exception $e) {
        http_response_code(400);
        echo json_encode(["success" => false, "message" => $e->getMessage()]);
    }
}
?> 