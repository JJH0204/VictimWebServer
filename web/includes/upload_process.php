<?php
// 모든 출력 버퍼링 시작
ob_start();

require_once 'auth.php';
requireLogin();

// 상대 경로로 업로드 디렉토리 설정 (수정)
define('UPLOAD_DIR', '/var/www/html/service/share/');

// 기본 설정 (PHP 설정에 맞춤)
$max_size = 2 * 1024 * 1024; // 2MB로 수정

// 디버깅을 위한 에러 로깅 활성화
ini_set('display_errors', 1);
error_reporting(E_ALL);

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

        // 업로드 디렉토리 확인 및 생성 (권한 수정)
        if (!file_exists(UPLOAD_DIR)) {
            if (!mkdir(UPLOAD_DIR, 0777, true)) {
                throw new Exception("업로드 디렉토리를 생성할 수 없습니다.");
            }
            chmod(UPLOAD_DIR, 0777); // 임시로 모든 권한 부여
            $response['logs'][] = "업로드 디렉토리 생성됨: " . UPLOAD_DIR;
        }

        // 파일 업로드 확인
        if (!isset($_FILES["fileToUpload"])) {
            throw new Exception("파일이 선택되지 않았습니다.");
        }

        $file = $_FILES["fileToUpload"];
        
        // 업로드 에러 확인
        if ($file['error'] !== UPLOAD_ERR_OK) {
            $upload_errors = [
                UPLOAD_ERR_INI_SIZE => '파일이 PHP의 upload_max_filesize(2MB)를 초과했습니다.',
                UPLOAD_ERR_FORM_SIZE => '파일이 HTML 폼에서 지정한 MAX_FILE_SIZE를 초과했습니다.',
                UPLOAD_ERR_PARTIAL => '파일이 일부만 업로드되었습니다.',
                UPLOAD_ERR_NO_FILE => '파일이 업로드되지 않았습니다.',
                UPLOAD_ERR_NO_TMP_DIR => '임시 폴더가 없습니다.',
                UPLOAD_ERR_CANT_WRITE => '디스크에 파일을 쓸 수 없습니다.',
                UPLOAD_ERR_EXTENSION => 'PHP 확장기능이 파일 업로드를 중지했습니다.'
            ];
            throw new Exception($upload_errors[$file['error']] ?? '알 수 없는 업로드 오류가 발생했습니다.');
        }

        // 파일 정보 로깅
        $response['logs'][] = "파일 정보: " . json_encode([
            'name' => $file['name'],
            'type' => $file['type'],
            'size' => $file['size'] . " bytes",
            'tmp_name' => $file['tmp_name']
        ], JSON_UNESCAPED_UNICODE);
        
        // 파일 크기 검사
        if ($file["size"] > $max_size) {
            throw new Exception("파일 크기가 너무 큽니다. (제한: 2MB)");
        }
        $response['logs'][] = "파일 크기 검사 통과: " . number_format($file["size"]/1024/1024, 2) . "MB";

        // 파일명 처리 (취약점 의도적 포함)
        $target_file = UPLOAD_DIR . basename($file["name"]);
        $response['logs'][] = "저장 경로: " . $target_file;

        // 파일 이동
        if (!move_uploaded_file($file["tmp_name"], $target_file)) {
            $response['logs'][] = "임시 파일 경로: " . $file["tmp_name"];
            $response['logs'][] = "대상 파일 경로: " . $target_file;
            $response['logs'][] = "현재 PHP 실행 사용자: " . exec('whoami');
            $response['logs'][] = "디렉토리 권한: " . substr(sprintf('%o', fileperms(UPLOAD_DIR)), -4);
            throw new Exception("파일 업로드 실패 - 권한을 확인하세요.");
        }
        $response['logs'][] = "파일 이동 완료";

        // 파일 권한 설정
        chmod($target_file, 0666); // 읽기/쓰기 권한 부여
        $response['logs'][] = "파일 권한 설정 완료";

        // 성공 응답
        $response['success'] = true;
        $response['message'] = "파일이 성공적으로 업로드되었습니다.";
        $response['filepath'] = $target_file;

    } catch (Exception $e) {
        // 이전 출력 버퍼 제거
        ob_clean();
        
        http_response_code(400);
        $response['success'] = false;
        $response['message'] = $e->getMessage();
        $response['logs'][] = "오류 발생: " . $e->getMessage();
    }

    // JSON 응답 전송
    header('Content-Type: application/json');
    echo json_encode($response, JSON_UNESCAPED_UNICODE);
}

// 출력 버퍼 종료 및 전송
ob_end_flush();
?>
