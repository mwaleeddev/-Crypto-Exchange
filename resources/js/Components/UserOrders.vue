<template>
    <div class="bg-white shadow rounded-lg p-6">
      <h2 class="text-lg font-semibold text-gray-800 mb-4">My Orders</h2>
      
      <!-- Filters -->
      <div class="flex space-x-2 mb-4">
        <select v-model="filters.status" @change="loadOrders"
                class="px-3 py-1 border border-gray-300 rounded-md text-sm">
          <option value="">All Status</option>
          <option value="open">Open</option>
          <option value="filled">Filled</option>
          <option value="cancelled">Cancelled</option>
        </select>
        <select v-model="filters.symbol" @change="loadOrders"
                class="px-3 py-1 border border-gray-300 rounded-md text-sm">
          <option value="">All Symbols</option>
          <option value="BTC">BTC</option>
          <option value="ETH">ETH</option>
        </select>
        <select v-model="filters.side" @change="loadOrders"
                class="px-3 py-1 border border-gray-300 rounded-md text-sm">
          <option value="">All Sides</option>
          <option value="buy">Buy</option>
          <option value="sell">Sell</option>
        </select>
      </div>
  
      <!-- Orders Table -->
      <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
          <thead>
            <tr>
              <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Time</th>
              <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Symbol</th>
              <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Side</th>
              <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Price</th>
              <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Amount</th>
              <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Filled</th>
              <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
              <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-gray-200">
            <tr v-for="order in orders" :key="order.id">
              <td class="px-4 py-3 text-sm text-gray-500">{{ formatDate(order.created_at) }}</td>
              <td class="px-4 py-3 text-sm font-medium text-gray-800">{{ order.symbol }}</td>
              <td class="px-4 py-3 text-sm">
                <span :class="order.side === 'buy' ? 'text-green-600' : 'text-red-600'"
                      class="font-medium">
                  {{ order.side.toUpperCase() }}
                </span>
              </td>
              <td class="px-4 py-3 text-sm text-gray-800">${{ formatNumber(order.price) }}</td>
              <td class="px-4 py-3 text-sm text-gray-800">{{ formatNumber(order.amount) }}</td>
              <td class="px-4 py-3 text-sm text-gray-800">{{ formatNumber(order.filled_amount) }}</td>
              <td class="px-4 py-3 text-sm">
                <span :class="getStatusClass(order.status)"
                      class="px-2 py-1 rounded-full text-xs font-medium">
                  {{ order.status.toUpperCase() }}
                </span>
              </td>
              <td class="px-4 py-3 text-sm">
                <button v-if="order.status === 'open'" @click="cancelOrder(order.id)"
                        class="text-red-600 hover:text-red-800 text-sm font-medium">
                  Cancel
                </button>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
  
      <!-- Empty State -->
      <div v-if="orders.length === 0" class="text-center py-8 text-gray-500">
        No orders found
      </div>
    </div>
  </template>
  
  <script setup>
  import { ref, onMounted, inject } from 'vue'
  import api from '../services/api'
  
  const refreshData = inject('refreshData')
  const orders = ref([])
  const filters = ref({
    status: '',
    symbol: '',
    side: ''
  })
  
  const loadOrders = async () => {
    try {
      const response = await api.get('/api/orders/user')
      let filtered = response.data
      
      if (filters.value.status) {
        filtered = filtered.filter(o => o.status === filters.value.status)
      }
      if (filters.value.symbol) {
        filtered = filtered.filter(o => o.symbol === filters.value.symbol)
      }
      if (filters.value.side) {
        filtered = filtered.filter(o => o.side === filters.value.side)
      }
      
      orders.value = filtered
    } catch (error) {
      console.error('Failed to load orders:', error)
    }
  }
  
  const cancelOrder = async (orderId) => {
    if (!confirm('Are you sure you want to cancel this order?')) return
    
    try {
      await api.post(`/api/orders/${orderId}/cancel`)
      alert('Order cancelled successfully')
      loadOrders()
      refreshData()
    } catch (error) {
      alert(error.response?.data?.error || 'Failed to cancel order')
    }
  }
  
  const getStatusClass = (status) => {
    switch (status) {
      case 'open': return 'bg-blue-100 text-blue-800'
      case 'filled': return 'bg-green-100 text-green-800'
      case 'cancelled': return 'bg-gray-100 text-gray-800'
      default: return 'bg-gray-100 text-gray-800'
    }
  }
  
  const formatNumber = (num) => {
    if (!num) return '0.00'
    return parseFloat(num).toLocaleString('en-US', {
      minimumFractionDigits: 2,
      maximumFractionDigits: 8
    })
  }
  
  const formatDate = (dateString) => {
    return new Date(dateString).toLocaleTimeString()
  }
  
  onMounted(() => {
    loadOrders()
    // Refresh orders every 10 seconds
    setInterval(loadOrders, 10000)
  })
  </script>