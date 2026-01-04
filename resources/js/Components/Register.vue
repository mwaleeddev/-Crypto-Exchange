<template>
    <div class="min-h-screen bg-gray-900 flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8">
        <div class="max-w-md w-full space-y-8">
            <div>
                <div class="flex justify-center">
                    <div class="w-16 h-16 bg-gradient-to-r from-blue-500 to-purple-600 rounded-xl flex items-center justify-center">
                        <span class="text-2xl font-bold text-white">C</span>
                    </div>
                </div>
                <h2 class="mt-6 text-center text-3xl font-extrabold text-white">
                    Create your account
                </h2>
                <p class="mt-2 text-center text-sm text-gray-400">
                    Or
                    <router-link to="/login" class="font-medium text-blue-400 hover:text-blue-300">
                        sign in to existing account
                    </router-link>
                </p>
            </div>
            <form class="mt-8 space-y-6" @submit.prevent="register">
                <div class="rounded-md shadow-sm space-y-4">
                    <div>
                        <label for="name" class="block text-sm font-medium text-gray-300">Full name</label>
                        <input id="name" v-model="form.name" type="text" required
                               class="mt-1 appearance-none relative block w-full px-3 py-3 border border-gray-700 bg-gray-800 rounded-lg text-white placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                               placeholder="John Doe">
                    </div>
                    <div>
                        <label for="email" class="block text-sm font-medium text-gray-300">Email address</label>
                        <input id="email" v-model="form.email" type="email" required
                               class="mt-1 appearance-none relative block w-full px-3 py-3 border border-gray-700 bg-gray-800 rounded-lg text-white placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                               placeholder="you@example.com">
                    </div>
                    <div>
                        <label for="password" class="block text-sm font-medium text-gray-300">Password</label>
                        <input id="password" v-model="form.password" type="password" required
                               class="mt-1 appearance-none relative block w-full px-3 py-3 border border-gray-700 bg-gray-800 rounded-lg text-white placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                               placeholder="••••••••">
                    </div>
                    <div>
                        <label for="password_confirmation" class="block text-sm font-medium text-gray-300">Confirm password</label>
                        <input id="password_confirmation" v-model="form.password_confirmation" type="password" required
                               class="mt-1 appearance-none relative block w-full px-3 py-3 border border-gray-700 bg-gray-800 rounded-lg text-white placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                               placeholder="••••••••">
                    </div>
                </div>

                <div>
                    <button type="submit" :disabled="loading"
                            class="group relative w-full flex justify-center py-3 px-4 border border-transparent text-sm font-medium rounded-lg text-white bg-gradient-to-r from-blue-600 to-purple-600 hover:from-blue-700 hover:to-purple-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 disabled:opacity-50">
                        <span v-if="loading" class="absolute left-0 inset-y-0 flex items-center pl-3">
                            <div class="animate-spin h-5 w-5 border-2 border-white border-t-transparent rounded-full"></div>
                        </span>
                        {{ loading ? 'Creating account...' : 'Create account' }}
                    </button>
                </div>
            </form>
            
            <div v-if="error" class="p-4 bg-red-900/30 border border-red-700 rounded-lg">
                <p class="text-red-400 text-sm">{{ error }}</p>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref } from 'vue'
import { useRouter } from 'vue-router'
import axios from 'axios'

const router = useRouter()
const loading = ref(false)
const error = ref('')

const form = ref({
    name: '',
    email: '',
    password: '',
    password_confirmation: ''
})

const register = async () => {
    if (form.value.password !== form.value.password_confirmation) {
        error.value = 'Passwords do not match'
        return
    }
    
    loading.value = true
    error.value = ''
    
    try {
        await axios.get('/sanctum/csrf-cookie')
        await axios.post('/register', form.value)
        window.location.href = '/dashboard'
    } catch (err) {
        error.value = err.response?.data?.error || 'Registration failed'
    } finally {
        loading.value = false
    }
}
</script>