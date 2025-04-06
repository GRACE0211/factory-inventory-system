<?php
header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json');

// 資料庫連線
$host = 'localhost';
$dbname = 'factory_inventory';
$username = 'root';
$password = '';

try {
  $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);

  // 取得 GET 參數
  $supplier_id = $_GET['supplier_id'] ?? '';
  $product_id = $_GET['product_id'] ?? '';
  $date = $_GET['date'] ?? '';
  $transaction_type = $_GET['transaction_type'] ?? '';
  $sort_field = $_GET['sort_field'] ?? 'transaction_date'; // ← 預設
  $sort_order = $_GET['sort_order'] ?? 'asc';

  // 安全檢查 - 防止 SQL injection
  $allowedSortFields = ['transaction_date', 'product_id', 'supplier_id'];
  if (!in_array($sort_field, $allowedSortFields)) {
    $sort_field = 'transaction_date';
  }

  $allowedSortOrders = ['asc', 'desc'];
  if (!in_array(strtolower($sort_order), $allowedSortOrders)) {
    $sort_order = 'asc';
  }

  // 基本 SQL
  $sql = "SELECT * FROM transactions WHERE 1=1";

  // 動態加上篩選條件
  if (!empty($supplier_id)) {
    $sql .= " AND supplier_id = :supplier_id";
  }

  if (!empty($product_id)) {
    $sql .= " AND product_id = :product_id";
  }

  if (!empty($transaction_type)) {
    $sql .= " AND transaction_type = :transaction_type";
  }

  if (!empty($date)) {
    $sql .= " AND transaction_date <= :date";
  }

  // 加上排序
  $sql .= " ORDER BY $sort_field $sort_order";

  // 準備 SQL
  $stmt = $pdo->prepare($sql);

  // 綁參數
  if (!empty($supplier_id)) {
    $stmt->bindParam(':supplier_id', $supplier_id);
  }
  if (!empty($product_id)) {
    $stmt->bindParam(':product_id', $product_id);
  }
  if (!empty($transaction_type)) {
    $stmt->bindParam(':transaction_type', $transaction_type);
  }
  if (!empty($date)) {
    $stmt->bindParam(':date', $date);
  }

  // 執行
  $stmt->execute();

  // 結果
  $transactions = $stmt->fetchAll(PDO::FETCH_ASSOC);

  echo json_encode($transactions);

} catch (PDOException $e) {
  echo json_encode(['error' => $e->getMessage()]);
}
