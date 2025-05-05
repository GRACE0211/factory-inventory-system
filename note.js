// ProductView
// 初始化頁面
onMounted(() => {
  fetchInventory()
})
// 先從後端料庫取得產品
const fetchInventory = async()

// 如果要編輯價錢
// 1. 按編輯 -> 觸發startEdit(item)
editingId = item.product_id
editPrice = item.price

// 2. 取消編輯 -> 觸發cancelEdit()
editingId = null
editPrice = 0

// 將前端資料送到後端資料庫儲存
const saveEdit = async (productId) => {
  const response = await axios.post('.php', {
    product_id: productId,
    price: editPrice.value,
  })
}

// 後端php是透過 $_POST['product_id'] 或 $_POST['price'] 接值

// StockView
