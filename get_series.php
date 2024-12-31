<?php
// get_series.php
include 'db_connection.php';

if (isset($_GET['brand_id']) && is_numeric($_GET['brand_id'])) {
    $brand_id = intval($_GET['brand_id']);

    // 修改 ORDER BY 子句：先按年份降序，再按車系名稱升序
    $stmt = $conn->prepare("SELECT id, model_name, year FROM models WHERE brand_id = ? ORDER BY year DESC, model_name ASC");
    $stmt->bind_param("i", $brand_id);
    $stmt->execute();
    $result = $stmt->get_result();

    echo "<option value=''>-- 選擇車系 --</option>";
    if ($result->num_rows > 0) {
        while($row = $result->fetch_assoc()) {
            // 將年份加入到車系名稱中
            $seriesText = htmlspecialchars($row['model_name']) . " (" . htmlspecialchars($row['year']) . ")";
            echo "<option value='" . $row['id'] . "'>" . $seriesText . "</option>";
        }
    } else {
        echo "<option value=''>無車系資料</option>";
    }

    $stmt->close();
}

$conn->close();
?>
