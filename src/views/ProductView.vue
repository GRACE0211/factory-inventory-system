<template>
  <div class="product-view" style="text-align: center">
    <h2>📦 產品清單</h2>
    <div class="product-table">
      <table>
        <thead style="background-color: #1976d27f">
          <tr>
            <th>產品ID</th>
            <th>產品名稱</th>
            <th>價格</th>
            <th>操作</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="item in products" :key="item.product_id">
            <td>{{ item.product_id }}</td>
            <td>{{ item.name }}</td>
            <!-- <td>${{ item.price }}</td> -->
            <!-- 如果在編輯狀態，就顯示 input -->
            <td v-if="editingId === item.product_id">
              <input v-model="editPrice" type="number" />
            </td>
            <!-- 否則顯示原本價格 -->
            <td v-else>${{ item.price }}</td>

            <td class="button-div">
              <!-- 編輯按鈕 / 儲存 & 取消 -->
              <!-- editingId==product_id 代表編輯中，顯示儲存/取消按鈕 -->
              <button v-if="editingId !== item.product_id" @click="startEdit(item)">編輯</button>
              <!-- editingId!=product_id 代表不在編輯中，顯示編輯按鈕 -->
              <div v-else>
                <button @click="saveEdit(item.product_id)">儲存</button>
                <button @click="cancelEdit" style="background-color: #f8ba00">取消</button>
              </div>
            </td>
          </tr>
        </tbody>
      </table>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import axios from 'axios'

// 創建響應式資料，當值改變時畫面也會跟著改變，() 裡面是初始值
const products = ref([])
const editingId = ref(null)
const editPrice = ref(0)

// 用ref創建的值需要.value存取
const fetchProducts = async () => {
  try {
    const response = await axios.get('http://localhost/factory_inventory_api/get_products.php')
    products.value = response.data
    // console.log(response)
    // console.log(products.value)
  } catch (error) {
    console.error('獲取產品資料錯誤:', error)
  }
}

// 開始編輯
const startEdit = (item) => {
  editingId.value = item.product_id
  editPrice.value = item.price
}

// 取消編輯
const cancelEdit = () => {
  editingId.value = null
  editPrice.value = 0
}

// 儲存修改 -> post('php',{要傳回去的值})
const saveEdit = async (productId) => {
  try {
    const response = await axios.post('http://localhost/factory_inventory_api/update_product.php', {
      product_id: productId,
      price: editPrice.value,
    })

    // .find()是在product.value陣列裡面，一筆一筆找出product_id符合的那筆資料
    if (response.data.success) {
      const product = products.value.find((p) => p.product_id === productId)
      product.price = editPrice.value
      cancelEdit()
      alert('更新成功！')
    } else {
      alert('更新失敗！')
    }
  } catch (error) {
    console.error(error)
    alert('發生錯誤！')
  }
}

// 網頁一觸發就會執行fetchProducts()，到後端抓資料
onMounted(() => {
  fetchProducts()
})
</script>

<style scoped>
.product-view {
  margin-top: 150px;
}
.product-table {
  margin-top: 50px;
  border-radius: 10px;
  display: flex;
  /* 水平置中 */
  justify-content: center;
  /* 垂直置中 */
  align-items: center;
  width: 100%;
}
table {
  border-radius: 5px;
  border-collapse: collapse;
  width: 60%;
  cursor: pointer;
  box-shadow:
    rgba(50, 50, 93, 0.532) 0px 6px 12px -5px,
    rgba(0, 0, 0, 0.428) 0px 3px 7px -2px;
  overflow: hidden;
}
th,
td {
  padding: 8px;
  text-align: center;
  border-bottom: 1px solid #dddddd;
}
tr:nth-of-type(even) td {
  background-color: #f3f3f3;
}

button {
  appearance: none;
  background-color: #006bd7;
  border: 2px;
  border-radius: 10px;
  box-sizing: border-box;
  color: #ffffff;
  cursor: pointer;
  /* display: inline-block; */
  font-size: 14px;
  font-weight: 400;
  line-height: normal;
  margin: 3px;
  /* min-height: 20px; */
  min-width: 0;
  /* outline: none; */
  padding: 2px 2px;
  /* text-align: center; */
  text-decoration: none;
  transition: all 300ms cubic-bezier(0.23, 1, 0.32, 1);
  user-select: none;
  -webkit-user-select: none;
  touch-action: manipulation;
  width: 44%;
  will-change: transform;
}

button:disabled {
  pointer-events: none;
}

button:hover {
  box-shadow: rgba(0, 0, 0, 0.25) 0 3px 3px;
  transform: translateY(-2px);
}

button:active {
  box-shadow: none;
  transform: translateY(0);
}
</style>
