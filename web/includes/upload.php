<?php
// 모든 출력 버퍼링 시작
ob_start();

require_once 'auth.php';
requireLogin();

// 파일 업로드 디렉토리 설정
define('UPLOAD_DIR', '/var/www/html/share/');

// 기본 설정
$max_size = 5 * 1024 * 1024; // 5MB

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    try {
        // 이전 출력 버퍼 제거
        ob_clean();
        
        $response = [
            'success' => false,
            'message' => '',
            'logs' => [],
            'filepath' => ''
        ];

        if (!isset($_FILES["fileToUpload"])) {
            throw new Exception("파일이 선택되지 않았습니다.");
        }

        $file = $_FILES["fileToUpload"];
        $response['logs'][] = "파일 정보: " . json_encode([
            'name' => $file['name'],
            'type' => $file['type'],
            'size' => $file['size'] . " bytes",
            'tmp_name' => $file['tmp_name']
        ]);
        
        // 파일 크기 검사
        if ($file["size"] > $max_size) {
            throw new Exception("파일 크기가 너무 큽니다. (제한: " . ($max_size/1024/1024) . "MB)");
        }
        $response['logs'][] = "파일 크기 검사 통과: " . number_format($file["size"]/1024/1024, 2) . "MB";

        // 원본 파일명 사용
        $target_file = UPLOAD_DIR . basename($file["name"]);
        $response['logs'][] = "저장 경로: " . $target_file;

        // 파일 이동
        if (!move_uploaded_file($file["tmp_name"], $target_file)) {
            throw new Exception("파일 업로드 실패 - 권한을 확인하세요.");
        }
        $response['logs'][] = "파일 이동 완료";

        $response['success'] = true;
        $response['message'] = "파일이 성공적으로 업로드되었습니다.";
        $response['filepath'] = $target_file;

        header('Content-Type: application/json');
        echo json_encode($response);
        
    } catch (Exception $e) {
        // 이전 출력 버퍼 제거
        ob_clean();
        
        http_response_code(400);
        $response['success'] = false;
        $response['message'] = $e->getMessage();
        echo json_encode($response);
    }
}

// 출력 버퍼 종료 및 전송
ob_end_flush();
?> 