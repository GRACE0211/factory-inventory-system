<!-- 查交易明細（進貨/出貨） -->
<template>
  <div class="transaction-view">
    <div style="text-align: center">
      <h2>庫存異動查詢</h2>
    </div>
    <div class="option-div">
      <!-- 供應商篩選 -->
      <!-- value預設是''(全部)，選單選了什麼，value就會變成什麼，並將data傳入selectedSupplier -->
      <div>
        選擇供應商：
        <select v-model="selectedSupplier">
          <option value="">全部</option>
          <option
            v-for="supplier in suppliers"
            :key="supplier.supplier_id"
            :value="Number(supplier.supplier_id)"
          >
            供應商{{ supplier.name }}
          </option>
        </select>
      </div>

      <!-- 產品篩選 -->
      <div>
        選擇產品：
        <select v-model="selectedProduct">
          <option value="">全部</option>
          <option
            v-for="product in products"
            :key="product.product_id"
            :value="Number(product.product_id)"
          >
            {{ product.name }}
          </option>
        </select>
      </div>

      <!-- 日期篩選 -->
      <div>
        日期區間：
        <input type="date" v-model="startDate" />
        ~
        <input type="date" v-model="endDate" />
      </div>
      <!-- 交易類型篩選 -->
      <div>
        選擇交易類型：
        <select v-model="selectedTransactionType">
          <option value="">全部</option>
          <option value="IN">進貨 (IN)</option>
          <option value="OUT">出貨 (OUT)</option>
        </select>
      </div>

      <!-- 查詢按鈕 -->
      <button @click="filterTransactions">查詢</button>
    </div>

    <!-- 查詢結果表格 -->
    <div class="transaction-view-table">
      <div class="table-container">
        <table>
          <thead>
            <tr>
              <th>
                供應商
                <button @click="handleSort('supplier_id')">
                  {{ sortField === 'supplier_id' ? (sortOrder === 'asc' ? '▲' : '▼') : '▲' }}
                </button>
              </th>
              <th>
                產品
                <button @click="handleSort('product_id')">
                  {{ sortField === 'product_id' ? (sortOrder === 'asc' ? '▲' : '▼') : '▲' }}
                </button>
              </th>
              <th>交易類型</th>
              <th>數量</th>
              <th>
                交易日期
                <button @click="handleSort('transaction_date')">
                  {{ sortField === 'transaction_date' ? (sortOrder === 'asc' ? '▲' : '▼') : '▲' }}
                </button>
              </th>
            </tr>
          </thead>
        </table>
        <div class="scroll-container">
          <table>
            <tbody>
              <tr
                v-for="tx in filteredTransactions"
                :key="tx.transaction_id"
                :class="{
                  IN: tx.transaction_type == 'IN',
                  OUT: tx.transaction_type == 'OUT',
                }"
              >
                <td>供應商{{ getSupplierName(tx.supplier_id) }}</td>
                <td>
                  {{ getProductName(tx.product_id) }}
                </td>

                <td style="font-weight: 900">
                  {{ tx.transaction_type }}
                </td>
                <td>{{ tx.quantity }}</td>
                <td>{{ tx.transaction_date }}</td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
    </div>

    <!-- 統計結果 -->
    <div v-if="filteredTransactions.length > 0" class="search-result">
      <h3>統計結果</h3>
      <p>總進貨量：{{ totalIn }}</p>
      <p>總出貨量：{{ totalOut }}</p>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import axios from 'axios'

// 狀態
const suppliers = ref([])
const products = ref([])
console.log(suppliers)
const transactions = ref([])
const filteredTransactions = ref([])

// 篩選條件
const selectedSupplier = ref('')
const selectedProduct = ref('')
const startDate = ref('')
const endDate = ref('')
const selectedType = ref('')
const selectedTransactionType = ref('')

// 統計
const totalIn = ref(0)
const totalOut = ref(0)

// 排序
const sortField = ref('')
const sortOrder = ref('asc')

// 從後端資料庫抓資料
const fetchProducts = async () => {
  try {
    const response = await axios.get('http://localhost/factory_inventory_api/get_products.php')
    products.value = response.data
  } catch (error) {
    console.error('獲取產品資料錯誤:', error)
  }
}

const fetchSuppliers = async () => {
  try {
    const response = await axios.get('http://localhost/factory_inventory_api/get_suppliers.php')
    suppliers.value = response.data
  } catch (error) {
    console.error('獲取供應商資料錯誤:', error)
  }
}

const fetchTransactions = async () => {
  try {
    const response = await axios.get('http://localhost/factory_inventory_api/get_transactions.php')
    transactions.value = response.data
    filteredTransactions.value = transactions.value
    calcSummary()
  } catch (error) {
    console.error('獲取交易資料失敗:', error)
  }
}

// 把表格的產品ID改成名字
const getProductName = (productId) => {
  const product = products.value.find((p) => p.product_id === productId)
  return product ? product.name : '未知產品'
}

// 把表格的供應商ID改成名字
const getSupplierName = (supplierId) => {
  const supplier = suppliers.value.find((s) => s.supplier_id === supplierId)
  return supplier ? supplier.name : '未知供應商'
}

// 篩選邏輯
// filter():將符合條件的資料過濾出來，組成新的陣列
// tx是每一筆交易資料，判斷每一筆資料是否符合條件(符合 -> true,不符 -> false)
// 4個條件皆為true才會加到新的filterTransactions陣列裡面
const filterTransactions = () => {
  filteredTransactions.value = transactions.value.filter((tx) => {
    const supplierMatch =
      selectedSupplier.value === '' || tx.supplier_id === Number(selectedSupplier.value)

    const productMatch =
      selectedProduct.value === '' || tx.product_id === Number(selectedProduct.value)

    const dateMatch =
      (startDate.value === '' || tx.transaction_date >= startDate.value) &&
      (endDate.value === '' || tx.transaction_date <= endDate.value)

    const typeMatch =
      selectedTransactionType.value === '' || tx.transaction_type === selectedTransactionType.value

    return supplierMatch && productMatch && dateMatch && typeMatch
  })

  calcSummary()
}

// 統計
const calcSummary = () => {
  totalIn.value = filteredTransactions.value
    // 過濾出transaction_type = 'IN'的資料
    .filter((tx) => tx.transaction_type === 'IN')
    // sum = 累積的結果，初始值為0
    .reduce((sum, tx) => sum + Number(tx.quantity), 0)

  totalOut.value = filteredTransactions.value
    // 過濾出transaction_type = 'OUT'的資料
    .filter((tx) => tx.transaction_type === 'OUT')
    .reduce((sum, tx) => sum + Number(tx.quantity), 0)
}

const handleSort = (field) => {
  if (sortField.value === field) {
    sortOrder.value = sortOrder.value === 'asc' ? 'desc' : 'asc'
  } else {
    sortField.value = field
    sortOrder.value = 'asc'
  }

  fetchSortedTransactions()
}

const fetchSortedTransactions = async () => {
  try {
    const params = {
      supplier_id: selectedSupplier.value,
      product_id: selectedProduct.value,
      start_date: startDate.value,
      end_date: endDate.value,
      transaction_type: selectedTransactionType.value,
      sort_field: sortField.value,
      sort_order: sortOrder.value,
    }

    const queryString = new URLSearchParams(params).toString()

    const response = await axios.get(
      `http://localhost/factory_inventory_api/get_sorted_transactions.php?${queryString}`,
    )

    transactions.value = response.data
    filteredTransactions.value = transactions.value
    calcSummary()
  } catch (error) {
    console.error('獲取交易資料失敗:', error)
  }
}

onMounted(() => {
  fetchTransactions()
  fetchProducts()
  fetchSuppliers()
})
</script>

<style scoped>
.transaction-view {
  margin-top: 110px;
}

.option-div {
  display: flex;
  flex-wrap: wrap;
  justify-content: center; /* 水平置中 */
  align-items: center; /* 垂直置中 */
  gap: 5px;
  margin: 0 auto; /* 外部左右置中 */
  max-width: 900px; /* 最寬不要撐滿 */
  text-align: center;
}

.option-div > div {
  margin: 5px 10px;
}

.IN {
  background-color: #54a2e28a;
}
.OUT {
  background-color: #cb757580;
}

.transaction-view-table {
  margin-top: 20px;
  /* border-radius: 10px; */
  display: flex;
  /* 水平置中 */
  justify-content: center;
  /* 垂直置中 */
  align-items: center;
  width: 100%;
}

.table-container {
  z-index: 100;
  padding: 10px;
  width: 80%;
  border-radius: 5px;
  border-collapse: collapse;
  width: 80%;
  cursor: pointer;
  box-shadow:
    rgba(50, 50, 93, 0.532) 0px 6px 12px -5px,
    rgba(0, 0, 0, 0.428) 0px 3px 7px -2px;
}

.table-container > table {
  table-layout: fixed; /* 讓表頭欄位對齊下面 */
  width: 100%;
  color: rgb(239, 244, 250);
  background-color: #0051a3;
  box-shadow:
    rgba(50, 50, 93, 0.532) 0px 6px 6px -5px,
    rgba(0, 0, 0, 0.428) 0px 3px 7px -2px;
  z-index: 100;
  /* overflow: visible; */
}
.scroll-container {
  width: 100%;
  max-height: 350px;
  overflow-y: auto;
  z-index: 1;
}

.scroll-container > table {
  width: 100%;
  table-layout: fixed;
  border-spacing: 0;
}

th,
td {
  padding: 8px;
  text-align: center;
}
.search-result {
  margin-top: 10px;
  display: flex;
  justify-content: center;
  align-items: center;
  gap: 10px;
}
</style>
