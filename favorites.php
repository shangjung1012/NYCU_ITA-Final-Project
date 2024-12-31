<?php
// favorites.php
session_start();

// 檢查是否登入且為普通用戶
if (!isset($_SESSION['username']) || $_SESSION['role'] !== 'user') {
    header("Location: login.php");
    exit();
}

include 'db_connection.php';

$user_id = $_SESSION['user_id'];

// 獲取用戶的最愛車輛
$stmt = $conn->prepare("SELECT favorites.variant_id, variants.*, models.model_name, models.year, brands.name as brand_name 
                        FROM favorites 
                        JOIN variants ON favorites.variant_id = variants.id 
                        JOIN models ON variants.model_id = models.id 
                        JOIN brands ON models.brand_id = brands.id 
                        WHERE favorites.user_id = ?
                        ORDER BY brands.name ASC, models.model_name ASC, variants.trim_name ASC");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$favorites = [];
while ($row = $result->fetch_assoc()) {
    $favorites[] = $row;
}
$stmt->close();

$conn->close();
?>
<!DOCTYPE html>
<html lang="zh-TW">
<head>
    <meta charset="UTF-8">
    <title>我的最愛 - 汽車比較系統</title>
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
    $current_page = 'favorites';
    include 'navbar.php';
    ?>
    
    <div class="container mt-5 pt-5">
        <h1 class="mb-4">我的最愛車輛</h1>
        <?php if (count($favorites) > 0): ?>
            <div class="table-responsive">
                <table class="table table-striped table-hover">
                    <thead class="table-secondary">
                        <tr>
                            <th>品牌</th>
                            <th>車型名稱</th>
                            <th>年份</th>
                            <th>配置名稱</th>
                            <th>價格 (萬)</th>
                            <th>車體類型</th>
                            <th>引擎排氣量 (cc)</th>
                            <th>馬力</th>
                            <th>燃料類型</th>
                            <th>操作</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($favorites as $car): ?>
                            <tr>
                                <td><?= htmlspecialchars($car['brand_name']) ?></td>
                                <td><?= htmlspecialchars($car['model_name']) ?></td>
                                <td><?= htmlspecialchars($car['year']) ?></td>
                                <td><?= htmlspecialchars($car['trim_name']) ?></td>
                                
                                <!-- 處理價格為 0 的情況 -->
                                <td><?= ($car['price'] == 0) ? '售價未公布' : htmlspecialchars($car['price']) ?></td>
                                
                                <td><?= htmlspecialchars($car['body_type']) ?></td>
                                <td><?= htmlspecialchars($car['engine_cc']) ?></td>
                                <td><?= htmlspecialchars($car['horsepower']) ?></td>
                                <td><?= htmlspecialchars($car['fuel_type']) ?></td>
                                
                                <td>
                                    <!-- 移除最愛按鈕 -->
                                    <form method="POST" action="remove_favorite.php" onsubmit="return confirm('確定要從最愛中移除此車輛嗎？');">
                                        <input type="hidden" name="variant_id" value="<?= htmlspecialchars($car['variant_id']) ?>">
                                        <button type="submit" class="btn btn-danger btn-sm">移除</button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php else: ?>
            <p>您尚未加入任何最愛車輛。</p>
        <?php endif; ?>
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
