<template>
  <div class="home">
    <!-- <h2>系統主頁</h2> -->

    <div class="button-grid">
      <router-link to="/products" class="button-link">
        <button>產品清單</button>
      </router-link>

      <router-link to="/stock" class="button-link">
        <button>庫存查詢</button>
      </router-link>

      <router-link to="/transactions" class="button-link">
        <button>交易紀錄</button>
      </router-link>

      <router-link to="/transaction-insert" class="button-link">
        <button>新增交易</button>
      </router-link>
    </div>
  </div>
</template>

<script setup lang="ts">
// 不需要邏輯，因為是 router-link 導頁
</script>

<style scoped>
.home {
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  height: 100vh; /* 讓它垂直置中 */
  text-align: center;
}

.button-grid {
  display: grid;
  grid-template-columns: repeat(2, 200px); /* 兩欄，每個寬 200px 可調 */
  grid-template-rows: repeat(2, 200px); /* 兩行，每個高 200px 可調 */
  gap: 20px; /* 按鈕之間間距，1rem = 16px，2rem = 約 32px（接近 2 公分）*/
}

/* .button-link {
  display: flex;
  align-items: center;
  justify-content: center;
} */

button {
  width: 100%;
  height: 100%;
  font-size: 25px;
  font-weight: 500;
  text-shadow: #1e558d 0px 5px 5px;
  background-color: #1976d2;
  color: #fff;
  border: none;
  border-radius: 10px;
  cursor: pointer;
  transition:
    background-color 0.45s,
    text-shadow 0.45s;
  box-shadow:
    rgba(50, 50, 93, 0.532) 0px 6px 12px -5px,
    rgba(0, 0, 0, 0.428) 0px 3px 7px -2px;
}

button:hover {
  background-color: #1255a1;
  text-shadow: #0f355b 0px 5px 5px;
  box-shadow:
    rgba(50, 50, 93, 0.724) 0px 6px 12px -2px,
    rgba(0, 0, 0, 0.614) 0px 3px 7px -0px;
  transition:
    background-color 0.45s,
    text-shadow 0.45s,
    box-shadow 0.3s;
}
</style>
