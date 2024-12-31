<?php
// variants.php
include 'db_connection.php';

if (!isset($_GET['model_id'])) {
    echo "缺少模型 ID";
    exit;
}

$model_id = intval($_GET['model_id']);

// 查詢模型資料
$stmt = $conn->prepare("SELECT models.*, brands.name as brand_name FROM models JOIN brands ON models.brand_id = brands.id WHERE models.id = ?");
$stmt->bind_param("i", $model_id);
$stmt->execute();
$result = $stmt->get_result();
if ($result->num_rows == 0) {
    echo "找不到模型資料";
    exit;
}
$model = $result->fetch_assoc();
$stmt->close();

// 查詢該模型的所有變種
$stmt = $conn->prepare("SELECT * FROM variants WHERE model_id = ? ORDER BY price ASC");
$stmt->bind_param("i", $model_id);
$stmt->execute();
$variants = $stmt->get_result();
$stmt->close();
?>

<!DOCTYPE html>
<html lang="zh-TW">
<head>
    <meta charset="UTF-8">
    <title><?php echo htmlspecialchars($model['brand_name'] . " " . $model['model_name']); ?> 變種</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <h1><?php echo htmlspecialchars($model['brand_name'] . " " . $model['model_name'] . " (" . $model['year'] . ")"); ?></h1>
    <a href="models.php?brand_id=<?php echo $model['brand_id']; ?>">返回模型列表</a>
    <h2>價格範圍：<?php echo htmlspecialchars($model['price_range']); ?> 萬</h2>
    <form action="compare.php" method="get">
        <table border="1">
            <thead>
                <tr>
                    <th>選擇</th>
                    <th>配置名稱</th>
                    <th>價格 (萬)</th>
                    <th>車體類型</th>
                    <th>引擎排氣量</th>
                    <th>馬力</th>
                    <th>燃料類型</th>
                </tr>
            </thead>
            <tbody>
                <?php
                if ($variants->num_rows > 0) {
                    while($row = $variants->fetch_assoc()) {
                        echo "<tr>";
                        echo "<td><input type='checkbox' name='variant_ids[]' value='" . $row['id'] . "'></td>";
                        echo "<td>" . htmlspecialchars($row['trim_name']) . "</td>";
                        echo "<td>" . htmlspecialchars($row['price']) . "</td>";
                        echo "<td>" . htmlspecialchars($row['body_type']) . "</td>";
                        echo "<td>" . htmlspecialchars($row['engine_cc']) . "</td>";
                        echo "<td>" . htmlspecialchars($row['horsepower']) . "</td>";
                        echo "<td>" . htmlspecialchars($row['fuel_type']) . "</td>";
                        echo "</tr>";
                    }
                } else {
                    echo "<tr><td colspan='7'>沒有變種資料</td></tr>";
                }
                ?>
            </tbody>
        </table>
        <br>
        <input type="submit" value="比較選擇">
    </form>
</body>
</html>

<?php
$conn->close();
?>
