<template>
    <div class="min-h-screen bg-gray-900 text-gray-100">
        <!-- Navigation -->
        <nav class="bg-gray-800 border-b border-gray-700">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between h-16">
                    <div class="flex items-center">
                        <div class="flex items-center space-x-3">
                            <div class="w-8 h-8 bg-gradient-to-r from-blue-500 to-purple-600 rounded-lg"></div>
                            <h1 class="text-xl font-bold bg-gradient-to-r from-blue-400 to-purple-500 bg-clip-text text-transparent">
                                Crypto Exchange
                            </h1>
                        </div>
                        <div class="hidden md:flex ml-10 space-x-4">
                            <router-link to="/dashboard" 
                                         class="px-3 py-2 rounded-md text-sm font-medium hover:bg-gray-700">
                                Dashboard
                            </router-link>
                            <router-link to="/trade"
                                         class="px-3 py-2 rounded-md text-sm font-medium hover:bg-gray-700">
                                Trade
                            </router-link>
                        </div>
                    </div>
                    <div class="flex items-center space-x-4">
                        <div class="text-right">
                            <p class="text-sm text-gray-300">{{ user?.name }}</p>
                            <p class="text-xs text-gray-500">{{ user?.email }}</p>
                        </div>
                        <button @click="logout"
                                class="px-4 py-2 bg-red-600 hover:bg-red-700 text-white text-sm font-medium rounded-md transition">
                            Logout
                        </button>
                    </div>
                </div>
            </div>
        </nav>

        <!-- Main Content -->
        <main class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
            <!-- Stats Overview -->
            <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
                <div class="bg-gray-800 rounded-xl p-6 border border-gray-700">
                    <p class="text-sm text-gray-400">USD Balance</p>
                    <p class="text-2xl font-bold text-green-400">${{ formatNumber(user?.balance || 0) }}</p>
                </div>
                <div class="bg-gray-800 rounded-xl p-6 border border-gray-700">
                    <p class="text-sm text-gray-400">BTC Balance</p>
                    <p class="text-2xl font-bold text-yellow-400">{{ btcBalance || '0.00' }} BTC</p>
                </div>
                <div class="bg-gray-800 rounded-xl p-6 border border-gray-700">
                    <p class="text-sm text-gray-400">ETH Balance</p>
                    <p class="text-2xl font-bold text-purple-400">{{ ethBalance || '0.00' }} ETH</p>
                </div>
                <div class="bg-gray-800 rounded-xl p-6 border border-gray-700">
                    <p class="text-sm text-gray-400">24h Volume</p>
                    <p class="text-2xl font-bold text-blue-400">${{ formatNumber(24 * 3600) }}</p>
                </div>
            </div>

            <!-- Trading Interface -->
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <!-- Left Column: Order Form & Wallet -->
                <div class="lg:col-span-1 space-y-6">
                    <!-- Order Form -->
                    <div class="bg-gray-800 rounded-xl border border-gray-700 overflow-hidden">
                        <div class="p-4 border-b border-gray-700">
                            <h3 class="font-semibold text-lg">Place Order</h3>
                        </div>
                        <div class="p-6">
                            <div class="space-y-4">
                                <div>
                                    <label class="block text-sm text-gray-400 mb-2">Market</label>
                                    <select class="w-full bg-gray-900 border border-gray-700 rounded-lg px-3 py-2 text-white">
                                        <option>BTC/USD</option>
                                        <option>ETH/USD</option>
                                    </select>
                                </div>
                                <div class="grid grid-cols-2 gap-4">
                                    <button class="py-3 bg-green-600 hover:bg-green-700 rounded-lg font-medium">
                                        Buy
                                    </button>
                                    <button class="py-3 bg-red-600 hover:bg-red-700 rounded-lg font-medium">
                                        Sell
                                    </button>
                                </div>
                                <div>
                                    <label class="block text-sm text-gray-400 mb-2">Amount</label>
                                    <input type="number" 
                                           class="w-full bg-gray-900 border border-gray-700 rounded-lg px-3 py-2 text-white"
                                           placeholder="0.00">
                                </div>
                                <div>
                                    <label class="block text-sm text-gray-400 mb-2">Price</label>
                                    <input type="number" 
                                           class="w-full bg-gray-900 border border-gray-700 rounded-lg px-3 py-2 text-white"
                                           placeholder="0.00">
                                </div>
                                <button class="w-full py-3 bg-blue-600 hover:bg-blue-700 rounded-lg font-medium">
                                    Place Order
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Recent Trades -->
                    <div class="bg-gray-800 rounded-xl border border-gray-700">
                        <div class="p-4 border-b border-gray-700">
                            <h3 class="font-semibold">Recent Trades</h3>
                        </div>
                        <div class="p-4">
                            <div class="space-y-2">
                                <div v-for="trade in recentTrades" :key="trade.id"
                                     class="flex justify-between items-center py-2">
                                    <div>
                                        <span :class="trade.side === 'buy' ? 'text-green-400' : 'text-red-400'">
                                            {{ trade.side.toUpperCase() }}
                                        </span>
                                        <span class="text-gray-400 text-sm ml-2">{{ trade.symbol }}</span>
                                    </div>
                                    <div class="text-right">
                                        <p class="font-medium">${{ trade.price }}</p>
                                        <p class="text-sm text-gray-400">{{ trade.amount }}</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Right Column: Order Book & Chart -->
                <div class="lg:col-span-2 space-y-6">
                    <!-- Order Book -->
                    <div class="bg-gray-800 rounded-xl border border-gray-700">
                        <div class="p-4 border-b border-gray-700 flex justify-between items-center">
                            <h3 class="font-semibold text-lg">Order Book - BTC/USD</h3>
                            <div class="text-sm text-gray-400">Spread: ${{ spread }}</div>
                        </div>
                        <div class="p-4">
                            <div class="grid grid-cols-3 gap-4 mb-2 text-sm text-gray-400">
                                <div>Price (USD)</div>
                                <div>Amount (BTC)</div>
                                <div class="text-right">Total</div>
                            </div>
                            <div class="space-y-1">
                                <!-- Sell Orders -->
                                <div v-for="order in sellOrders" :key="order.id"
                                     class="grid grid-cols-3 gap-4 py-2 hover:bg-gray-750 rounded">
                                    <div class="text-red-400">{{ order.price }}</div>
                                    <div>{{ order.amount }}</div>
                                    <div class="text-right">{{ order.total }}</div>
                                </div>
                                
                                <!-- Market Price -->
                                <div class="py-3 text-center border-t border-b border-gray-700 my-2">
                                    <div class="text-2xl font-bold">{{ marketPrice }}</div>
                                    <div class="text-sm text-gray-400">Market Price</div>
                                </div>
                                
                                <!-- Buy Orders -->
                                <div v-for="order in buyOrders" :key="order.id"
                                     class="grid grid-cols-3 gap-4 py-2 hover:bg-gray-750 rounded">
                                    <div class="text-green-400">{{ order.price }}</div>
                                    <div>{{ order.amount }}</div>
                                    <div class="text-right">{{ order.total }}</div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Open Orders -->
                    <div class="bg-gray-800 rounded-xl border border-gray-700">
                        <div class="p-4 border-b border-gray-700">
                            <h3 class="font-semibold text-lg">Your Open Orders</h3>
                        </div>
                        <div class="overflow-x-auto">
                            <table class="w-full">
                                <thead>
                                    <tr class="text-sm text-gray-400 border-b border-gray-700">
                                        <th class="py-3 px-4 text-left">Time</th>
                                        <th class="py-3 px-4 text-left">Market</th>
                                        <th class="py-3 px-4 text-left">Side</th>
                                        <th class="py-3 px-4 text-left">Price</th>
                                        <th class="py-3 px-4 text-left">Amount</th>
                                        <th class="py-3 px-4 text-left">Filled</th>
                                        <th class="py-3 px-4 text-left">Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr v-for="order in openOrders" :key="order.id"
                                        class="border-b border-gray-700 hover:bg-gray-750">
                                        <td class="py-3 px-4">{{ order.time }}</td>
                                        <td class="py-3 px-4">{{ order.market }}</td>
                                        <td class="py-3 px-4">
                                            <span :class="order.side === 'buy' ? 'text-green-400' : 'text-red-400'">
                                                {{ order.side.toUpperCase() }}
                                            </span>
                                        </td>
                                        <td class="py-3 px-4">${{ order.price }}</td>
                                        <td class="py-3 px-4">{{ order.amount }}</td>
                                        <td class="py-3 px-4">{{ order.filled }}%</td>
                                        <td class="py-3 px-4">
                                            <span class="px-2 py-1 text-xs rounded-full bg-blue-900 text-blue-300">
                                                {{ order.status }}
                                            </span>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { useRouter } from 'vue-router'
import axios from 'axios'

const router = useRouter()
const user = ref(window.user || null)
const btcBalance = ref('0.00')
const ethBalance = ref('0.00')
const marketPrice = ref('45,328.50')
const spread = ref('12.50')

// Sample data
const recentTrades = ref([
    { id: 1, side: 'buy', symbol: 'BTC', price: '45,328.50', amount: '0.5' },
    { id: 2, side: 'sell', symbol: 'BTC', price: '45,320.00', amount: '1.2' },
    { id: 3, side: 'buy', symbol: 'ETH', price: '2,450.75', amount: '3.5' },
])

const sellOrders = ref([
    { id: 1, price: '45,340.00', amount: '0.85', total: '38,539.00' },
    { id: 2, price: '45,335.50', amount: '1.20', total: '54,402.60' },
    { id: 3, price: '45,332.00', amount: '0.50', total: '22,666.00' },
])

const buyOrders = ref([
    { id: 1, price: '45,315.00', amount: '1.50', total: '67,972.50' },
    { id: 2, price: '45,310.00', amount: '0.75', total: '33,982.50' },
    { id: 3, price: '45,305.00', amount: '2.25', total: '101,936.25' },
])

const openOrders = ref([
    { id: 1, time: '12:30:45', market: 'BTC/USD', side: 'buy', price: '45,000.00', amount: '0.1', filled: '0', status: 'Open' },
    { id: 2, time: '12:25:10', market: 'ETH/USD', side: 'sell', price: '2,500.00', amount: '2.0', filled: '50', status: 'Partial' },
])

const formatNumber = (num) => {
    return num.toLocaleString('en-US', {
        minimumFractionDigits: 2,
        maximumFractionDigits: 2
    })
}

const loadBalances = async () => {
    try {
        // First, ensure we have a CSRF token
        await axios.get('/sanctum/csrf-cookie');
        
        const response = await axios.get('/api/profile');
        user.value = response.data.user;
        // Extract BTC and ETH balances
        const assets = response.data.assets || [];
        const btcAsset = assets.find(a => a.symbol === 'BTC');
        const ethAsset = assets.find(a => a.symbol === 'ETH');
        btcBalance.value = btcAsset ? parseFloat(btcAsset.amount).toFixed(8) : '0.00';
        ethBalance.value = ethAsset ? parseFloat(ethAsset.amount).toFixed(8) : '0.00';
    } catch (error) {
        console.error('Failed to load balances:', error);
        // If unauthorized, redirect to login
        if (error.response?.status === 401) {
            window.location.href = '/login';
        }
    }
};

const logout = async () => {
    try {
        await axios.post('/logout')
        router.push('/login')
    } catch (error) {
        console.error('Logout failed:', error)
    }
}

onMounted(() => {
    loadBalances()
})
</script>