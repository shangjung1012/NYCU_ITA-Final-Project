<?php
// add_compare.php
session_start();
include 'db_connection.php';

// 檢查是否登入
if (!isset($_SESSION['username'])) {
    echo 'unauthorized';
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['variant_id']) && is_numeric($_POST['variant_id'])) {
        $variant_id = intval($_POST['variant_id']);
        
        // 初始化比較清單
        if (!isset($_SESSION['compare_list'])) {
            $_SESSION['compare_list'] = [];
        }
        
        // 檢查是否已存在
        if (in_array($variant_id, $_SESSION['compare_list'])) {
            echo 'exists';
            exit();
        }
        
        // 檢查是否已達到限制
        if (count($_SESSION['compare_list']) >= 4) {
            echo 'limit';
            exit();
        }
        
        // 添加到比較清單
        $_SESSION['compare_list'][] = $variant_id;
        echo 'success';
    } else {
        echo 'invalid';
    }
} else {
    echo 'invalid_request';
}

$conn->close();
?>
