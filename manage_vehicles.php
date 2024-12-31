<?php
// manage_vehicles.php
session_start();

// 檢查用戶是否登入且為管理員
if (!isset($_SESSION['username']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

include 'db_connection.php';

// 初始化錯誤和成功訊息
$error = '';
$success = '';

// 設定分頁參數
$per_page_options = [10, 20, 50];
$per_page = isset($_GET['per_page']) && in_array(intval($_GET['per_page']), $per_page_options) ? intval($_GET['per_page']) : 10;
$page = isset($_GET['page']) && is_numeric($_GET['page']) && intval($_GET['page']) > 0 ? intval($_GET['page']) : 1;

// 計算 OFFSET
$offset = ($page - 1) * $per_page;

// 處理新增、編輯和刪除車輛
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    if ($_POST['action'] === 'add') {
        // 獲取並清理輸入
        $brand_id = intval($_POST['brand_id']);
        $model_name = trim($_POST['model_name']);
        $year = intval($_POST['year']);
        $trim_name = trim($_POST['trim_name']);
        $price = floatval($_POST['price']);
        $body_type = trim($_POST['body_type']);
        $engine_cc = trim($_POST['engine_cc']);
        $horsepower = trim($_POST['horsepower']);
        $fuel_type = trim($_POST['fuel_type']);
        
        // 檢查必填欄位是否為空
        if (empty($brand_id) || empty($model_name) || empty($year) || empty($trim_name) || empty($fuel_type)) {
            $error = "請填寫所有必填欄位。";
        } else {
            // 插入或查詢車型
            $stmt = $conn->prepare("SELECT id FROM models WHERE brand_id = ? AND model_name = ?");
            $stmt->bind_param("is", $brand_id, $model_name);
            $stmt->execute();
            $result = $stmt->get_result();
            if ($result->num_rows > 0) {
                $model = $result->fetch_assoc();
                $model_id = $model['id'];
            } else {
                // 新增車型
                $stmt_insert_model = $conn->prepare("INSERT INTO models (brand_id, model_name, year) VALUES (?, ?, ?)");
                $stmt_insert_model->bind_param("isi", $brand_id, $model_name, $year);
                if ($stmt_insert_model->execute()) {
                    $model_id = $stmt_insert_model->insert_id;
                } else {
                    $error = "新增車型時出錯: " . $stmt_insert_model->error;
                    $stmt_insert_model->close();
                    // 不需要關閉連接，因為後續還有可能操作
                    header("Location: manage_vehicles.php?error=" . urlencode($error));
                    exit();
                }
                $stmt_insert_model->close();
            }
            $stmt->close();
            
            // 插入變種
            $stmt_insert_variant = $conn->prepare("INSERT INTO variants (model_id, trim_name, price, body_type, engine_cc, horsepower, fuel_type) VALUES (?, ?, ?, ?, ?, ?, ?)");
            $stmt_insert_variant->bind_param("isdssss", $model_id, $trim_name, $price, $body_type, $engine_cc, $horsepower, $fuel_type);
            
            if ($stmt_insert_variant->execute()) {
                // 成功新增車輛
                $stmt_insert_variant->close();
                $success = "車輛已成功新增。";
            } else {
                // 新增失敗，顯示錯誤訊息
                $error = "新增車輛時出錯: " . $stmt_insert_variant->error;
                $stmt_insert_variant->close();
            }
        }
    } elseif ($_POST['action'] === 'delete' && isset($_POST['variant_id'])) {
        $variant_id = intval($_POST['variant_id']);
        
        // 刪除車輛
        $stmt = $conn->prepare("DELETE FROM variants WHERE id = ?");
        $stmt->bind_param("i", $variant_id);
        if ($stmt->execute()) {
            // 成功刪除車輛
            $stmt->close();
            $success = "車輛已成功刪除。";
        } else {
            // 刪除失敗，顯示錯誤訊息
            $error = "刪除車輛時出錯: " . $stmt->error;
            $stmt->close();
        }
    } elseif ($_POST['action'] === 'edit' && isset($_POST['variant_id'])) {
        // 編輯車輛
        $variant_id = intval($_POST['variant_id']);
        $brand_id = intval($_POST['brand_id']);
        $model_name = trim($_POST['model_name']);
        $year = intval($_POST['year']);
        $trim_name = trim($_POST['trim_name']);
        $price = floatval($_POST['price']);
        $body_type = trim($_POST['body_type']);
        $engine_cc = trim($_POST['engine_cc']);
        $horsepower = trim($_POST['horsepower']);
        $fuel_type = trim($_POST['fuel_type']);
        
        // 檢查必填欄位是否為空
        if (empty($brand_id) || empty($model_name) || empty($year) || empty($trim_name) || empty($fuel_type)) {
            $error = "請填寫所有必填欄位。";
        } else {
            // 插入或查詢車型
            $stmt = $conn->prepare("SELECT id FROM models WHERE brand_id = ? AND model_name = ?");
            $stmt->bind_param("is", $brand_id, $model_name);
            $stmt->execute();
            $result = $stmt->get_result();
            if ($result->num_rows > 0) {
                $model = $result->fetch_assoc();
                $model_id = $model['id'];
            } else {
                // 新增車型
                $stmt_insert_model = $conn->prepare("INSERT INTO models (brand_id, model_name, year) VALUES (?, ?, ?)");
                $stmt_insert_model->bind_param("isi", $brand_id, $model_name, $year);
                if ($stmt_insert_model->execute()) {
                    $model_id = $stmt_insert_model->insert_id;
                } else {
                    $error = "新增車型時出錯: " . $stmt_insert_model->error;
                    $stmt_insert_model->close();
                    header("Location: manage_vehicles.php?error=" . urlencode($error));
                    exit();
                }
                $stmt_insert_model->close();
            }
            $stmt->close();
            
            // 更新變種
            $stmt_update_variant = $conn->prepare("UPDATE variants SET model_id = ?, trim_name = ?, price = ?, body_type = ?, engine_cc = ?, horsepower = ?, fuel_type = ? WHERE id = ?");
            $stmt_update_variant->bind_param("isdssssi", $model_id, $trim_name, $price, $body_type, $engine_cc, $horsepower, $fuel_type, $variant_id);
            
            if ($stmt_update_variant->execute()) {
                // 成功更新車輛
                $stmt_update_variant->close();
                $success = "車輛已成功更新。";
            } else {
                // 更新失敗，顯示錯誤訊息
                $error = "更新車輛時出錯: " . $stmt_update_variant->error;
                $stmt_update_variant->close();
            }
        }
    }
}

// 獲取所有品牌
$brands = [];
$sql_brands = "SELECT * FROM brands ORDER BY name ASC";
$result_brands = $conn->query($sql_brands);
if ($result_brands->num_rows > 0) {
    while ($row = $result_brands->fetch_assoc()) {
        $brands[] = $row;
    }
}

// 獲取選擇的品牌ID（如果有）
$selected_brand_id = isset($_GET['brand_id']) && is_numeric($_GET['brand_id']) ? intval($_GET['brand_id']) : 0;

// 計算總車輛數量
if ($selected_brand_id > 0) {
    $stmt_count = $conn->prepare("SELECT COUNT(*) as total FROM variants 
                                   JOIN models ON variants.model_id = models.id 
                                   JOIN brands ON models.brand_id = brands.id 
                                   WHERE brands.id = ?");
    $stmt_count->bind_param("i", $selected_brand_id);
} else {
    $stmt_count = $conn->prepare("SELECT COUNT(*) as total FROM variants 
                                   JOIN models ON variants.model_id = models.id 
                                   JOIN brands ON models.brand_id = brands.id");
}

$stmt_count->execute();
$result_count = $stmt_count->get_result();
$total = 0;
if ($row = $result_count->fetch_assoc()) {
    $total = intval($row['total']);
}
$stmt_count->close();

// 計算總頁數
$total_pages = ceil($total / $per_page);

// 獲取所有車輛（如果有選擇品牌，則只顯示該品牌下的車輛）
$vehicles = [];
if ($selected_brand_id > 0) {
    $stmt_vehicles = $conn->prepare("SELECT variants.*, models.model_name, models.year, brands.id as brand_id, brands.name as brand_name 
                                     FROM variants 
                                     JOIN models ON variants.model_id = models.id 
                                     JOIN brands ON models.brand_id = brands.id 
                                     WHERE brands.id = ?
                                     ORDER BY models.model_name ASC, variants.trim_name ASC 
                                     LIMIT ? OFFSET ?");
    $stmt_vehicles->bind_param("iii", $selected_brand_id, $per_page, $offset);
} else {
    $stmt_vehicles = $conn->prepare("SELECT variants.*, models.model_name, models.year, brands.id as brand_id, brands.name as brand_name 
                                     FROM variants 
                                     JOIN models ON variants.model_id = models.id 
                                     JOIN brands ON models.brand_id = brands.id 
                                     ORDER BY brands.name ASC, models.model_name ASC, variants.trim_name ASC 
                                     LIMIT ? OFFSET ?");
    $stmt_vehicles->bind_param("ii", $per_page, $offset);
}

$stmt_vehicles->execute();
$result_vehicles = $stmt_vehicles->get_result();
if ($result_vehicles->num_rows > 0) {
    while ($row = $result_vehicles->fetch_assoc()) {
        $vehicles[] = $row;
    }
}
$stmt_vehicles->close();

$conn->close();
?>
<!DOCTYPE html>
<html lang="zh-TW">
<head>
    <meta charset="UTF-8">
    <title>管理車輛 - 管理員後台 - 汽車比較系統</title>
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
        .table-responsive {
            margin-top: 20px;
        }
        .pagination {
            justify-content: center;
        }
        /* 自訂 active 分頁按鈕的樣式 */
        .pagination .page-item.active .page-link {
            background-color: #343a40; /* 深灰色背景 */
            border-color: #343a40; /* 深灰色邊框 */
            color: #ffffff; /* 白色文字 */
        }

        /* 當滑鼠懸停在 active 分頁按鈕上時保持樣式 */
        .pagination .page-item.active .page-link:hover {
            background-color: #343a40;
            border-color: #343a40;
            color: #ffffff;
        }
    </style>
</head>
<body>
    <?php
    $current_page = 'admin_dashboard';
    include 'navbar.php';
    ?>
    
    <div class="container mt-5 pt-5">
        <h1 class="mb-4">管理車輛</h1>
        
        <!-- 顯示成功訊息 -->
        <?php if ($success): ?>
            <div class="alert alert-success"><?= htmlspecialchars($success) ?></div>
        <?php endif; ?>
        
        <!-- 顯示錯誤訊息 -->
        <?php if ($error): ?>
            <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>
        
        <!-- 品牌篩選表單 -->
        <div class="card mb-4">
            <div class="card-header">
                篩選品牌
            </div>
            <div class="card-body">
                <form method="GET" action="manage_vehicles.php" class="row g-3">
                    <div class="col-md-4">
                        <label for="brand_id_filter" class="form-label">選擇品牌</label>
                        <select class="form-select" id="brand_id_filter" name="brand_id">
                            <option value="0">所有品牌</option>
                            <?php foreach ($brands as $brand): ?>
                                <option value="<?= htmlspecialchars($brand['id']) ?>" <?= ($brand['id'] === $selected_brand_id) ? 'selected' : ''; ?>>
                                    <?= htmlspecialchars($brand['name']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label for="per_page" class="form-label">每頁顯示筆數</label>
                        <select class="form-select" id="per_page" name="per_page" onchange="this.form.submit()">
                            <?php foreach ($per_page_options as $option): ?>
                                <option value="<?= $option ?>" <?= ($per_page === $option) ? 'selected' : ''; ?>>
                                    <?= $option ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-2 align-self-end">
                        <button type="submit" class="btn btn-primary">篩選</button>
                    </div>
                </form>
            </div>
        </div>
        
        <!-- 新增車輛表單 -->
        <div class="card mb-4">
            <div class="card-header">
                新增車輛
            </div>
            <div class="card-body">
                <form method="POST" action="manage_vehicles.php">
                    <input type="hidden" name="action" value="add">
                    <div class="mb-3">
                        <label for="brand_id" class="form-label">品牌</label>
                        <select class="form-select" id="brand_id" name="brand_id" required>
                            <option value="">選擇品牌</option>
                            <?php foreach ($brands as $brand): ?>
                                <option value="<?= htmlspecialchars($brand['id']) ?>" <?= ($brand['id'] === $selected_brand_id) ? 'selected' : ''; ?>>
                                    <?= htmlspecialchars($brand['name']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="model_name" class="form-label">車型名稱</label>
                        <input type="text" class="form-control" id="model_name" name="model_name" required>
                    </div>
                    <div class="mb-3">
                        <label for="year" class="form-label">年份</label>
                        <input type="number" class="form-control" id="year" name="year" min="1900" max="2100" required>
                    </div>
                    <div class="mb-3">
                        <label for="trim_name" class="form-label">配置名稱</label>
                        <input type="text" class="form-control" id="trim_name" name="trim_name" required>
                    </div>
                    <div class="mb-3">
                        <label for="price" class="form-label">價格 (萬)</label>
                        <input type="number" class="form-control" id="price" name="price" min="0" step="0.1" required>
                    </div>
                    <div class="mb-3">
                        <label for="body_type" class="form-label">車體類型</label>
                        <input type="text" class="form-control" id="body_type" name="body_type" required>
                    </div>
                    <div class="mb-3">
                        <label for="engine_cc" class="form-label">引擎排氣量 (cc)</label>
                        <input type="text" class="form-control" id="engine_cc" name="engine_cc" required>
                    </div>
                    <div class="mb-3">
                        <label for="horsepower" class="form-label">馬力</label>
                        <input type="text" class="form-control" id="horsepower" name="horsepower" required>
                    </div>
                    <div class="mb-3">
                        <label for="fuel_type" class="form-label">燃料類型</label>
                        <input type="text" class="form-control" id="fuel_type" name="fuel_type" required>
                    </div>
                    <button type="submit" class="btn btn-primary">新增車輛</button>
                </form>
            </div>
        </div>
        
        <!-- 車輛列表 -->
        <h2>所有車輛<?= $selected_brand_id > 0 ? " - " . htmlspecialchars($brands[array_search($selected_brand_id, array_column($brands, 'id'))]['name']) : ''; ?></h2>
        <div class="table-responsive">
            <table class="table table-striped table-hover">
                <thead class="table-secondary">
                    <tr>
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
                    <?php if (count($vehicles) > 0): ?>
                        <?php foreach ($vehicles as $vehicle): ?>
                            <tr>
                                <td><?= htmlspecialchars($vehicle['model_name']) ?></td>
                                <td><?= htmlspecialchars($vehicle['year']) ?></td>
                                <td><?= htmlspecialchars($vehicle['trim_name']) ?></td>
                                <td><?= ($vehicle['price'] == 0) ? '售價未公布' : htmlspecialchars($vehicle['price']) ?></td>
                                <td><?= htmlspecialchars($vehicle['body_type']) ?></td>
                                <td><?= htmlspecialchars($vehicle['engine_cc']) ?></td>
                                <td><?= htmlspecialchars($vehicle['horsepower']) ?></td>
                                <td><?= htmlspecialchars($vehicle['fuel_type']) ?></td>
                                <td>
                                    <!-- 編輯按鈕 -->
                                    <button class="btn btn-warning btn-sm edit-btn"
                                        data-variant-id="<?= htmlspecialchars($vehicle['id']) ?>"
                                        data-brand-id="<?= htmlspecialchars($vehicle['brand_id']) ?>"
                                        data-model-name="<?= htmlspecialchars($vehicle['model_name']) ?>"
                                        data-year="<?= htmlspecialchars($vehicle['year']) ?>"
                                        data-trim-name="<?= htmlspecialchars($vehicle['trim_name']) ?>"
                                        data-price="<?= htmlspecialchars($vehicle['price']) ?>"
                                        data-body-type="<?= htmlspecialchars($vehicle['body_type']) ?>"
                                        data-engine-cc="<?= htmlspecialchars($vehicle['engine_cc']) ?>"
                                        data-horsepower="<?= htmlspecialchars($vehicle['horsepower']) ?>"
                                        data-fuel-type="<?= htmlspecialchars($vehicle['fuel_type']) ?>">編輯
                                    </button>
                                    
                                    <!-- 刪除按鈕 -->
                                    <form method="POST" action="manage_vehicles.php" style="display:inline;" onsubmit="return confirm('確定要刪除此車輛嗎？');">
                                        <input type="hidden" name="action" value="delete">
                                        <input type="hidden" name="variant_id" value="<?= htmlspecialchars($vehicle['id']) ?>">
                                        <button type="submit" class="btn btn-danger btn-sm">刪除</button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="9" class="text-center">目前沒有任何車輛資料。</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
        
        <!-- 分頁導航 -->
        <?php if ($total_pages > 1): ?>
            <nav aria-label="Page navigation">
                <ul class="pagination">
                    <!-- 上一頁 -->
                    <li class="page-item <?= ($page <= 1) ? 'disabled' : ''; ?>">
                        <a class="page-link" href="?<?= http_build_query(array_merge($_GET, ['page' => $page - 1])) ?>" aria-label="Previous">
                            <span aria-hidden="true">&laquo;</span>
                        </a>
                    </li>
                    
                    <!-- 頁碼 -->
                    <?php
                    // 顯示最多 5 個頁碼，並根據當前頁碼調整
                    $max_links = 5;
                    $start = max(1, $page - floor($max_links / 2));
                    $end = min($total_pages, $start + $max_links - 1);
                    if ($end - $start + 1 < $max_links) {
                        $start = max(1, $end - $max_links + 1);
                    }
                    for ($i = $start; $i <= $end; $i++): ?>
                        <li class="page-item <?= ($i === $page) ? 'active' : ''; ?>">
                            <a class="page-link" href="?<?= http_build_query(array_merge($_GET, ['page' => $i])) ?>"><?= $i ?></a>
                        </li>
                    <?php endfor; ?>
                    
                    <!-- 下一頁 -->
                    <li class="page-item <?= ($page >= $total_pages) ? 'disabled' : ''; ?>">
                        <a class="page-link" href="?<?= http_build_query(array_merge($_GET, ['page' => $page + 1])) ?>" aria-label="Next">
                            <span aria-hidden="true">&raquo;</span>
                        </a>
                    </li>
                </ul>
            </nav>
        <?php endif; ?>
        
        <!-- 編輯車輛模態框 -->
        <div class="modal fade" id="editVehicleModal" tabindex="-1" aria-labelledby="editVehicleModalLabel" aria-hidden="true">
          <div class="modal-dialog">
            <form method="POST" action="manage_vehicles.php">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="editVehicleModalLabel">編輯車輛</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="關閉"></button>
                    </div>
                    <div class="modal-body">
                        <input type="hidden" name="action" value="edit">
                        <input type="hidden" name="variant_id" id="edit_variant_id" value="">
                        <div class="mb-3">
                            <label for="edit_brand_id" class="form-label">品牌</label>
                            <select class="form-select" id="edit_brand_id" name="brand_id" required>
                                <option value="">選擇品牌</option>
                                <?php foreach ($brands as $brand): ?>
                                    <option value="<?= htmlspecialchars($brand['id']) ?>"><?= htmlspecialchars($brand['name']) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="edit_model_name" class="form-label">車型名稱</label>
                            <input type="text" class="form-control" id="edit_model_name" name="model_name" required>
                        </div>
                        <div class="mb-3">
                            <label for="edit_year" class="form-label">年份</label>
                            <input type="number" class="form-control" id="edit_year" name="year" min="1900" max="2100" required>
                        </div>
                        <div class="mb-3">
                            <label for="edit_trim_name" class="form-label">配置名稱</label>
                            <input type="text" class="form-control" id="edit_trim_name" name="trim_name" required>
                        </div>
                        <div class="mb-3">
                            <label for="edit_price" class="form-label">價格 (萬)</label>
                            <input type="number" class="form-control" id="edit_price" name="price" min="0" step="0.1" required>
                        </div>
                        <div class="mb-3">
                            <label for="edit_body_type" class="form-label">車體類型</label>
                            <input type="text" class="form-control" id="edit_body_type" name="body_type" required>
                        </div>
                        <div class="mb-3">
                            <label for="edit_engine_cc" class="form-label">引擎排氣量 (cc)</label>
                            <input type="text" class="form-control" id="edit_engine_cc" name="engine_cc" required>
                        </div>
                        <div class="mb-3">
                            <label for="edit_horsepower" class="form-label">馬力</label>
                            <input type="text" class="form-control" id="edit_horsepower" name="horsepower" required>
                        </div>
                        <div class="mb-3">
                            <label for="edit_fuel_type" class="form-label">燃料類型</label>
                            <input type="text" class="form-control" id="edit_fuel_type" name="fuel_type" required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">取消</button>
                        <button type="submit" class="btn btn-primary">更新車輛</button>
                    </div>
                </div>
            </form>
          </div>
        </div>
        
    </div>
    
    <!-- Footer -->
    <footer class="footer mt-5">
        <div class="container text-center">
            <p class="mb-0">&copy; 2024 汽車比較系統. 版權所有.</p>
        </div>
    </footer>
    
    <!-- Bootstrap JS 和 jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        $(document).ready(function() {
            // 當點擊編輯按鈕時，填充模態框並顯示
            $('.edit-btn').click(function() {
                var variantId = $(this).data('variant-id');
                var brandId = $(this).data('brand-id');
                var modelName = $(this).data('model-name');
                var year = $(this).data('year');
                var trimName = $(this).data('trim-name');
                var price = $(this).data('price');
                var bodyType = $(this).data('body-type');
                var engineCc = $(this).data('engine-cc');
                var horsepower = $(this).data('horsepower');
                var fuelType = $(this).data('fuel-type');
                
                // 填充模態框的表單欄位
                $('#edit_variant_id').val(variantId);
                $('#edit_brand_id').val(brandId);
                $('#edit_model_name').val(modelName);
                $('#edit_year').val(year);
                $('#edit_trim_name').val(trimName);
                $('#edit_price').val(price);
                $('#edit_body_type').val(bodyType);
                $('#edit_engine_cc').val(engineCc);
                $('#edit_horsepower').val(horsepower);
                $('#edit_fuel_type').val(fuelType);
                
                // 顯示模態框
                $('#editVehicleModal').modal('show');
            });
        });
    </script>
</body>
</html>
