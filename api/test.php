<?php
// 告訴前端回應 JSON 格式
header('Content-Type: application/json');

// 允許所有來源（最基本 CORS 解法）
header('Access-Control-Allow-Origin: *');

// 如果有要傳送 POST、PUT 等其他請求方法，可以加：
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');

// 如果有額外的自定義 header，也可以加這行（選擇性）
header('Access-Control-Allow-Headers: Content-Type');


// 資料庫連線設定 -> 告訴 PHP 資料庫在哪裡
$host = 'localhost';
$dbname = 'factory_inventory';
$username = 'root';
$password = '';

try {
    // 資料來源名稱
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
    // 設定錯誤模式
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    echo json_encode([
        'error' => '資料庫連線失敗',
        'message' => $e->getMessage()
    ]);
    exit; // 結束程式
}

try{
    $sql = "
    SELECT p.supplier_id,p.product_id,p.quantity AS initial_quantity,
SUM(CASE WHEN t.transaction_type = 'IN' THEN t.quantity ELSE 0 END) AS sum_in,
SUM(CASE WHEN t.transaction_type = 'OUT' THEN t.quantity ELSE 0 END) AS sum_out,
p.quantity + (
    SUM(CASE WHEN t.transaction_type = 'IN' THEN t.quantity ELSE 0 END) - 
    SUM(CASE WHEN t.transaction_type = 'OUT' THEN t.quantity ELSE 0 END)) AS remaining_quantity
FROM product_suppliers p
LEFT JOIN transactions t
ON p.product_id = t.product_id AND p.supplier_id = t.supplier_id
GROUP BY t.product_id,t.supplier_id  
ORDER BY `p`.`supplier_id` ASC
    ";
    $stmt = $pdo->query($sql);
    $products = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo json_encode($products);
} catch (PDOException $e) {
    echo json_encode([
    'error' => '資料查詢失敗',
    'message' => $e ->getMessage()
    ]);
}





?>