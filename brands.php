<?php
// brands.php
include 'db_connection.php';
session_start();
?>
<!DOCTYPE html>
<html lang="zh-TW">
<head>
    <meta charset="UTF-8">
    <title>所有品牌 - 汽車比較系統</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Noto+Sans+TC&display=swap" rel="stylesheet">
    <!-- 自訂 CSS -->
    <style>
        body {
            font-family: 'Noto Sans TC', sans-serif;
        }
        .brand-card {
            transition: transform 0.2s;
        }
        .brand-card:hover {
            transform: scale(1.05);
        }
        .brand-logo {
            height: 150px;
            object-fit: contain;
        }
        .footer {
            background-color: #f8f9fa;
            padding: 20px 0;
        }
    </style>
</head>
<body>
    <?php include 'navbar.php'; ?> <!-- 引入導航欄 -->

    <!-- 主要內容 -->
    <div class="container mt-5 pt-5">
        <h1 class="mb-4 text-center">所有品牌</h1>
        <div class="row">
            <?php
            // 獲取所有品牌
            $sql = "SELECT * FROM brands ORDER BY name ASC";
            $result = $conn->query($sql);

            if ($result->num_rows > 0) {
                while($brand = $result->fetch_assoc()) {
                    echo "<div class='col-md-3 mb-4'>";
                    echo "<div class='card brand-card h-100'>";
                    
                    // 假設每個品牌有一個 logo 圖片，存放在 'images/brands/' 目錄，文件名為 brand_id.png
                    $logoPath = "images/brands/" . $brand['id'] . ".png";
                    if (!file_exists($logoPath)) {
                        $logoPath = "images/brands/default.png"; // 預設圖片
                    }

                    echo "<img src='" . htmlspecialchars($logoPath) . "' class='card-img-top brand-logo' alt='" . htmlspecialchars($brand['name']) . " Logo'>";
                    echo "<div class='card-body d-flex flex-column'>";
                    echo "<h5 class='card-title'>" . htmlspecialchars($brand['name']) . "</h5>";
                    // echo "<p class='card-text'>" . htmlspecialchars($brand['description']) . "</p>";
                    echo "<a href='brand_cars.php?brand_id=" . $brand['id'] . "' class='btn btn-primary mt-auto'>選擇品牌</a>"; // 修改為 brand_cars.php
                    echo "</div>";
                    echo "</div>";
                    echo "</div>";
                }
            } else {
                echo "<p>目前沒有任何品牌資料。</p>";
            }
            ?>
        </div>
    </div>

    <!-- 腳註 -->
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
