<template>
  <div class="transaction-insert">
    <h2>新增交易</h2>
    <div class="transaction-insert-view-table">
      <!-- 按下 type="submit" 的按鈕，會執行 submitTransaction() 這個函式 -->
      <form @submit.prevent="submitTransaction" class="table-container">
        <!-- 供應商選擇 -->
        <label>
          供應商：
          <select v-model="selectedSupplier">
            <option disabled value="">請選擇供應商</option>
            <option
              v-for="supplier in suppliers"
              :key="supplier.supplier_id"
              :value="supplier.supplier_id"
            >
              供應商 {{ supplier.name }}
            </option>
          </select>
        </label>

        <!-- 產品選擇 -->
        <label>
          產品：
          <select v-model="selectedProduct">
            <option disabled value="">請選擇產品</option>
            <option
              v-for="product in products"
              :key="product.product_id"
              :value="product.product_id"
            >
              {{ product.name }}
            </option>
          </select>
        </label>
        <!-- <div
          v-if="currentStock !== null"
          class="currentStock-div"
          :style="{
            color: currentStock <= 80 ? '#bd1550' : '#285943',
          }"
        >
          目前庫存：{{ currentStock }}
        </div> -->

        <!-- 交易類型 -->
        <label>
          交易類型：
          <select v-model="transactionType">
            <option disabled value="">請選擇類型</option>
            <option value="IN">進貨</option>
            <option value="OUT">出貨</option>
          </select>
        </label>

        <!-- 數量 -->
        <label>
          數量：
          <input type="number" v-model="quantity" min="10" step="10" />
        </label>

        <!-- <div
          v-if="transactionType"
          :style="{ color: futureStock <= 80 ? '#bd1550' : '#006e30' }"
          class="futureStock-div"
        >
          交易後庫存：{{ futureStock }}
        </div> -->

        <div v-if="currentStock !== null" class="showStock-div">
          <span
            v-if="currentStock !== null"
            class="currentStock-div"
            :style="{
              color: currentStock <= 80 ? '#d21054' : '#006e30',
            }"
          >
            目前庫存：{{ currentStock }}
          </span>
          <span
            v-if="transactionType"
            :style="{ color: futureStock <= 80 ? '#d21054' : '#006e30' }"
            class="futureStock-div"
          >
            ➡️ 交易後庫存：{{ futureStock }}
          </span>
        </div>

        <!-- 日期 -->
        <label>
          日期：
          <input type="date" v-model="transactionDate" />
        </label>

        <button type="submit" class="submit-button">送出</button>
      </form>
      <transition name="fade">
        <div v-if="errorMsg" class="error-div">
          <div>
            <button class="close-button" @click="errorMsg = ''">✖</button>
            <p>{{ errorMsg }}</p>
          </div>
        </div>
      </transition>
    </div>
  </div>
  <transition name="fade">
    <div v-if="showConfirm" class="confirm-div">
      <div>
        <button class="close-button" @click="showConfirm = false">✖</button>
        <p>
          確定要新增這筆交易嗎？<br />
          📦 交易後庫存：{{ futureStock }}
        </p>
        <button @click="confirmSubmit" class="confirmSubmit-button">確定</button>
      </div>
    </div>
  </transition>
</template>

<script setup>
import { ref, onMounted, watch, computed } from 'vue'
import { useRouter } from 'vue-router'
import axios from 'axios'

// 初始化
const router = useRouter()
const showConfirm = ref(false)

// 從後端抓到的資料
const suppliers = ref([])
const products = ref([])
const currentStock = ref(null)

// 表單欄位
const quantity = ref(10)
const selectedSupplier = ref('')
const selectedProduct = ref('')
const transactionType = ref('')
// console.log(transactionType)
const transactionDate = ref('')

// 錯誤訊息
const errorMsg = ref('')

// 抓資料
const fetchProducts = async () => {
  const response = await axios.get('http://localhost/factory_inventory_api/get_products.php')
  products.value = response.data
}

const fetchSuppliers = async () => {
  const response = await axios.get('http://localhost/factory_inventory_api/get_suppliers.php')
  suppliers.value = response.data
}

const fetchInventory = async () => {
  // 從後端抓庫存清單
  try {
    const response = await axios.get('http://localhost/factory_inventory_api/get_inventory.php')
    // console.log(response.data)
    // 根據選定的商品和供應商，找出對應的庫存項目
    const stock = response.data.find(
      (item) =>
        item.product_id === selectedProduct.value && item.supplier_id === selectedSupplier.value,
    )
    // 根據選定的商品和供應商，找出對應的庫存項目
    currentStock.value = stock ? Number(stock.remaining_quantity) : null
  } catch (error) {
    console.error('❌ 無法取得庫存資訊', error)
    currentStock.value = null
  }
}

// 自動監聽選擇變更時抓最新庫存
// 當[供應商/產品]改變，即執行()=>{...}
watch([selectedSupplier, selectedProduct], () => {
  // 若供應商/產品確實有值，則執行 fetchInventory()
  if (selectedSupplier.value && selectedProduct.value) {
    fetchInventory()
  } else {
    currentStock.value = null
  }
})

// 送出表單
const confirmSubmit = async () => {
  // 送一個 JSON 格式的資料（payload）
  const payload = {
    supplier_id: selectedSupplier.value,
    product_id: selectedProduct.value,
    transaction_type: transactionType.value,
    quantity: quantity.value,
    transaction_date: transactionDate.value,
  }

  const response = await axios.post(
    'http://localhost/factory_inventory_api/add_transaction.php',
    payload,
  )

  if (response.data.success) {
    alert('✅ 新增成功！即將返回交易查詢頁')
    router.push('/transactions')
  } else {
    alert('❌ 新增失敗：' + response.data.message)
  }
}

const submitTransaction = async () => {
  errorMsg.value = ''

  // ➤ 加入欄位未填的驗證
  if (
    !selectedSupplier.value ||
    !selectedProduct.value ||
    !transactionType.value ||
    !transactionDate.value ||
    !quantity.value
  ) {
    errorMsg.value = '⚠️ 選項不得為空，請確認所有欄位已填寫 ⚠️'
    return
  }

  await fetchInventory()

  if (transactionType.value === 'OUT') {
    const remaining = currentStock.value - quantity.value
    if (quantity.value > currentStock.value) {
      errorMsg.value = `⚠️ 出貨數量不可超過庫存 (${currentStock.value}) ⚠️`
      return
    }
    if (remaining < 50) {
      errorMsg.value = `⚠️ 出貨後庫存僅剩 ${remaining}，禁止出貨 ⚠️`
      return
    }
  }

  showConfirm.value = true // 顯示"確認新增這筆交易？"訊息框
}

const futureStock = computed(() => {
  if (transactionType.value === 'IN') return currentStock.value + quantity.value
  if (transactionType.value === 'OUT') return currentStock.value - quantity.value
  return futureStock.value
})

onMounted(() => {
  fetchProducts()
  fetchSuppliers()
})
</script>

<style scoped>
.transaction-insert {
  margin-top: 130px;
  text-align: center;
}

.transaction-insert > h2 {
  margin-bottom: 30px;
}

.transaction-insert-view-table {
  display: flex;
  /* 水平置中 */
  justify-content: center;
  /* 垂直置中 */
  align-items: center;
  width: 100%;
}

/* form {
  display: inline-block;
  text-align: left;
  border: 1px solid #ccc;
  border-radius: 10px;
} */

.table-container {
  display: inline-block;
  place-items: center;
  z-index: 100;
  padding: 20px;
  background-color: #d1dfed;
  border-radius: 5px;
  border-collapse: collapse;
  width: 50%;
  cursor: pointer;
  box-shadow:
    rgba(50, 50, 93, 0.532) 0px 6px 12px -5px,
    rgba(0, 0, 0, 0.428) 0px 3px 7px -2px;
}

label {
  display: block;
  margin-bottom: 15px;
}

input,
select {
  width: 200px;
  padding: 5px;
  margin-top: 5px;
}

/* .currentStock-div {
  background-color: #01468b4a;
  margin-bottom: 5px;
  padding: 5px 0;
  font-weight: bold;
  width: 60%;
  border-radius: 5px;
} */

/* .futureStock-div {
  background-color: #01468b4a;
  margin-bottom: 5px;
  padding: 5px 0;
  font-weight: bold;
  width: 60%;
  border-radius: 5px;
} */

.showStock-div {
  background-color: #5a8fbd4a;
  margin-bottom: 5px;
  padding: 10px 0;
  font-weight: bold;
  width: 70%;
  border-radius: 5px;
}

.submit-button {
  border: 4px solid;
  border-radius: 5px;
  border-color: #006bd7;
  background-color: #006bd7;
  color: #ffffff;
  cursor: pointer;
  font-size: 14px;
  font-weight: 500;
  margin: 3px;
  padding: 2px 50px;
  text-decoration: none;
  transition: all 300ms cubic-bezier(0.23, 1, 0.32, 1);
  user-select: none;
  -webkit-user-select: none;
  touch-action: manipulation;
  width: 100%;
  will-change: transform;
}

.submit-button:disabled {
  pointer-events: none;
}

.submit-button:hover {
  box-shadow: rgba(0, 0, 0, 0.25) 0 3px 3px;
  transform: translateY(-2px);
  border: 4px solid;
  border-radius: 5px;
  border-color: #006bd7;
  background-color: #ffffff;
  color: #006bd7;
}

.submit-button:active {
  box-shadow: none;
  transform: translateY(0);
}

/* 淡入淡出動畫 */
.fade-enter-active,
.fade-leave-active {
  transition: opacity 0.2s ease;
}
.fade-enter-from,
.fade-leave-to {
  opacity: 0;
}

.error-div {
  position: fixed;
  top: 0;
  left: 0;
  width: 100vw;
  height: 100vh;
  z-index: 9999;
  display: flex;
  justify-content: center;
  align-items: center;

  background-color: rgba(0, 0, 0, 0.414); /* 半透明背景 */
}

.error-div > div {
  position: relative;
  background-color: #fff6c1;
  width: 50%;
  height: 30%;
  border: 5px solid;
  border-radius: 2%;
  border-color: #6b3601;
  display: flex;
  justify-content: center;
  align-items: center;
  box-shadow:
    rgba(29, 29, 60, 0.532) 0px 6px 12px -5px,
    rgba(0, 0, 0, 0.428) 0px 3px 7px -2px;
}

.error-div > p {
  background-color: #ffd900;
  color: #006bd7;
  font-weight: bold;
  padding: 20px 30px;
  border-radius: 10px;
  box-shadow: 0 0 10px rgba(0, 0, 0, 0.3);
}

.confirm-div {
  position: fixed;
  top: 0;
  left: 0;
  width: 100vw;
  height: 100vh;
  z-index: 9999;
  display: flex;
  justify-content: center;
  align-items: center;

  background-color: rgba(0, 0, 0, 0.414); /* 半透明背景 */
}

.confirm-div > div {
  display: grid;
  justify-content: center;
  place-items: center;
  position: relative;
  background-color: #fff6c1;
  width: 50%;
  height: 30%;
  border: 5px solid;
  border-radius: 2%;
  border-color: #6b3601;
  box-shadow:
    rgba(29, 29, 60, 0.532) 0px 6px 12px -5px,
    rgba(0, 0, 0, 0.428) 0px 3px 7px -2px;
}

.confirm-div > div > p {
  padding: 30px 20px;
}

.confirm-div > div > .confirmSubmit-button {
  border: 4px solid;
  border-radius: 5px;
  border-color: #6b3601;
  background-color: #6b3601;
  color: #ffffff;
  cursor: pointer;
  font-size: 14px;
  font-weight: 500;
  margin: 3px;
  padding: 2px 0;
  text-decoration: none;
  transition: all 300ms cubic-bezier(0.23, 1, 0.32, 1);
  user-select: none;
  -webkit-user-select: none;
  touch-action: manipulation;
  width: 50%;
  place-items: center;
  will-change: transform;
}

.confirm-div > div > .confirmSubmit-button:disabled {
  pointer-events: none;
}

.confirm-div > div > .confirmSubmit-button:hover {
  box-shadow: rgba(0, 0, 0, 0.25) 0 3px 3px;
  transform: translateY(-2px);
  border: 4px solid;
  border-radius: 5px;
  border-color: #6b3601;
  background-color: #ffffff;
  color: #6b3601;
}

.confirm-div > div > .confirmSubmit-button:active {
  box-shadow: none;
  transform: translateY(0);
}

.close-button {
  position: absolute;
  top: 0px;
  right: 0px;
  padding: 6px 8px;
  background: none;
  border: none;
  font-size: 20px;
  font-weight: bold;
  color: #c16200;
  cursor: pointer;
}
.close-button:hover {
  color: #6b3601;
  background-color: red;
}
</style>
