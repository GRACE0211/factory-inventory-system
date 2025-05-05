import { createRouter, createWebHistory } from 'vue-router'

import HomeView from '../views/HomeView.vue'
import ProductView from '../views/ProductView.vue'
import StockView from '../views/StockView.vue'
// import test from '../views/TestView.vue'
import TransactionView from '../views/TransactionsView.vue'
import TransactionInsert from '../views/Transaction_insert.vue'

const routes = [
  {
    path: '/',
    name: 'Home',
    component: HomeView,
  },
  {
    path: '/products',
    name: 'Product',
    component: ProductView,
  },
  {
    path: '/stock',
    name: 'Stock',
    component: StockView,
  },
  {
    path: '/transactions',
    name: 'Transactions',
    component: TransactionView,
  },
  {
    path: '/transaction-insert',
    name: 'TransactionInsert',
    component: TransactionInsert,
  },
  // {
  //   path: '/test',
  //   name: 'test',
  //   component: test,
  // },
]

const router = createRouter({
  history: createWebHistory(import.meta.env.BASE_URL),
  routes,
})

export default router
