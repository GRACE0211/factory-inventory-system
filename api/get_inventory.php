<?php
// 設定 HTTP 標頭，允許跨來源請求（前後端分離必備）
header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json');

// 資料庫連線資訊
$host = 'localhost';
$dbname = 'factory_inventory';  // ✅ 你的資料庫名字
$username = 'root';             // ✅ 你的 MySQL 帳號
$password = '';                 // ✅ 你的 MySQL 密碼

try {
    // 建立 PDO 連線
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // SQL 語法
    // 計算出所有供應商各個產品的初始庫存和剩餘庫存
    $sql = "
        SELECT 
            p.supplier_id,
            p.product_id,
            p.quantity AS initial_quantity,
            p.quantity + 
                COALESCE(SUM(CASE WHEN t.transaction_type = 'IN' THEN t.quantity ELSE 0 END), 0) -
                COALESCE(SUM(CASE WHEN t.transaction_type = 'OUT' THEN t.quantity ELSE 0 END), 0) 
            AS remaining_quantity
        FROM product_suppliers p
        LEFT JOIN transactions t 
            ON p.product_id = t.product_id 
            AND p.supplier_id = t.supplier_id
        GROUP BY p.supplier_id, p.product_id
        ORDER BY p.supplier_id, p.product_id
    ";

    // 執行查詢
    $stmt = $pdo->prepare($sql);
    $stmt->execute();

    // 把結果取出來（陣列格式）
    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // 回傳 JSON 給前端
    echo json_encode($result);

} catch (PDOException $e) {
    // 錯誤處理
    echo json_encode([
        'error' => $e->getMessage()
    ]);
}
