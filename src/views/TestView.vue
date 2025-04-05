<template>
  <div v-for="item in array" key="item.product_id">
    {{ item.supplier_id }}/ {{ item.product_id }}/
    {{ item.remaining_quantity }}
  </div>

  <div>
    <p
      v-if="currentStock !== null"
      class="currentStock-div"
      :style="{
        color: currentStock <= 80 ? '#bd1550' : '#285943',
      }"
    >
      目前庫存：{{ currentStock }}
    </p>
    <p
      v-if="transactionType"
      :style="{ color: futureStock <= 80 ? '#bd1550' : '#285943' }"
      class="futureStock-div"
    >
      交易後庫存：{{ futureStock }}
    </p>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import axios from 'axios'

const array = ref([])
console.log(array)

const getData = async () => {
  try {
    const response = await axios.get('http://localhost/factory_inventory_api/test.php')
    array.value = response.data
  } catch (error) {
    console.error('獲取產品資料錯誤:', error)
  }
}

// 網頁一觸發就會執行fetchProducts()，到後端抓資料
onMounted(() => {
  getData()
})
</script>

<style scoped></style>
