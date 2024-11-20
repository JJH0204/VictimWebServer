<?php
// 파일 업로드 제한 설정
ini_set('upload_max_filesize', '20M');
ini_set('post_max_size', '20M');

// 모든 출력 버퍼링 시작
ob_start();

$response = [
    'success' => false,
    'message' => '',
    'logs' => []
];

try {
    // 파일 업로드 확인
    if (!isset($_FILES["fileToUpload"]) || !is_uploaded_file($_FILES["fileToUpload"]["tmp_name"])) {
        throw new Exception("파일이 정상적으로 업로드되지 않았습니다.");
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
}

// JSON 응답 전송
header('Content-Type: application/json');
echo json_encode($response, JSON_UNESCAPED_UNICODE);

// 출력 버퍼 종료 및 전송
ob_end_flush();
?>
