<?php
require_once '../includes/auth.php';
requireLogin();
?>
<!DOCTYPE html>
<html lang="ko">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>파일 업로드</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Noto Sans KR', sans-serif;
            background-color: #f5f5f5;
            line-height: 1.6;
            color: #333;
        }

        .container {
            max-width: 800px;
            margin: 0 auto;
            padding: 20px;
        }

        nav {
            background-color: #2c3e50;
            padding: 1rem;
            margin-bottom: 2rem;
        }

        nav a {
            color: white;
            text-decoration: none;
            padding: 0.5rem 1rem;
            border-radius: 4px;
            transition: background-color 0.3s;
        }

        nav a:hover {
            background-color: #34495e;
        }

        .upload-section {
            background-color: white;
            padding: 2rem;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }

        h2 {
            color: #2c3e50;
            margin-bottom: 1.5rem;
            text-align: center;
        }

        .warning {
            background-color: #fff3cd;
            color: #856404;
            padding: 1rem;
            border-radius: 4px;
            margin-bottom: 1.5rem;
            text-align: center;
            border: 1px solid #ffeeba;
        }

        #uploadForm {
            display: flex;
            flex-direction: column;
            gap: 1rem;
            align-items: center;
        }

        .file-input-wrapper {
            width: 100%;
            max-width: 400px;
            position: relative;
        }

        #fileToUpload {
            width: 100%;
            padding: 1rem;
            border: 2px dashed #2c3e50;
            border-radius: 4px;
            cursor: pointer;
            background-color: #f8f9fa;
        }

        .submit-btn {
            background-color: #3498db;
            color: white;
            border: none;
            padding: 0.8rem 2rem;
            border-radius: 4px;
            cursor: pointer;
            font-size: 1rem;
            transition: all 0.3s ease;
            min-width: 200px;
        }

        .submit-btn:hover {
            background-color: #2980b9;
            transform: translateY(-2px);
        }

        .submit-btn:disabled {
            background-color: #95a5a6;
            cursor: not-allowed;
            transform: none;
        }

        .upload-result {
            margin-top: 2rem;
            padding: 1rem;
            border-radius: 4px;
            background-color: #f8f9fa;
            border: 1px solid #dee2e6;
        }

        .upload-result pre {
            margin: 0;
            white-space: pre-wrap;
            word-wrap: break-word;
            font-family: monospace;
            font-size: 0.9rem;
            line-height: 1.4;
            color: #495057;
        }

        @media (max-width: 768px) {
            .container {
                padding: 10px;
            }

            .upload-section {
                padding: 1rem;
            }

            .submit-btn {
                width: 100%;
                max-width: 400px;
            }
        }
    </style>
</head>
<body>
    <nav>
        <div class="container">
            <a href="../includes/logout.php">로그아웃</a>
        </div>
    </nav>

    <div class="container">
        <div class="upload-section">
            <h2>파일 업로드</h2>

            <form id="uploadForm" enctype="multipart/form-data">
                <div class="file-input-wrapper">
                    <input type="file" name="fileToUpload" id="fileToUpload" required>
                </div>
                <button type="submit" class="submit-btn">파일 업로드</button>
            </form>

            <div id="uploadResult" class="upload-result" style="display: none;">
                <pre></pre>
            </div>
        </div>
    </div>

    <script>
        document.getElementById('uploadForm').addEventListener('submit', async function(e) {
            e.preventDefault();
            
            const fileInput = document.getElementById('fileToUpload');
            const submitBtn = document.querySelector('.submit-btn');
            const resultDiv = document.getElementById('uploadResult');
            const resultPre = resultDiv.querySelector('pre');
            
            // 파일 선택 및 크기 확인
            if (!fileInput.files || fileInput.files.length === 0) {
                alert('파일을 선택해주세요.');
                return;
            }

            const maxSize = 20 * 1024 * 1024; // 20MB
            if (fileInput.files[0].size > maxSize) {
                alert('파일 크기가 20MB를 초과합니다.');
                return;
            }

            try {
                submitBtn.disabled = true;
                submitBtn.textContent = '업로드 중...';

                const formData = new FormData();
                formData.append('fileToUpload', fileInput.files[0]);

                const response = await fetch('../includes/upload_process.php', {
                    method: 'POST',
                    body: formData
                });

                const data = await response.json();
                
                resultPre.textContent = data.success ? 
                    `업로드 성공: ${data.message}` : 
                    `업로드 실패: ${data.message}`;
                resultDiv.style.display = 'block';

            } catch (error) {
                resultPre.textContent = `업로드 실패: ${error.message}`;
                resultDiv.style.display = 'block';
            } finally {
                submitBtn.disabled = false;
                submitBtn.textContent = '파일 업로드';
            }
        });

        // 파일 선택 이벤트
        document.getElementById('fileToUpload').addEventListener('change', function(e) {
            const submitBtn = document.querySelector('.submit-btn');
            const maxSize = 20 * 1024 * 1024; // 20MB
            
            if (this.files && this.files[0]) {
                const file = this.files[0];
                if (file.size > maxSize) {
                    alert('파일 크기가 20MB를 초과합니다.');
                    this.value = '';
                    submitBtn.textContent = '파일 업로드';
                    return;
                }
                submitBtn.textContent = `'${file.name}' 업로드`;
            } else {
                submitBtn.textContent = '파일 업로드';
            }
        });
    </script>
</body>
</html> 