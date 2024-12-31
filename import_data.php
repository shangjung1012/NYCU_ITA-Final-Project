<?php
// import_data.php

// 資料庫連接設置
$servername = "localhost";
$username = "root"; // XAMPP 默認用戶名
$password = "";     // XAMPP 默認無密碼
$dbname = "car_database";
$port = 3306; // 新的 MySQL 埠號

// 建立連接
$conn = new mysqli($servername, $username, $password, $dbname, $port);

// 檢查連接
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// 指定 car_data 目錄
$dataDir = __DIR__ . "/car_scraper/car_data/";

// 遍歷品牌資料夾
$brands = array_diff(scandir($dataDir), array('..', '.'));

foreach ($brands as $brand) {
    $brandPath = $dataDir . $brand . "/info.json";
    if (file_exists($brandPath)) {
        $jsonData = file_get_contents($brandPath);
        $carModels = json_decode($jsonData, true);

        foreach ($carModels as $carModel) {
            // 插入品牌
            $brandName = $conn->real_escape_string($carModel['brand']);
            // 檢查品牌是否存在
            $brandCheck = $conn->query("SELECT id FROM brands WHERE name='$brandName'");
            if ($brandCheck->num_rows > 0) {
                $brandId = $brandCheck->fetch_assoc()['id'];
            } else {
                $conn->query("INSERT INTO brands (name) VALUES ('$brandName')");
                $brandId = $conn->insert_id;
            }

            // 插入模型
            $modelName = $conn->real_escape_string($carModel['model_name']);
            $year = intval($carModel['year']);
            $priceRange = $conn->real_escape_string($carModel['price_range']);
            $url = $conn->real_escape_string($carModel['url']);

            // 檢查模型是否存在
            $modelCheck = $conn->query("SELECT id FROM models WHERE model_name='$modelName' AND year=$year AND brand_id=$brandId");
            if ($modelCheck->num_rows > 0) {
                $modelId = $modelCheck->fetch_assoc()['id'];
            } else {
                $stmt = $conn->prepare("INSERT INTO models (brand_id, model_name, year, price_range, url) VALUES (?, ?, ?, ?, ?)");
                $stmt->bind_param("issss", $brandId, $modelName, $year, $priceRange, $url);
                $stmt->execute();
                $modelId = $stmt->insert_id;
                $stmt->close();
            }

            // 插入變種
            foreach ($carModel['variants'] as $variant) {
                $trimName = $conn->real_escape_string($variant['trim_name']);
                $price = floatval($variant['price']);
                $bodyType = $conn->real_escape_string($variant['body_type']);
                $engineCc = $conn->real_escape_string($variant['engine_cc']);
                $horsepower = $conn->real_escape_string($variant['horsepower']);
                $fuelType = $conn->real_escape_string($variant['fuel_type']);

                // 檢查變種是否存在
                $variantCheck = $conn->query("SELECT id FROM variants WHERE model_id=$modelId AND trim_name='$trimName'");
                if ($variantCheck->num_rows == 0) {
                    $stmt = $conn->prepare("INSERT INTO variants (model_id, trim_name, price, body_type, engine_cc, horsepower, fuel_type) VALUES (?, ?, ?, ?, ?, ?, ?)");
                    $stmt->bind_param("isdssss", $modelId, $trimName, $price, $bodyType, $engineCc, $horsepower, $fuelType);
                    $stmt->execute();
                    $stmt->close();
                }
            }
        }
    }
}

echo "Data import completed successfully.";

$conn->close();
?>
