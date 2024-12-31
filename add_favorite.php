<?php
// add_favorite.php
session_start();
include 'db_connection.php';

// 檢查是否登入且為普通用戶
if (!isset($_SESSION['username']) || $_SESSION['role'] !== 'user') {
    echo 'unauthorized';
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['variant_id']) && is_numeric($_POST['variant_id'])) {
        $variant_id = intval($_POST['variant_id']);
        $user_id = $_SESSION['user_id'];
        
        // 初始化最愛清單
        if (!isset($_SESSION['favorites_list'])) {
            $_SESSION['favorites_list'] = [];
        }
        
        // 檢查是否已存在
        if (in_array($variant_id, $_SESSION['favorites_list'])) {
            echo 'exists';
            exit();
        }
        
        // 插入到 favorites 表
        $stmt = $conn->prepare("INSERT INTO favorites (user_id, variant_id) VALUES (?, ?)");
        $stmt->bind_param("ii", $user_id, $variant_id);
        if ($stmt->execute()) {
            $_SESSION['favorites_list'][] = $variant_id;
            echo 'success';
        } else {
            echo 'error';
        }
        $stmt->close();
    } else {
        echo 'invalid';
    }
} else {
    echo 'invalid_request';
}

$conn->close();
?>
