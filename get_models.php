<?php
// get_models.php
include 'db_connection.php';

if (isset($_GET['series_id']) && is_numeric($_GET['series_id'])) {
    $series_id = intval($_GET['series_id']);
    $min_price = isset($_GET['min_price']) ? floatval($_GET['min_price']) : 0;
    $max_price = isset($_GET['max_price']) ? floatval($_GET['max_price']) : 9999999;

    // 獲取該車系的所有車款，並根據價格範圍篩選
    $stmt = $conn->prepare("SELECT * FROM variants WHERE model_id = ? AND price BETWEEN ? AND ? ORDER BY price ASC");
    $stmt->bind_param("idd", $series_id, $min_price, $max_price);
    $stmt->execute();
    $result = $stmt->get_result();

    echo "<option value=''>-- 選擇車款 --</option>";
    if ($result->num_rows > 0) {
        while($row = $result->fetch_assoc()) {
            // 判斷價格是否為 0
            if ($row['price'] == 0) {
                echo "<option value='" . $row['id'] . "'>" . htmlspecialchars($row['trim_name']) . " - 售價未公布</option>";
            } else {
                echo "<option value='" . $row['id'] . "'>" . htmlspecialchars($row['trim_name']) . " - " . htmlspecialchars($row['price']) . " 萬</option>";
            }
        }
    } else {
        echo "<option value=''>無車款資料</option>";
    }

    $stmt->close();
}

$conn->close();
?>
