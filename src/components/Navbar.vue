// 導覽列
<template>
  <nav class="navbar">
    <h1>進出貨系統</h1>
    <ul>
      <li><router-link to="/">首頁</router-link></li>
      <li><router-link to="/products">產品清單</router-link></li>
      <li><router-link to="/stock">庫存查詢</router-link></li>
      <li><router-link to="/transactions">庫存異動查詢</router-link></li>
      <li><router-link to="/transaction-insert">新增交易</router-link></li>
      <!-- <li><router-link to="/test">測試</router-link></li> -->
    </ul>
  </nav>
</template>

<script setup lang="ts">
// 不需要邏輯，單純組件
</script>

<style scoped>
.navbar {
  position: fixed;
  top: 0;
  left: 0;
  width: 100%;
  z-index: 200;
  background-color: #1976d2;
  color: #fff;
  padding: 10px 20px;
  /* text-align: center; */
  font-size: 1rem;
  display: flex; /* → 讓 h1 和 ul 同一行 */
  justify-content: space-between; /* → 左右兩邊對齊 */
  align-items: center;
}

.navbar h1 {
  display: flex;
  font-size: 1.5rem;
}

ul {
  display: flex;
  justify-content: flex-end;
  list-style: none;
  /* margin-right: 10px; */
  padding-right: 40px;
}

li {
  margin-left: 10px;
}

a {
  color: #fff;
  text-decoration: none;
}

a.router-link-exact-active {
  font-weight: bold;
  border-bottom: 2px solid #fff;
}
</style>
