<?php
// models.php
include 'db_connection.php';

if (!isset($_GET['brand_id'])) {
    echo "缺少品牌 ID";
    exit;
}

$brand_id = intval($_GET['brand_id']);

// 查詢品牌名稱
$stmt = $conn->prepare("SELECT name FROM brands WHERE id = ?");
$stmt->bind_param("i", $brand_id);
$stmt->execute();
$stmt->bind_result($brand_name);
$stmt->fetch();
$stmt->close();

// 查詢該品牌下的所有模型
$stmt = $conn->prepare("SELECT * FROM models WHERE brand_id = ? ORDER BY model_name ASC");
$stmt->bind_param("i", $brand_id);
$stmt->execute();
$models = $stmt->get_result();
$stmt->close();
?>

<!DOCTYPE html>
<html lang="zh-TW">
<head>
    <meta charset="UTF-8">
    <title><?php echo htmlspecialchars($brand_name); ?> 模型列表</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <h1><?php echo htmlspecialchars($brand_name); ?> 模型列表</h1>
    <a href="index.php">返回品牌列表</a>
    <ul>
        <?php
        if ($models->num_rows > 0) {
            while($row = $models->fetch_assoc()) {
                echo "<li><a href='variants.php?model_id=" . $row['id'] . "'>" . htmlspecialchars($row['model_name']) . " (" . $row['year'] . ")</a> - 價格範圍：" . htmlspecialchars($row['price_range']) . "</li>";
            }
        } else {
            echo "<li>沒有模型資料</li>";
        }
        ?>
    </ul>
</body>
</html>

<?php
$conn->close();
?>
