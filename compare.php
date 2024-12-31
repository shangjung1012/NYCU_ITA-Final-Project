<?php 
// compare.php
include 'db_connection.php';
session_start();

if (!isset($_SESSION['compare_list']) || count($_SESSION['compare_list']) < 1) {
    echo "沒有選擇要比較的車輛。<br><a href='compare_selection.php' class='btn btn-primary mt-3'>返回選擇頁面</a>";
    exit;
}

$variant_ids = array_map('intval', $_SESSION['compare_list']);
$placeholders = implode(',', array_fill(0, count($variant_ids), '?'));

// 構建查詢
$query = "SELECT variants.*, models.model_name, models.year, brands.name as brand_name 
          FROM variants 
          JOIN models ON variants.model_id = models.id 
          JOIN brands ON models.brand_id = brands.id 
          WHERE variants.id IN ($placeholders)";

$stmt = $conn->prepare($query);

// 動態綁定參數
$types = str_repeat('i', count($variant_ids));
$stmt->bind_param($types, ...$variant_ids);
$stmt->execute();
$result = $stmt->get_result();

// 獲取所有選擇的車輛
$variants = [];
while($row = $result->fetch_assoc()) {
    $variants[] = $row;
}
$stmt->close();

// 准备图表数据
$chartData = [];
foreach ($variants as $variant) {
    $horsepower = $variant['horsepower'];

    // Extract the numeric part for chart purposes
    if (preg_match('/^(\d+)/', $horsepower, $matches)) {
        $parsedHorsepower = (int)$matches[1];
    } else {
        $parsedHorsepower = null; // Handle cases where no valid number is present
    }

    $chartData[] = [
        'name' => htmlspecialchars($variant['model_name']),
        'price' => (int)$variant['price'],
        'engine' => (int)$variant['engine_cc'],
        'horsepower' => $parsedHorsepower,
        'horsepowerRaw' => htmlspecialchars($horsepower), // Full value for display
    ];
}

// 清空比較列表
// $_SESSION['compare_list'] = [];
?>

<!DOCTYPE html>
<html lang="zh-TW">
<head>
    <meta charset="UTF-8">
    <title>汽車比較結果</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="styles.css">
    <style>
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        th, td {
            border: 1px solid #dee2e6;
            padding: 12px;
            text-align: center;
        }
        th {
            background-color: #e9ecef;
        }
        tr:hover {
            background-color: #f1f3f5;
        }
    </style>
</head>
<body style="margin-top: 100px;">
    <?php include 'navbar.php'; ?>

    <div class="container mt-5">
        <h1 class="mb-4">汽車比較結果</h1>
        <a href="compare_selection.php" class="btn btn-secondary mb-4">返回比較選擇頁面</a>

        <!-- Comparison Table -->
        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>品牌</th>
                        <th>車系</th>
                        <th>年份</th>
                        <th>配置名稱</th>
                        <th>價格 (萬)</th>
                        <th>車體類型</th>
                        <th>引擎排氣量</th>
                        <th>馬力</th>
                        <th>燃料類型</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (count($variants) > 0): ?>
                        <?php foreach ($variants as $variant): ?>
                            <tr>
                                <td><?= htmlspecialchars($variant['brand_name']); ?></td>
                                <td><?= htmlspecialchars($variant['model_name']); ?></td>
                                <td><?= htmlspecialchars($variant['year']); ?></td>
                                <td><?= htmlspecialchars($variant['trim_name']); ?></td>
                                <td><?= $variant['price'] == 0 ? '售價未公布' : htmlspecialchars($variant['price']); ?></td>
                                <td><?= htmlspecialchars($variant['body_type']); ?></td>
                                <td><?= htmlspecialchars($variant['engine_cc']); ?></td>
                                <td><?= htmlspecialchars($variant['horsepower']); ?></td>
                                <td><?= htmlspecialchars($variant['fuel_type']); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr><td colspan="9">沒有選擇的車輛資料</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <!-- Chart.js Visualization -->
        <div class="row mt-5">
            <!-- Left Column for Bar Charts -->
            <div class="col-md-4">
                <h2>價格 (萬)</h2>
                <canvas id="priceChart"></canvas>
                <h2>引擎排氣量 (cc)</h2>
                <canvas id="engineChart"></canvas>
                <h2>馬力</h2>
                <canvas id="horsepowerChart"></canvas>
            </div>

            <!-- Right Column for Radar Chart -->
            <div class="col-md-8">
                <h2>綜合性能比較</h2>
                <canvas id="radarChart"></canvas>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        // Prepare chart data
        const chartData = <?= json_encode($chartData); ?>;

        // Create Separate Bar Charts
        function createBarChart(ctx, label, data, color) {
            new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: chartData.map(car => car.name),
                    datasets: [{
                        label: label,
                        data: data,
                        backgroundColor: color,
                        borderColor: color,
                        borderWidth: 1
                    }]
                },
                options: {
                    responsive: true,
                    scales: {
                        y: {
                            beginAtZero: true
                        }
                    }
                }
            });
        }

        // Generate Bar Charts
        createBarChart(
            document.getElementById('priceChart'),
            '價格 (萬)',
            chartData.map(car => car.price),
            'rgba(75, 192, 192, 0.6)'
        );
        createBarChart(
            document.getElementById('engineChart'),
            '引擎排氣量 (cc)',
            chartData.map(car => car.engine),
            'rgba(192, 75, 192, 0.6)'
        );
        createBarChart(
            document.getElementById('horsepowerChart'),
            '馬力',
            chartData.map(car => car.horsepower),
            'rgba(192, 192, 75, 0.6)'
        );

        // Radar Chart Data Preparation
        const radarLabels = ['價格 (萬)', '引擎排氣量 (cc)', '馬力'];
        const radarData = chartData.map(car => ({
            label: car.name,
            data: [car.price, car.engine, car.horsepower],
        }));

        const maxValues = {
            price: Math.max(...chartData.map(car => car.price)),
            engine: Math.max(...chartData.map(car => car.engine)),
            horsepower: Math.max(...chartData.map(car => car.horsepower))
        };

        const normalizedData = chartData.map(car => ({
            label: car.name,
            data: [
                car.price / maxValues.price,
                car.engine / maxValues.engine,
                car.horsepower / maxValues.horsepower
            ]
        }));

        // Create Radar Chart
        new Chart(document.getElementById('radarChart'), {
            type: 'radar',
            data: {
                labels: ['價格 (萬)', '引擎排氣量 (cc)', '馬力'],
                datasets: normalizedData.map((car, index) => ({
                    label: car.label,
                    data: car.data,
                    backgroundColor: `rgba(${(index * 50) % 255}, ${(index * 100) % 255}, ${(index * 150) % 255}, 0.2)`,
                    borderColor: `rgba(${(index * 50) % 255}, ${(index * 100) % 255}, ${(index * 150) % 255}, 1)`,
                    borderWidth: 1,
                })),
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        position: 'top',
                    },
                },
                scales: {
                    r: {
                        ticks: {
                            beginAtZero: true,
                            callback: function (value) {
                                return (value * 100).toFixed(0) + '%'; // Show values as percentages
                            }
                        },
                        pointLabels: {
                            font: {
                                size: 14
                            }
                        }
                    }
                },
            },
        });
    </script>
</body>
</html>

<?php $conn->close(); ?>