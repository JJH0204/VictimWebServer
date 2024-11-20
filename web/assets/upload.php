<?php
// 파일 업로드 취약점이 있는 코드
$target_dir = "uploads/";
$target_file = $target_dir . basename($_FILES["fileToUpload"]["name"]);

if (move_uploaded_file($_FILES["fileToUpload"]["tmp_name"], $target_file)) {
    echo "파일이 업로드되었습니다.";
} else {
    echo "업로드 실패!";
}
?> 