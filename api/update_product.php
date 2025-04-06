<?php
// 允許跨域
header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json');
header('Access-Control-Allow-Methods: POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

// 瀏覽器「會先發一個 OPTIONS 請求」問後端：「這個網頁可以存取你的 API 嗎？」
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
  http_response_code(200);
  exit();
}

$host = 'localhost';
$dbname = 'factory_inventory';
$username = 'root';
$password = '';

// try: 正常流程
// 把 PDO 設成「只要有錯誤就拋出 Exception」
try {
  $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
  $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

  // 從前端 axios 傳來的 JSON 取資料
  $data = json_decode(file_get_contents("php://input"), true);

  // isset() 判斷變數是否有被設定
  // 如果沒帶 product_id 或 price，就回應：『欄位沒帶齊！』，然後程式停止
  if (!isset($data['product_id']) || !isset($data['price'])) {
    // success: false -> 代表失敗,error: '缺少必要欄位' -> 說明錯在哪
    echo json_encode(['success' => false, 'error' => '缺少必要欄位']);
    exit();
  }

  $product_id = intval($data['product_id']);
  $price = floatval($data['price']);

  // 更新語法、綁定變數進去(防 SQL Injection)
  $sql = "UPDATE products SET price = :price WHERE product_id = :product_id";
  $stmt = $pdo->prepare($sql);
  // bindParam
  $stmt->bindParam(':price', $price);
  $stmt->bindParam(':product_id', $product_id);

  // 執行 SQL
  if ($stmt->execute()) {
    echo json_encode(['success' => true, 'message' => '產品更新成功']);
  } else {
    echo json_encode(['success' => false, 'message' => '產品更新失敗']);
  }

// 錯誤處理
} catch (PDOException $e) {
  echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}
