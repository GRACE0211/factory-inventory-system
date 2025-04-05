<template>
  <Navbar v-if="showNavbar" />
  <!-- 頂部導覽列 -->
  <router-view />
  <!-- 主畫面切換 -->
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

main {
  padding: 20px;
}
</style>
