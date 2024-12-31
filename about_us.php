<?php
// about_us.php
session_start();

// 檢查是否登入
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="zh-TW">
<head>
    <meta charset="UTF-8">
    <title>關於我們 - 汽車比較系統</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Noto+Sans+TC&display=swap" rel="stylesheet">
    <!-- 自訂 CSS -->
    <style>
        body {
            font-family: 'Noto Sans TC', sans-serif;
            padding-top: 70px; /* 確保內容不被固定導航欄遮擋 */
        }
        .footer {
            background-color: #f8f9fa;
            padding: 20px 0;
            margin-top: 40px;
        }
        .team-members {
            list-style-type: none;
            padding: 0;
        }
        .team-members li {
            background: #fff;
            margin: 5px 0;
            padding: 10px;
            border: 1px solid #dee2e6;
            border-radius: 4px;
        }
        .team-photo {
            max-width:65%;
            height: auto;
            border-radius: 8px;
            margin-bottom: 20px;
        }
    </style>
</head>
<body>
    <?php include 'navbar.php'; ?>

    <!-- 主要內容 -->
    <div class="container mt-5 pt-5">
        <h1 class="mb-4 text-center">關於我們</h1>

        <div class="row">
            <div class="col-md-12">
                <h3>課程名稱</h3>
                <p>Internet Technology and Applications</p>

                <h3>專案名稱</h3>
                <p>Comprehensive Car Comparison Platform</p>

                <h3>團隊成員</h3>
                <ul class="team-members">
                    <li>蔡尚融</li>
                    <li>林幼馨</li>
                    <li>陳宥臻</li>
                </ul>
            </div>
        </div>
    </div>

    <!-- 腳註 -->
    <footer class="footer">
        <div class="container text-center">
            <p class="mb-0">&copy; 版權所有 翻印不咎.</p>
        </div>
    </footer>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>