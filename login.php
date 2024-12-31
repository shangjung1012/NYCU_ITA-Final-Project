<?php
// login.php
session_start();
include 'db_connection.php';


// 設定當前頁面
$current_page = 'login';


// 初始化錯誤訊息
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);
    
    // 查詢用戶
    $stmt = $conn->prepare("SELECT id, username, password, role FROM users WHERE username = ?");
    if ($stmt === false) {
        die("準備查詢失敗: " . htmlspecialchars($conn->error));
    }
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows === 1) {
        // 獲取用戶資料
        $user = $result->fetch_assoc();
        
        // 檢查密碼
        if (password_verify($password, $user['password'])) {
            // 密碼正確，設定會話變數
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['role'] = $user['role'];
            
            // 根據角色重定向
            if ($user['role'] === 'admin') {
                header("Location: admin_dashboard.php");
            } else {
                header("Location: index.php");
            }
            exit();
        } else {
            $error = "無效的使用者名稱或密碼。";
        }
    } else {
        $error = "無效的使用者名稱或密碼。";
    }
    
    $stmt->close();
}

$conn->close();
?>
<!DOCTYPE html>
<html lang="zh-TW">
<head>
    <meta charset="UTF-8">
    <title>登入 - 汽車比較系統</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Noto+Sans+TC&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Noto Sans TC', sans-serif;
            background-color: #f8f9fa;
        }
        .login-container {
            max-width: 400px;
            margin: 100px auto;
            padding: 30px;
            background-color: #fff;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
        .login-container h2 {
            margin-bottom: 20px;
        }
        .login-container .btn-group {
            display: flex;
            justify-content: space-between;
        }
        .login-container .btn-group a {
            width: 48%;
        }
    </style>
</head>
<body>
    <div class="container login-container">
        <h2 class="text-center">登入</h2>
        <?php if ($error): ?>
            <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>
        <form method="POST" action="login.php">
            <div class="mb-3">
                <label for="username" class="form-label">使用者名稱</label>
                <input type="text" class="form-control" id="username" name="username" required autofocus>
            </div>
            <div class="mb-3">
                <label for="password" class="form-label">密碼</label>
                <input type="password" class="form-control" id="password" name="password" required>
            </div>
            <button type="submit" class="btn btn-primary w-100">登入</button>
        </form>
        <div class="btn-group mt-3">
            <a href="register.php" class="btn btn-outline-secondary">註冊</a>
            <a href="index.php" class="btn btn-outline-secondary">返回首頁</a>
        </div>
    </div>
    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
