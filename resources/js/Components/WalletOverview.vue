<template>
    <div class="bg-white shadow rounded-lg p-6">
      <h2 class="text-lg font-semibold text-gray-800 mb-4">Wallet Overview</h2>
      
      <!-- USD Balance -->
      <div class="mb-6 p-4 bg-blue-50 rounded-lg">
        <div class="flex justify-between items-center">
          <div>
            <p class="text-sm text-gray-600">USD Balance</p>
            <p class="text-2xl font-bold text-gray-800">${{ formatNumber(profile?.user?.balance) }}</p>
          </div>
          <div class="text-blue-600">
            <span class="text-2xl">$</span>
          </div>
        </div>
      </div>
  
      <!-- Asset Balances -->
      <div>
        <h3 class="text-md font-medium text-gray-700 mb-3">Assets</h3>
        <div class="space-y-3">
          <div v-for="asset in profile?.assets" :key="asset.symbol" 
               class="flex justify-between items-center p-3 bg-gray-50 rounded-lg">
            <div>
              <p class="font-medium text-gray-800">{{ asset.symbol }}</p>
              <p class="text-sm text-gray-600">Available: {{ formatNumber(asset.available) }}</p>
            </div>
            <div class="text-right">
              <p class="font-medium text-gray-800">{{ formatNumber(asset.amount) }}</p>
              <p class="text-xs text-gray-500">Locked: {{ formatNumber(asset.locked_amount) }}</p>
            </div>
          </div>
        </div>
      </div>
    </div>
  </template>
  
  <script setup>
  import { ref, onMounted, inject } from 'vue'
  import api from '../services/api'
  
  const refreshData = inject('refreshData')
  const profile = ref(null)
  
  const loadProfile = async () => {
    try {
      const response = await api.get('/api/profile')
      profile.value = response.data
    } catch (error) {
      console.error('Failed to load profile:', error)
    }
  }
  
  const formatNumber = (num) => {
    if (!num) return '0.00'
    return parseFloat(num).toLocaleString('en-US', {
      minimumFractionDigits: 2,
      maximumFractionDigits: 8
    })
  }
  
  onMounted(() => {
    loadProfile()
  })
  </script>