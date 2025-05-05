<template>
  <!-- 頂部導覽列 -->
  <Navbar v-if="showNavbar" />
  <!-- 主畫面切換 -->
  <router-view />
</template>

<script setup lang="ts">
// 可以先不寫邏輯，日後再加
import { useRoute } from 'vue-router'
import { computed } from 'vue'
import Navbar from './components/Navbar.vue'

const route = useRoute()

// 判斷當前路由，不是首頁就顯示 Navbar
const showNavbar = computed(() => route.path !== '/')
</script>

<style scoped>
header {
  background-color: #1976d2;
  color: #fff;
  padding: 16px;
  text-align: center;
  font-size: 1.5rem;
}
body {
  font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', sans-serif;
}

main {
  padding: 20px;
}
</style>
