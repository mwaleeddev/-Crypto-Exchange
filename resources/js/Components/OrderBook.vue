<template>
    <div class="bg-white shadow rounded-lg p-6">
      <div class="flex justify-between items-center mb-4">
        <h2 class="text-lg font-semibold text-gray-800">Order Book</h2>
        <select v-model="selectedSymbol" @change="loadOrderBook"
                class="px-3 py-1 border border-gray-300 rounded-md text-sm">
          <option value="BTC">BTC/USD</option>
          <option value="ETH">ETH/USD</option>
        </select>
      </div>
  
      <div class="grid grid-cols-2 gap-6">
        <!-- Buy Orders -->
        <div>
          <h3 class="text-sm font-medium text-green-600 mb-2">Buy Orders</h3>
          <div class="space-y-1">
            <div v-for="order in buyOrders" :key="order.id"
                 class="flex justify-between text-sm p-2 hover:bg-gray-50 rounded">
              <span class="text-gray-600">{{ formatNumber(order.remaining_amount) }}</span>
              <span class="font-medium text-green-600">${{ formatNumber(order.price) }}</span>
              <span class="text-gray-500">${{ formatNumber(order.price * order.remaining_amount) }}</span>
            </div>
          </div>
        </div>
  
        <!-- Sell Orders -->
        <div>
          <h3 class="text-sm font-medium text-red-600 mb-2">Sell Orders</h3>
          <div class="space-y-1">
            <div v-for="order in sellOrders" :key="order.id"
                 class="flex justify-between text-sm p-2 hover:bg-gray-50 rounded">
              <span class="text-gray-600">{{ formatNumber(order.remaining_amount) }}</span>
              <span class="font-medium text-red-600">${{ formatNumber(order.price) }}</span>
              <span class="text-gray-500">${{ formatNumber(order.price * order.remaining_amount) }}</span>
            </div>
          </div>
        </div>
      </div>
    </div>
  </template>
  
  <script setup>
  import { ref, onMounted } from 'vue'
  import api from '../services/api'
  
  const selectedSymbol = ref('BTC')
  const buyOrders = ref([])
  const sellOrders = ref([])
  
  const loadOrderBook = async () => {
    try {
      const response = await api.get('/api/orders', {
        params: { symbol: selectedSymbol.value }
      })
      buyOrders.value = response.data.buy
      sellOrders.value = response.data.sell
    } catch (error) {
      console.error('Failed to load order book:', error)
    }
  }
  
  const formatNumber = (num) => {
    if (!num) return '0.00'
    return parseFloat(num).toLocaleString('en-US', {
      minimumFractionDigits: 2,
      maximumFractionDigits: 8
    })
  }
  
  // Refresh order book every 5 seconds
  onMounted(() => {
    loadOrderBook()
    setInterval(loadOrderBook, 5000)
  })
  </script>