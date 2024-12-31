<?php
// get_variant.php
session_start();
include 'db_connection.php';

if (isset($_GET['variant_id']) && is_numeric($_GET['variant_id'])) {
    $variant_id = intval($_GET['variant_id']);
    
    // 獲取車輛詳細資料
    $stmt = $conn->prepare("SELECT variants.*, models.model_name, models.year, brands.name as brand_name 
                            FROM variants 
                            JOIN models ON variants.model_id = models.id 
                            JOIN brands ON models.brand_id = brands.id 
                            WHERE variants.id = ?");
    $stmt->bind_param("i", $variant_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows === 1) {
        $vehicle = $result->fetch_assoc();
        
        // 顯示 HTML 列表項
        echo "<li class='list-group-item d-flex justify-content-between align-items-center' data-id='" . htmlspecialchars($variant_id) . "'>";
        echo htmlspecialchars($vehicle['brand_name']) . " " . htmlspecialchars($vehicle['model_name']) . " (" . htmlspecialchars($vehicle['year']) . ") - " . htmlspecialchars($vehicle['trim_name']);
        
        // 判斷價格是否為 0
        if ($vehicle['price'] == 0) {
            echo " - 售價未公布";
        } else {
            echo " - " . htmlspecialchars($vehicle['price']) . " 萬";
        }
        
        echo "<button class='btn btn-danger btn-sm remove-btn' data-id='" . htmlspecialchars($variant_id) . "'>移除</button>";
        echo "</li>";
    } else {
        echo "車輛不存在。";
    }
    
    $stmt->close();
} else {
    echo "無效的車輛ID。";
}

$conn->close();
?>
