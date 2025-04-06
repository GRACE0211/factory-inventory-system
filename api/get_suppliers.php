<?php
// 告訴前端回應 JSON 格式
header('Content-Type: application/json');

// 允許所有來源（最基本 CORS 解法）
header('Access-Control-Allow-Origin: *');

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
    // 建立 PDO 連線
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION); // 啟用錯誤顯示
} catch (PDOException $e) {
    echo json_encode(['error' => '資料庫連線失敗', 'message' => $e->getMessage()]);
    exit;
}

try {
    // 查詢 suppliers 資料
    $sql = "SELECT * FROM supplier";
    $stmt = $pdo->query($sql);
    $suppliers = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode($suppliers);
} catch (PDOException $e) {
    echo json_encode(['error' => '資料查詢失敗', 'message' => $e->getMessage()]);
}
?>
