<?php
// 디버깅을 위한 에러 로깅 활성화
ini_set('display_errors', 1);
error_reporting(E_ALL);

// 요청 정보 로깅
error_log("Request Method: " . $_SERVER['REQUEST_METHOD']);
error_log("Files: " . print_r($_FILES, true));
error_log("Post: " . print_r($_POST, true));

// 모든 출력 버퍼링 시작
ob_start();

try {
    // 파일 업로드 확인
    if (!isset($_FILES["fileToUpload"]) || $_FILES["fileToUpload"]["error"] !== UPLOAD_ERR_OK) {
        throw new Exception("파일 업로드에 실패했습니다.");
    }

    $file = $_FILES["fileToUpload"];
    
    // 업로드 디렉토리 설정
    $uploadDir = __DIR__ . '/../share/';
    
    // 업로드 디렉토리 확인 및 생성
    if (!file_exists($uploadDir)) {
        if (!mkdir($uploadDir, 0777, true)) {
            throw new Exception("업로드 디렉토리를 생성할 수 없습니다.");
        }
    }

    // 파일 정보 로깅
    $response = [
        'success' => false,
        'message' => '',
        'logs' => [],
        'filepath' => ''
    ];

    $response['logs'][] = "파일 정보: " . json_encode([
        'name' => $file['name'],
        'type' => $file['type'],
        'size' => $file['size'] . " bytes",
        'tmp_name' => $file['tmp_name']
    ]);

    // 파일 이동
    $targetPath = $uploadDir . basename($file["name"]);
    if (!move_uploaded_file($file["tmp_name"], $targetPath)) {
        throw new Exception("파일 이동에 실패했습니다.");
    }

    // 성공 응답
    $response['success'] = true;
    $response['message'] = "파일이 성공적으로 업로드되었습니다.";
    $response['filepath'] = $targetPath;

} catch (Exception $e) {
    http_response_code(400);
    $response['success'] = false;
    $response['message'] = $e->getMessage();
    $response['logs'][] = "오류 발생: " . $e->getMessage();
}

// JSON 응답 전송
header('Content-Type: application/json');
echo json_encode($response, JSON_UNESCAPED_UNICODE);

// 출력 버퍼 종료 및 전송
ob_end_flush();
?>
