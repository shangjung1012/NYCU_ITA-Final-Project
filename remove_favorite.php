<?php
// remove_favorite.php
session_start();
include 'db_connection.php';

// 檢查是否登入且為普通用戶
if (!isset($_SESSION['username']) || $_SESSION['role'] !== 'user') {
    echo 'unauthorized';
    exit();
}
?>
<!DOCTYPE html>
<html lang="zh-TW">
<head>
    <meta charset="UTF-8">
    <title>移除我的最愛 - 汽車比較系統</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .success-message {
            margin-top: 100px;
            margin-left: 20px;
            font-size: 2rem; /* Same size as the heading in favorites.php */
        }
    </style>
</head>
<body>
    <?php include 'navbar.php'; ?>

    <div class="container mt-5">
        <?php
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (isset($_POST['variant_id']) && is_numeric($_POST['variant_id'])) {
                $variant_id = intval($_POST['variant_id']);
                $user_id = $_SESSION['user_id'];
                
                // 刪除 favorites 表中的條目
                $stmt = $conn->prepare("DELETE FROM favorites WHERE user_id = ? AND variant_id = ?");
                $stmt->bind_param("ii", $user_id, $variant_id);
                if ($stmt->execute()) {
                    // 從 session 的 favorites_list 中移除
                    if (isset($_SESSION['favorites_list'])) {
                        $_SESSION['favorites_list'] = array_values(array_diff($_SESSION['favorites_list'], [$variant_id]));
                    }
                    echo '<div class="success-message">已成功移除車輛</div>';
                    echo '<button onclick="window.location.href=\'favorites.php\'" class="btn btn-primary" style="margin-top: 20px; margin-left: 20px;">返回我的最愛</button>';
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
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>