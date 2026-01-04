<template>
    <div class="bg-white shadow rounded-lg p-6">
      <h2 class="text-lg font-semibold text-gray-800 mb-4">Place Limit Order</h2>
      
      <form @submit.prevent="placeOrder" class="space-y-4">
        <!-- Symbol -->
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-1">Symbol</label>
          <select v-model="form.symbol" 
                  class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">
            <option value="BTC">BTC/USD</option>
            <option value="ETH">ETH/USD</option>
          </select>
        </div>
  
        <!-- Side -->
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-1">Side</label>
          <div class="flex space-x-2">
            <button type="button"
                    @click="form.side = 'buy'"
                    :class="form.side === 'buy' ? 'bg-green-600 text-white' : 'bg-gray-200 text-gray-700'"
                    class="flex-1 py-2 px-4 rounded-md font-medium transition-colors">
              Buy
            </button>
            <button type="button"
                    @click="form.side = 'sell'"
                    :class="form.side === 'sell' ? 'bg-red-600 text-white' : 'bg-gray-200 text-gray-700'"
                    class="flex-1 py-2 px-4 rounded-md font-medium transition-colors">
              Sell
            </button>
          </div>
        </div>
  
        <!-- Price -->
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-1">Price (USD)</label>
          <input v-model="form.price" type="number" step="0.01" min="0.01"
                 class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                 placeholder="Enter price">
        </div>
  
        <!-- Amount -->
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-1">Amount</label>
          <input v-model="form.amount" type="number" step="0.00000001" min="0.00000001"
                 class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                 placeholder="Enter amount">
        </div>
  
        <!-- Total and Fee Preview -->
        <div class="p-4 bg-gray-50 rounded-md">
          <div class="flex justify-between text-sm mb-1">
            <span class="text-gray-600">Total {{ form.side === 'buy' ? 'Cost' : 'Receive' }}:</span>
            <span class="font-medium">${{ calculateTotal() }}</span>
          </div>
          <div class="flex justify-between text-sm">
            <span class="text-gray-600">Commission (1.5%):</span>
            <span class="font-medium">${{ calculateCommission() }}</span>
          </div>
        </div>
  
        <!-- Submit Button -->
        <button type="submit" :disabled="isSubmitting"
                class="w-full py-3 px-4 bg-blue-600 hover:bg-blue-700 disabled:bg-gray-400 text-white font-medium rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors">
          {{ isSubmitting ? 'Placing Order...' : `Place ${form.side === 'buy' ? 'Buy' : 'Sell'} Order` }}
        </button>
      </form>
    </div>
  </template>
  
  <script setup>
  import { ref, inject } from 'vue'
  import api from '../services/api'
  
  const emit = defineEmits(['order-placed'])
  const refreshData = inject('refreshData')
  
  const form = ref({
    symbol: 'BTC',
    side: 'buy',
    price: '',
    amount: ''
  })
  
  const isSubmitting = ref(false)
  
  const calculateTotal = () => {
    if (!form.value.price || !form.value.amount) return '0.00'
    const total = parseFloat(form.value.price) * parseFloat(form.value.amount)
    return total.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 })
  }
  
  const calculateCommission = () => {
    if (!form.value.price || !form.value.amount) return '0.00'
    const total = parseFloat(form.value.price) * parseFloat(form.value.amount)
    const commission = total * 0.015
    return commission.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 })
  }
  
  const placeOrder = async () => {
    if (!form.value.price || !form.value.amount || parseFloat(form.value.price) <= 0 || parseFloat(form.value.amount) <= 0) {
      alert('Please enter valid price and amount')
      return
    }
  
    isSubmitting.value = true
    try {
      const response = await api.post('/api/orders', form.value)
      alert('Order placed successfully!')
      form.value.price = ''
      form.value.amount = ''
      
      // Refresh data
      refreshData()
      emit('order-placed')
    } catch (error) {
      alert(error.response?.data?.error || 'Failed to place order')
    } finally {
      isSubmitting.value = false
    }
  }
  </script>