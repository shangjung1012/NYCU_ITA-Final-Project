<?php
// brand_cars.php
session_start();

// 檢查是否選擇了有效的 brand_id
if (!isset($_GET['brand_id']) || !is_numeric($_GET['brand_id'])) {
    echo "未選擇任何品牌。<br><a href='brands.php' class='btn btn-primary mt-3'>返回所有品牌</a>";
    exit();
}

$brand_id = intval($_GET['brand_id']);

include 'db_connection.php';

// 獲取品牌名稱
$stmt = $conn->prepare("SELECT name FROM brands WHERE id = ?");
$stmt->bind_param("i", $brand_id);
$stmt->execute();
$result = $stmt->get_result();
if ($result->num_rows === 0) {
    echo "選擇的品牌不存在。<br><a href='brands.php' class='btn btn-primary mt-3'>返回所有品牌</a>";
    exit();
}
$brand = $result->fetch_assoc();
$brand_name = htmlspecialchars($brand['name']);
$stmt->close();

// 處理篩選和排序參數
$year_filter = isset($_GET['year']) && is_numeric($_GET['year']) ? intval($_GET['year']) : '';
$sort_option = isset($_GET['sort']) ? $_GET['sort'] : '';

// 獲取該品牌的所有車輛，根據篩選和排序
$sql = "SELECT variants.*, models.model_name, models.year 
        FROM variants 
        JOIN models ON variants.model_id = models.id 
        WHERE models.brand_id = ?";

$params = [$brand_id];
$types = "i";

if ($year_filter) {
    $sql .= " AND models.year = ?";
    $params[] = $year_filter;
    $types .= "i";
}

// 添加排序條件
switch ($sort_option) {
    case 'price_asc':
        $sql .= " ORDER BY variants.price ASC";
        break;
    case 'price_desc':
        $sql .= " ORDER BY variants.price DESC";
        break;
    case 'year_asc':
        $sql .= " ORDER BY models.year ASC";
        break;
    case 'year_desc':
        $sql .= " ORDER BY models.year DESC";
        break;
    default:
        $sql .= " ORDER BY models.model_name ASC";
        break;
}

$stmt = $conn->prepare($sql);
if ($stmt === false) {
    die("準備查詢失敗: " . htmlspecialchars($conn->error));
}

// 使用動態參數綁定
$stmt->bind_param($types, ...$params);
$stmt->execute();
$result_vehicles = $stmt->get_result();
$vehicles = [];
while ($row = $result_vehicles->fetch_assoc()) {
    $vehicles[] = $row;
}
$stmt->close();

// 獲取所有品牌（用於新增車輛時的選擇）
$brands = [];
$sql_brands = "SELECT * FROM brands ORDER BY name ASC";
$result_brands = $conn->query($sql_brands);
if ($result_brands->num_rows > 0) {
    while ($row = $result_brands->fetch_assoc()) {
        $brands[] = $row;
    }
}
$conn->close();
?>
<!DOCTYPE html>
<html lang="zh-TW">
<head>
    <meta charset="UTF-8">
    <title><?= $brand_name ?> - 所有車款 - 汽車比較系統</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Noto+Sans+TC&display=swap" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
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
        .table-responsive {
            margin-top: 20px;
        }
        .btn-add {
            margin-top: 5px;
        }
        .filter-sort-form {
            margin-bottom: 20px;
        }
    </style>
</head>
<body>
    <?php
    $current_page = 'brands';
    include 'navbar.php';
    ?>
    
    <div class="container mt-5 pt-5">
        <h1 class="mb-4 text-center"><?= $brand_name ?> - 所有車輛</h1>
        <a href="brands.php" class="btn btn-secondary mb-4">返回所有品牌</a>
        
        <!-- 篩選和排序表單 -->
        <form method="GET" action="brand_cars.php" class="row g-3 filter-sort-form">
            <input type="hidden" name="brand_id" value="<?= htmlspecialchars($brand_id) ?>">
            <div class="col-md-4">
                <label for="year" class="form-label">年份篩選</label>
                <select id="year" name="year" class="form-select">
                    <option value="">所有年份</option>
                    <?php
                    // 獲取該品牌所有可用的年份
                    include 'db_connection.php';
                    $stmt_year = $conn->prepare("SELECT DISTINCT year FROM models WHERE brand_id = ? ORDER BY year DESC");
                    if ($stmt_year === false) {
                        die("準備查詢年份失敗: " . htmlspecialchars($conn->error));
                    }
                    $stmt_year->bind_param("i", $brand_id);
                    $stmt_year->execute();
                    $result_year = $stmt_year->get_result();
                    while ($row_year = $result_year->fetch_assoc()) {
                        $selected = ($year_filter == $row_year['year']) ? 'selected' : '';
                        echo "<option value='" . htmlspecialchars($row_year['year']) . "' $selected>" . htmlspecialchars($row_year['year']) . "</option>";
                    }
                    $stmt_year->close();
                    $conn->close();
                    ?>
                </select>
            </div>
            <div class="col-md-4">
                <label for="sort" class="form-label">排序方式</label>
                <select id="sort" name="sort" class="form-select">
                    <option value="year_asc" <?= ($sort_option == 'year_asc') ? 'selected' : ''; ?>>年份遞增</option>
                    <option value="year_desc" <?= ($sort_option == 'year_desc') ? 'selected' : ''; ?>>年份遞減</option>
                    <option value="price_asc" <?= ($sort_option == 'price_asc') ? 'selected' : ''; ?>>價格遞增</option>
                    <option value="price_desc" <?= ($sort_option == 'price_desc') ? 'selected' : ''; ?>>價格遞減</option>
                </select>
            </div>
            <div class="col-md-4 d-flex align-items-end">
                <button type="submit" class="btn btn-primary w-100">應用篩選與排序</button>
            </div>
        </form>
        
        <div class="table-responsive">
            <table class="table table-striped table-hover">
                <thead class="table-primary">
                    <tr>
                        <th>車型名稱</th>
                        <th>年份</th>
                        <th>組態名稱</th>
                        <th>價格 (萬)</th>
                        <th>車體類型</th>
                        <th>引擎排氣量 (cc)</th>
                        <th>馬力</th>
                        <th>燃料類型</th>
                        <th>操作</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (count($vehicles) > 0): ?>
                        <?php foreach ($vehicles as $vehicle): ?>
                            <tr>
                                <td><?= htmlspecialchars($vehicle['model_name']) ?></td>
                                <td><?= htmlspecialchars($vehicle['year']) ?></td>
                                <td><?= htmlspecialchars($vehicle['trim_name']) ?></td>
                                
                                <!-- 處理價格為 0 的情況 -->
                                <td><?= ($vehicle['price'] == 0) ? '售價未公佈' : htmlspecialchars($vehicle['price']) ?></td>
                                
                                <td><?= htmlspecialchars($vehicle['body_type']) ?></td>
                                <td><?= htmlspecialchars($vehicle['engine_cc']) ?></td>
                                <td><?= htmlspecialchars($vehicle['horsepower']) ?></td>
                                <td><?= htmlspecialchars($vehicle['fuel_type']) ?></td>
                                
                                <td>
                                    <!-- 加入比較按鈕 -->
                                    <?php
                                    $is_added_compare = isset($_SESSION['compare_list']) && in_array($vehicle['id'], $_SESSION['compare_list']);
                                    $is_favorited = isset($_SESSION['favorites_list']) && in_array($vehicle['id'], $_SESSION['favorites_list']);
                                    ?>
                                    <?php if ($is_added_compare): ?>
                                        <button class="btn btn-success btn-sm" disabled>已加入比較</button>
                                    <?php else: ?>
                                        <button class="btn btn-primary btn-sm btn-add-compare" data-id="<?= htmlspecialchars($vehicle['id']) ?>">加入比較</button>
                                    <?php endif; ?>
                                    
                                    <?php if (isset($_SESSION['role']) && $_SESSION['role'] === 'user'): ?>
                                        <?php if ($is_favorited): ?>
                                            <button class="btn btn-warning btn-sm" disabled>已加入最愛</button>
                                        <?php else: ?>
                                            <button class="btn btn-outline-warning btn-sm btn-add-favorite" data-id="<?= htmlspecialchars($vehicle['id']) ?>">加入最愛</button>
                                        <?php endif; ?>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="9" class="text-center">此品牌目前沒有任何車款資料。</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
    
    <!-- Footer -->
    <footer class="footer mt-5">
        <div class="container text-center">
            <p class="mb-0">&copy; 2024 汽車比較系統. 版權所有.</p>
        </div>
    </footer>
    
    <!-- jQuery 和 Bootstrap JS -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        $(document).ready(function() {
            // 加入比較功能
            $('.btn-add-compare').click(function() {
                var variantId = $(this).data('id');
                var button = $(this);
                
                $.ajax({
                    url: 'add_compare.php',
                    type: 'POST',
                    data: { variant_id: variantId },
                    success: function(response) {
                        if (response === 'success') {
                            button.removeClass('btn-primary').addClass('btn-success').text('已加入比較').prop('disabled', true);
                            alert("車輛已成功加入比較列表。");
                        } else if (response === 'limit') {
                            alert("最多只能比較四輛車。");
                        } else if (response === 'exists') {
                            alert("此車款已加入比較列表。");
                            button.removeClass('btn-primary').addClass('btn-success').text('已加入比較').prop('disabled', true);
                        } else {
                            // alert("加入比較時出現未知錯誤。");
                            alert("請先登入才能進行比較。");
                        }
                    },
                    error: function() {
                        alert("加入比較時出現錯誤，請稍後再試。");
                    }
                });
            });
            
            <?php if (isset($_SESSION['role']) && $_SESSION['role'] === 'user'): ?>
                // 加入最愛功能
                $('.btn-add-favorite').click(function() {
                    var variantId = $(this).data('id');
                    var button = $(this);
                    
                    $.ajax({
                        url: 'add_favorite.php',
                        type: 'POST',
                        data: { variant_id: variantId },
                        success: function(response) {
                            if (response === 'success') {
                                button.removeClass('btn-outline-warning').addClass('btn-warning').text('已加入最愛').prop('disabled', true);
                                alert("車輛已成功加入最愛列表。");
                            } else if (response === 'exists') {
                                alert("此車款已加入最愛列表。");
                                button.removeClass('btn-outline-warning').addClass('btn-warning').text('已加入最愛').prop('disabled', true);
                            } else {
                                alert("加入最愛時出現未知錯誤。");
                            }
                        },
                        error: function() {
                            alert("加入最愛時出現錯誤，請稍後再試。");
                        }
                    });
                });
            <?php endif; ?>
        });
    </script>
</body>
</html>