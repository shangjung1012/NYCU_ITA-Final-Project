# NYCU_DBMS-Final-Project

## 1. 專案概述
**NYCU_DBMS-Final-Project** 是一個基於 PHP 和 MySQL 的汽車比較系統，允許用戶查看各品牌的車型，進行比較，並將喜愛的車輛加入收藏列表。專案包括用戶認證、資料管理、篩選與排序等功能。

## 2.專案目錄結構
以下為本專案主要檔案與資料夾結構說明。
```
NYCU_DBMS-Final-Project/
├── README.md                // 專案使用說明（本檔）
├── car_scraper/
│   └── ...                  // 爬蟲相關檔案 (如爬取 scripts、JSON data)
├── images/
│   └── brands/              // 儲存各品牌圖示或 logo
├── index.php                // 首頁，顯示英雄圖、功能簡介
├── about_us.php             // 關於我們頁面
├── navbar.php               // 共用導覽列
├── import_data.php          // 匯入爬取之 JSON 車輛資料至資料庫
├── login.php                // 使用者登入頁面
├── logout.php               // 登出功能，銷毀 session
├── register.php             // 使用者註冊頁面
├── db_connection.php        // 資料庫連線設定
├── brands.php               // 顯示所有品牌（主清單）
├── brand_cars.php           // 列出該品牌所有車款，可排序/篩選
├── models.php               // 顯示指定品牌下之車型
├── variants.php             // 顯示某車型（model）的所有變種
├── get_models.php           // 依車系 ID 回傳對應變種（variants）選單
├── get_series.php           // 依品牌 ID 回傳對應車系（models）選單
├── get_variant.php          // 取得單一車輛詳細資訊（Ajax用）
├── compare_selection.php    // 選擇欲比較之車款（含我的最愛）
├── add_compare.php          // 將車款加入比較列表
├── admin_setup.php          // 建立預設管理員帳號
├── admin_dashboard.php      // 管理員後台首頁
├── manage_brands.php        // 管理員後台：管理品牌資訊
├── manage_vehicles.php      // 管理員後台：管理車輛資訊
├── favorites.php            // 我的最愛清單
├── add_favorite.php         // 將車款加入我的最愛
├── remove_favorite.php      // 從我的最愛中移除某車款
├── remove_compare.php       // 從比較列表移除某車款
├── reset_compare.php        // 重置比較清單
├── compare.php              // 顯示已選擇車輛之比較結果
└── styles.css               // 全域自訂樣式

```


## 3. 環境準備
### 安裝必備軟體
1. **XAMPP**：包含 Apache、MySQL、PHP 等。
2. **Git**：用於複製 GitHub 倉庫。
3. **網頁瀏覽器**：如 Chrome、Firefox 等。

### 安裝指導
- **XAMPP**：
  - 前往 [XAMPP 官方網站](https://www.apachefriends.org/) 下載適用於你系統的版本。
  - 安裝後啟動 Apache 和 MySQL 服務。
- **Git**：
  - 前往 [Git 官方網站](https://git-scm.com/) 下載適用於你系統的版本。
  - 安裝並確認 `git` 指令可正常使用。

## 4. 複製 GitHub repository
在下列路徑
```bash
cd C:\xampp\htdocs\
```
在終端機執行以下指令複製專案：
```bash
git clone --recurse-submodules https://github.com/shangjung1012/NYCU_DBMS-Final-Project.git
```

## 4. 建立和組態資料庫
### 創建資料庫
開啟 phpMyAdmin，登入後選擇 New 建立新的資料庫。
在 Database name 欄位輸入 car_database，選擇編碼為 utf8mb4_general_ci，點擊 Create。

### 創建資料表
執行以下 SQL 語句來創建所需資料表: http://localhost/phpmyadmin/

```sql
CREATE TABLE brands (
  id INT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(100) NOT NULL UNIQUE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;


CREATE TABLE models (
  id INT AUTO_INCREMENT PRIMARY KEY,
  brand_id INT NOT NULL,
  model_name VARCHAR(255) NOT NULL,
  year INT NOT NULL,
  price_range VARCHAR(50),
  url VARCHAR(255),
  FOREIGN KEY (brand_id) REFERENCES `brands`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;


CREATE TABLE variants (
  id INT AUTO_INCREMENT PRIMARY KEY,
  model_id INT NOT NULL,
  trim_name VARCHAR(255) NOT NULL,
  price DECIMAL(10,2),
  body_type VARCHAR(50),
  engine_cc VARCHAR(50),
  horsepower VARCHAR(50),
  fuel_type VARCHAR(50),
  FOREIGN KEY (model_id) REFERENCES `models`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;


CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    role ENUM('admin', 'user') NOT NULL DEFAULT 'user'
);


CREATE TABLE favorites (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    variant_id INT NOT NULL,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (variant_id) REFERENCES variants(id) ON DELETE CASCADE,
    UNIQUE KEY unique_favorite (user_id, variant_id)
);
```

## 5. 導入資料
### 導入車輛資料
訪問以下 URL 來導入車輛資料：
```bash
http://localhost/NYCU_DBMS-Final-Project/import_data.php
```

### 設置管理員帳號
訪問以下 URL 創建預設管理員帳號：
```bash
http://localhost/NYCU_DBMS-Final-Project/admin_setup.php
```
