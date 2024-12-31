# NYCU_ITA-Final-Project

## 1. 專案概述
**NYCU_ITA-Final-Project** 是一個基於 PHP 和 MySQL 的汽車比較系統，允許用戶查看各品牌的車型，進行比較，並將喜愛的車輛加入收藏列表。專案包括用戶認證、資料管理、篩選與排序等功能。

## 2.專案目錄結構
以下為本專案主要檔案與資料夾結構說明。
```
NYCU_ITA-Final-Project/
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
