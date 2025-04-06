<?php
header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json');
header('Access-Control-Allow-Methods: POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

// 資料庫設定
$host = 'localhost';
$dbname = 'factory_inventory';
$username = 'root';
$password = '';

// 建立 PDO 連線
try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
} catch (PDOException $e) {
    echo json_encode([
        'success' => false,
        'message' => '資料庫連線失敗: ' . $e->getMessage()
    ]);
    exit;
}

// 接收 POST 的 JSON 輸入
$data = json_decode(file_get_contents("php://input"), true);

// 驗證輸入欄位
$supplier_id = $data['supplier_id'] ?? '';
$product_id = $data['product_id'] ?? '';
$transaction_type = $data['transaction_type'] ?? '';
$quantity = $data['quantity'] ?? '';
$transaction_date = $data['transaction_date'] ?? '';

if (
    empty($supplier_id) ||
    empty($product_id) ||
    empty($transaction_type) ||
    empty($quantity) ||
    empty($transaction_date)
) {
    echo json_encode([
        'success' => false,
        'message' => '欄位不得為空'
    ]);
    exit;
}

// 寫入資料庫
try {
    $sql = "INSERT INTO transactions (supplier_id, product_id, transaction_type, quantity, transaction_date)
            VALUES (:supplier_id, :product_id, :transaction_type, :quantity, :transaction_date)";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        ':supplier_id' => $supplier_id,
        ':product_id' => $product_id,
        ':transaction_type' => $transaction_type,
        ':quantity' => $quantity,
        ':transaction_date' => $transaction_date
    ]);

    echo json_encode([
        'success' => true,
        'message' => '新增成功'
    ]);
} catch (PDOException $e) {
    echo json_encode([
        'success' => false,
        'message' => '新增失敗: ' . $e->getMessage()
    ]);
}
