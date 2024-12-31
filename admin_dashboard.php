<?php
// admin_dashboard.php
session_start();

// 檢查用戶是否登入且為管理員
if (!isset($_SESSION['username']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

include 'db_connection.php';
?>
<!DOCTYPE html>
<html lang="zh-TW">
<head>
    <meta charset="UTF-8">
    <title>管理員後台 - 汽車比較系統</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            font-family: 'Noto Sans TC', sans-serif;
            padding-top: 70px;
        }
        .footer {
            background-color: #f8f9fa;
            padding: 20px 0;
            margin-top: 40px;
        }
    </style>
</head>
<body>
    <?php
    $current_page = 'admin_dashboard';
    include 'navbar.php';
    ?>
    
    <div class="container mt-5 pt-5">
        <h1 class="mb-4">管理員後台</h1>
        <p>在此管理所有車輛資料。</p>
        
        <!-- 管理品牌與車輛的連結 -->
        <div class="list-group">
            <a href="manage_brands.php" class="list-group-item list-group-item-action">管理品牌</a>
            <a href="manage_vehicles.php" class="list-group-item list-group-item-action">管理車輛</a>
        </div>
    </div>
    
    <!-- Footer -->
    <footer class="footer mt-5">
        <div class="container text-center">
            <p class="mb-0">&copy; 2024 汽車比較系統. 版權所有.</p>
        </div>
    </footer>
    
    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
<?php
$conn->close();
?>
