<?php
require_once '../includes/auth.php';
requireLogin();
?>
<!DOCTYPE html>
<html lang="ko">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>파일 업로드 실습</title>
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

            <form id="uploadForm" action="../includes/upload_process.php" method="post" enctype="multipart/form-data">
                <div class="file-input-wrapper">
                    <input type="file" name="fileToUpload" id="fileToUpload">
                </div>
                <button type="submit" class="submit-btn" name="submit">파일 업로드</button>
            </form>

            <div id="uploadResult" class="upload-result" style="display: none;">
                <pre></pre>
            </div>
        </div>
    </div>

    <script>
        document.getElementById('uploadForm').addEventListener('submit', function(e) {
            e.preventDefault();
            
            const submitBtn = document.querySelector('.submit-btn');
            const resultDiv = document.getElementById('uploadResult');
            const resultPre = resultDiv.querySelector('pre');
            
            submitBtn.disabled = true;
            submitBtn.textContent = '업로드 중...';
            
            const formData = new FormData(this);
            
            fetch('../includes/upload_process.php', {
                method: 'POST',
                body: formData
            })
            .then(response => {
                if (!response.ok) {
                    throw new Error('Network response was not ok');
                }
                return response.json();
            })
            .then(data => {
                console.log('서버 응답:', data);
                
                let logText = '== 업로드 결과 ==\n';
                logText += `상태: ${data.success ? '성공' : '실패'}\n`;
                logText += `메시지: ${data.message}\n`;
                
                if (data.logs && data.logs.length > 0) {
                    logText += '\n== 상세 로그 ==\n';
                    data.logs.forEach(log => {
                        logText += `${log}\n`;
                    });
                }
                
                if (data.filepath) {
                    logText += `\n저장 위치: ${data.filepath}`;
                }
                
                resultPre.textContent = logText;
                resultDiv.style.display = 'block';
            })
            .catch(error => {
                console.error('업로드 오류:', error);
                resultPre.textContent = `오류 발생: ${error.message}`;
                resultDiv.style.display = 'block';
            })
            .finally(() => {
                submitBtn.disabled = false;
                submitBtn.textContent = '파일 업로드';
            });
        });

        document.getElementById('fileToUpload').addEventListener('change', function(e) {
            const fileName = e.target.files[0]?.name;
            if (fileName) {
                console.log('선택된 파일:', fileName);
                document.querySelector('.submit-btn').textContent = `'${fileName}' 업로드`;
            } else {
                console.log('파일 선택 취소됨');
                document.querySelector('.submit-btn').textContent = '파일 업로드';
            }
        });
    </script>
</body>
</html> 