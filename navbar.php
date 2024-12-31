<!-- navbar.php -->
<?php
// navbar.php
if (!isset($current_page)) {
    $current_page = '';
}
?>
<nav class="navbar navbar-expand-lg navbar-light bg-light fixed-top">
  <div class="container">
    <a class="navbar-brand" href="index.php">汽車比較系統</a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" 
            aria-controls="navbarNav" aria-expanded="false" aria-label="切換導航">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse justify-content-end" id="navbarNav">
      <ul class="navbar-nav">
        <?php if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin'): ?>
        <li class="nav-item">
          <a class="nav-link <?php echo ($current_page == 'home') ? 'active' : ''; ?>" href="index.php">首頁</a>
        </li>
        <li class="nav-item">
          <a class="nav-link <?php echo ($current_page == 'compare') ? 'active' : ''; ?>" href="compare_selection.php">開始比較</a>
        </li>
        <?php endif; ?>
        <li class="nav-item">
          <a class="nav-link <?php echo ($current_page == 'brands') ? 'active' : ''; ?>" href="brands.php">所有品牌</a>
        </li>
        <?php if (isset($_SESSION['username'])): ?>
            <?php if ($_SESSION['role'] === 'admin'): ?>
                <li class="nav-item">
                    <a class="nav-link <?php echo ($current_page == 'admin_dashboard') ? 'active' : ''; ?>" href="admin_dashboard.php">管理員後台</a>
                </li>
            <?php else: ?>
                <li class="nav-item">
                    <a class="nav-link <?php echo ($current_page == 'favorites') ? 'active' : ''; ?>" href="favorites.php">我的最愛</a>
                </li>
            <?php endif; ?>
            <li class="nav-item">
                <a class="nav-link" href="logout.php">登出 (<?= htmlspecialchars($_SESSION['username']) ?>)</a>
            </li>
        <?php else: ?>
            <li class="nav-item">
                <a class="nav-link <?php echo ($current_page == 'login') ? 'active' : ''; ?>" href="login.php">登入</a>
            </li>
            <li class="nav-item">
                <a class="nav-link <?php echo ($current_page == 'register') ? 'active' : ''; ?>" href="register.php">註冊</a>
            </li>
        <?php endif; ?>
        <li class="nav-item">
            <a class="nav-link active" aria-current="page" href="about_us.php">關於我們</a> <!-- 新增的導航連結 -->
        </li>
      </ul>
    </div>
  </div>
</nav>