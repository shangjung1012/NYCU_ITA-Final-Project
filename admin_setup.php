<?php
// admin_setup.php

include 'db_connection.php';

// 檢查管理員是否已存在
$stmt = $conn->prepare("SELECT * FROM users WHERE username = ?");
$admin_username = 'admin';
$stmt->bind_param("s", $admin_username);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows == 0) {
    // 使用 password_hash 加密密碼
    $admin_password = password_hash('admin', PASSWORD_DEFAULT); // 默認使用 Bcrypt

    // 插入管理員帳號
    $stmt_insert = $conn->prepare("INSERT INTO users (username, password, role) VALUES (?, ?, 'admin')");
    $stmt_insert->bind_param("ss", $admin_username, $admin_password);
    if ($stmt_insert->execute()) {
        echo "管理員帳號創建成功。";
    } else {
        echo "創建管理員帳號時出錯：" . $stmt_insert->error;
    }
    $stmt_insert->close();
} else {
    echo "管理員帳號已存在。";
}

$stmt->close();
$conn->close();
?>
