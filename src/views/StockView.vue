<template>
  <div class="stock-view">
    <div style="text-align: center">
      <h2>即時庫存查詢（可勾選供應商與產品）</h2>

      <!-- 供應商勾選 -->
      <div style="margin-bottom: 10px">
        <label v-for="supplier in suppliers" :key="supplier.supplier_id" style="margin-right: 10px">
          <input type="checkbox" :value="supplier.supplier_id" v-model="selectedSuppliers" />
          供應商 {{ supplier.name }}
        </label>
      </div>

      <!-- 產品勾選 -->
      <div>
        <label v-for="product in products" :key="product.product_id" style="margin-right: 10px">
          <input type="checkbox" :value="product.product_id" v-model="selectedProducts" />
          {{ product.name }}
        </label>
      </div>
    </div>

    <!-- 資料表 -->
    <div class="stock-table">
      <div class="table-container">
        <table>
          <thead>
            <tr>
              <th>供應商</th>
              <th>產品名稱</th>
              <th>庫存數量</th>
              <th>狀態</th>
            </tr>
          </thead>
        </table>
        <div class="scroll-container">
          <table>
            <tbody>
              <tr
                v-for="item in filteredProducts"
                :key="item.product_id + '-' + item.supplier_id"
                :class="{ lowStock: item.remaining_quantity <= 80 }"
              >
                <td>{{ getSupplierName(item.supplier_id) }}</td>
                <td>{{ getProductName(item.product_id) }}</td>
                <td>{{ item.remaining_quantity }}</td>
                <td v-if="item.remaining_quantity <= 80">⚠️ 低庫存</td>
                <td v-else>✅ 正常</td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import axios from 'axios'

const suppliers = ref([])
const products = ref([])
const stocks = ref([])

const selectedSuppliers = ref([])
const selectedProducts = ref([])

const fetchSuppliers = async () => {
  const response = await axios.get('http://localhost/factory_inventory_api/get_suppliers.php')
  suppliers.value = response.data
}

const fetchProducts = async () => {
  const response = await axios.get('http://localhost/factory_inventory_api/get_products.php')
  products.value = response.data
}

const fetchInventory = async () => {
  const response = await axios.get('http://localhost/factory_inventory_api/get_inventory.php')
  stocks.value = response.data
}

// 根據stocks.value, selectedSuppliers.value, selectedProducts.value 自動更新
const filteredProducts = computed(() => {
  // .filter() -> 從庫存資料中篩出符合條件的項目
  return stocks.value.filter((item) => {
    // length === 0 代表沒有勾選 -> 全部顯示
    const supplierMatch =
      selectedSuppliers.value.length === 0 || selectedSuppliers.value.includes(item.supplier_id)
    const productMatch =
      selectedProducts.value.length === 0 || selectedProducts.value.includes(item.product_id)
    return supplierMatch && productMatch
  })
})

// 根據供應商ID找出對應的供應商名字
const getSupplierName = (supplierId) => {
  const supplier = suppliers.value.find((s) => s.supplier_id === supplierId)
  return supplier ? supplier.name : '未知供應商'
}

// 根據產品ID找出對應產品名
const getProductName = (productId) => {
  const product = products.value.find((p) => p.product_id === productId)
  return product ? product.name : '未知產品'
}

onMounted(() => {
  fetchSuppliers()
  fetchProducts()
  fetchInventory()
})
</script>

<style scoped>
.stock-view {
  margin-top: 110px;
}
.lowStock {
  background-color: rgb(238, 176, 176);
}

.stock-table {
  margin-top: 20px;
  display: flex;
  justify-content: center;
  align-items: center;
  width: 100%;
}

.table-container {
  border: none;
  z-index: 100;
  padding: 10px;
  width: 80%;
  border-radius: 5px;
  cursor: pointer;
  box-shadow:
    rgba(50, 50, 93, 0.532) 0px 6px 12px -5px,
    rgba(0, 0, 0, 0.428) 0px 3px 7px -2px;
}

.table-container > table {
  border: none;
  table-layout: fixed;
  width: 100%;
  color: rgb(239, 244, 250);
  background-color: #0051a3;
  box-shadow:
    rgba(50, 50, 93, 0.532) 0px 6px 6px -5px,
    rgba(0, 0, 0, 0.428) 0px 3px 7px -2px;
}

.scroll-container {
  max-height: 350px; /* 你自己定義的高度 */
  overflow-y: auto;
  z-index: 1;
}

.scroll-container > table {
  width: 100%;
  table-layout: fixed;
  border-spacing: 0;
}

thead th {
  position: sticky;
  top: 0;
  z-index: 2;
}
tbody td {
  z-index: 0;
}

th,
td {
  border: none;
  padding: 8px;
  text-align: center;
}
</style>
