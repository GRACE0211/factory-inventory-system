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

// 建立 PDO 連線 -> 連上 MySQL 資料庫，準備操作
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

try {
    // 查詢產品資料
    $sql = "SELECT * FROM products";
    // query(): 執行SQL, 執行完會得到結果集
    $stmt = $pdo->query($sql);
    // 拿出查詢結果
    // [
    //     ["product_id" => 1, "name" => "toothbrush", "price" => 100],
    //     ["product_id" => 2, "name" => "toothpaste", "price" => 50]
    // ]
    $products = $stmt->fetchAll(PDO::FETCH_ASSOC);
    

    // 輸出 JSON 結果, 
    // json_encode()會把陣列轉成JSON格式, axios.get()可以直接收到
    echo json_encode($products);
} catch (PDOException $e) {
    echo json_encode([
        'error' => '資料查詢失敗',
        'message' => $e->getMessage()
    ]);
}

// JSON格式:
// [
//     { "product_id": 1, "name": "toothbrush", "price": 100 },
//     { "product_id": 2, "name": "toothpaste", "price": 50 }
// ] 
?>


