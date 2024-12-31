<?php
// index.php
session_start();

// 設定當前頁面
$current_page = 'home';
?>
<!DOCTYPE html>
<html lang="zh-TW">
<head>
    <meta charset="UTF-8">
    <title>汽車比較查詢系統</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Noto+Sans+TC&display=swap" rel="stylesheet">
    <!-- 自訂 CSS -->
    <style>
        body {
            font-family: 'Noto Sans TC', sans-serif;
        }
        .hero {
            background-image: url('your-hero-image.jpg'); /* 替換為你的英雄圖片 */
            background-size: cover;
            background-position: center;
            color: white;
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            text-align: center;
            position: relative;
        }
        .hero::before {
            content: "";
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.5); /* 半透明遮罩 */
            z-index: 1;
        }
        .hero-content {
            position: relative;
            z-index: 2;
        }
        .features {
            padding: 60px 0;
        }
        .feature-icon {
            font-size: 50px;
            color: #007bff;
        }
        .footer {
            background-color: #f8f9fa;
            padding: 20px 0;
        }
    </style>
</head>
<body>
    <?php include 'navbar.php'; ?> <!-- 引入導航欄 -->

    <!-- 英雄區 -->
    <section class="hero">
        <div class="hero-content">
            <div class="container">
                <h1 class="display-4">快速比較您心儀的車款</h1>
                <p class="lead">選擇您喜愛的品牌、車系和車款，輕鬆比較多項車輛參數，幫助您做出最佳選擇。</p>
                <a href="compare_selection.php" class="btn btn-primary btn-lg me-3">開始比較</a>
                <a href="brands.php" class="btn btn-secondary btn-lg">查看所有品牌</a> <!-- 新增的按鈕 -->
            </div>
        </div>
    </section>

    <!-- 功能介紹區 -->
    <section class="features">
        <div class="container">
            <div class="row text-center">
                <div class="col-md-4 mb-4">
                    <div class="feature-icon mb-3">
                        <i class="bi bi-speedometer2"></i> <!-- Bootstrap Icons -->
                    </div>
                    <h3>多項參數比較</h3>
                    <p>從價格、馬力、引擎排氣量等多項指標，全面比較不同車款。</p>
                </div>
                <div class="col-md-4 mb-4">
                    <div class="feature-icon mb-3">
                        <i class="bi bi-graph-up"></i>
                    </div>
                    <h3>即時數據更新</h3>
                    <p>所有車輛資料即時更新，確保資訊的準確性與時效性。</p>
                </div>
                <div class="col-md-4 mb-4">
                    <div class="feature-icon mb-3">
                        <i class="bi bi-people"></i>
                    </div>
                    <h3>用戶友好界面</h3>
                    <p>直觀易用的介面設計，讓您輕鬆完成比較過程。</p>
                </div>
            </div>
        </div>
    </section>

    <!-- 腳註 -->
    <footer class="footer">
        <div class="container text-center">
            <p class="mb-0">&copy; 2024 汽車比較系統. 版權所有.</p>
        </div>
    </footer>

    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
</body>
</html>
