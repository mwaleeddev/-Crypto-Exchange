<template>
    <div class="min-h-screen bg-gray-100">
      <nav class="bg-white shadow-sm">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
          <div class="flex justify-between h-16">
            <div class="flex items-center">
              <h1 class="text-xl font-semibold text-gray-800">Crypto Exchange</h1>
            </div>
            <div class="flex items-center space-x-4">
              <span class="text-gray-600">{{ user?.name }}</span>
              <button @click="logout" class="text-red-600 hover:text-red-800">
                Logout
              </button>
            </div>
          </div>
        </div>
      </nav>
  
      <main class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
        <div class="px-4 py-6 sm:px-0">
          <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Left Column: Wallet and Order Form -->
            <div class="lg:col-span-1 space-y-6">
              <WalletOverview />
              <OrderForm @order-placed="refreshData" />
            </div>
  
            <!-- Right Column: Orderbook and Orders -->
            <div class="lg:col-span-2 space-y-6">
              <OrderBook />
              <UserOrders />
            </div>
          </div>
        </div>
      </main>
    </div>
  </template>
  
  <script setup>
  import { ref, onMounted, provide } from 'vue'
  import { useRouter } from 'vue-router'
  import WalletOverview from './components/WalletOverview.vue'
  import OrderForm from './components/OrderForm.vue'
  import OrderBook from './components/OrderBook.vue'
  import UserOrders from './components/UserOrders.vue'
  import Echo from './services/echo'
  import api from './services/api'
  
  const router = useRouter()
  const user = ref(null)
  
  const loadProfile = async () => {
    try {
      const response = await api.get('/api/profile')
      user.value = response.data.user
    } catch (error) {
      console.error('Failed to load profile:', error)
    }
  }
  
  const logout = async () => {
    await api.post('/logout')
    router.push('/login')
  }
  
  const refreshData = () => {
    loadProfile()
    // Components will refresh their own data via their props/emits
  }
  
  onMounted(() => {
    loadProfile()
    
    // Listen for order matched events
    Echo.private(`user.${user.value?.id}`)
      .listen('.order.matched', (data) => {
        console.log('Order matched:', data)
        refreshData()
        // Show notification
        alert(`Order matched! Price: ${data.price}, Amount: ${data.amount}`)
      })
  })
  
  provide('refreshData', refreshData)
  </script>