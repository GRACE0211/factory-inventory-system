<?php
// CORS 設定
header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json');
// 如果有要傳送 POST、PUT 等其他請求方法，可以加：
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');

// 如果有額外的自定義 header，也可以加這行（選擇性）
header('Access-Control-Allow-Headers: Content-Type');

// 資料庫連線設定
$host = 'localhost';
$dbname = 'factory_inventory';
$username = 'root';
$password = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
    
    $sql = "SELECT * FROM transactions";
    $stmt = $pdo->query($sql);
    $transactions = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode($transactions);

} catch (PDOException $e) {
    echo json_encode(['error' => $e->getMessage()]);
}

