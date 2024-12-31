<?php
// register.php
session_start();
include 'db_connection.php';

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);
    
    // 密碼強度檢查
    if (strlen($password) < 8) {
        $error = "密碼長度至少為 8 個字符。";
    } elseif (!preg_match('/[A-Z]/', $password)) {
        $error = "密碼必須包含至少一個大寫字母。";
    } elseif (!preg_match('/[a-z]/', $password)) {
        $error = "密碼必須包含至少一個小寫字母。";
    } elseif (!preg_match('/[0-9]/', $password)) {
        $error = "密碼必須包含至少一個數字。";
    } elseif (!preg_match('/[\W]/', $password)) {
        $error = "密碼必須包含至少一個特殊字符。";
    }
    
    if (empty($username) || empty($password)) {
        $error = "使用者名稱和密碼不可為空。";
    }
    
    if (empty($error)) {
        // 檢查用戶是否已存在
        $stmt = $conn->prepare("SELECT id FROM users WHERE username = ?");
        if ($stmt === false) {
            die("準備查詢失敗: " . htmlspecialchars($conn->error));
        }
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows > 0) {
            $error = "此使用者名稱已被使用。";
        } else {
            // 使用 password_hash 加密密碼
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            
            // 插入新用戶
            $stmt_insert = $conn->prepare("INSERT INTO users (username, password, role) VALUES (?, ?, 'user')");
            if ($stmt_insert === false) {
                die("準備插入失敗: " . htmlspecialchars($conn->error));
            }
            $stmt_insert->bind_param("ss", $username, $hashed_password);
            
            if ($stmt_insert->execute()) {
                $success = "註冊成功，您現在可以 <a href='login.php'>登入</a>。";
            } else {
                $error = "註冊時出錯：" . $stmt_insert->error;
            }
            $stmt_insert->close();
        }
        $stmt->close();
    }
}

$conn->close();
?>
<!DOCTYPE html>
<html lang="zh-TW">
<head>
    <meta charset="UTF-8">
    <title>註冊 - 汽車比較系統</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Noto+Sans+TC&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Noto Sans TC', sans-serif;
            background-color: #f8f9fa;
        }
        .register-container {
            max-width: 400px;
            margin: 100px auto;
            padding: 30px;
            background-color: #fff;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
        .register-container h2 {
            margin-bottom: 20px;
        }
        .register-container .btn-group {
            display: flex;
            justify-content: space-between;
        }
        .register-container .btn-group a {
            width: 48%;
        }
    </style>
</head>
<body>
    <div class="container register-container">
        <h2 class="text-center">註冊</h2>
        <?php if ($error): ?>
            <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>
        
        <?php if ($success): ?>
            <div class="alert alert-success"><?= $success ?></div>
        <?php endif; ?>
        
        <form method="POST" action="register.php">
            <div class="mb-3">
                <label for="username" class="form-label">使用者名稱</label>
                <input type="text" class="form-control" id="username" name="username" required autofocus>
            </div>
            <div class="mb-3">
                <label for="password" class="form-label">密碼</label>
                <input type="password" class="form-control" id="password" name="password" required>
                <div class="form-text">密碼需至少包含 8 個字符，包括大寫字母、小寫字母、數字和特殊字符。</div>
            </div>
            <button type="submit" class="btn btn-primary w-100">註冊</button>
        </form>
        <div class="btn-group mt-3">
            <a href="login.php" class="btn btn-outline-secondary">已有帳號？登入</a>
            <a href="index.php" class="btn btn-outline-secondary">返回首頁</a>
        </div>
    </div>
    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
