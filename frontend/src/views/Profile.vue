<template>
  <div class="min-h-screen bg-gradient-to-br from-blue-500 via-purple-600 to-pink-500 flex items-center justify-center px-4">
    <div class="w-full max-w-md">
      <!-- Login Card -->
      <div class="bg-white/10 backdrop-blur-lg rounded-2xl shadow-2xl border border-white/20 p-8">
        <!-- Header -->
        <div class="text-center mb-8">
          <div class="w-16 h-16 bg-white/20 rounded-full flex items-center justify-center mx-auto mb-4">
            <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
            </svg>
          </div>
          <h1 class="text-3xl font-bold text-white mb-2">
            Bienvenue
          </h1>
          <p class="text-white/70 text-sm">
            Connectez-vous à votre compte
          </p>
        </div>
        
        <!-- Form -->
        <div class="space-y-6">
          <LoginForm
            :loading="authStore.loading"
            @submit="handleLogin"
          />
        </div>
        
        <!-- Footer -->
        <div class="mt-8 text-center">
          <p class="text-white/60 text-sm">
            Nouveau ici ?
            <button 
              @click="$router.push('/register')"
              class="text-white font-semibold hover:text-white/80 transition-colors ml-1 underline decoration-white/40 hover:decoration-white/60"
            >
              Créer un compte
            </button>
          </p>
        </div>
      </div>
      
      <!-- Decorative elements -->
      <div class="absolute top-1/4 left-1/4 w-32 h-32 bg-white/5 rounded-full blur-xl"></div>
      <div class="absolute bottom-1/3 right-1/4 w-40 h-40 bg-white/5 rounded-full blur-xl"></div>
    </div>
  </div>
</template>

<script setup>
import { useAuthStore } from '@/stores/auth'
import { useRouter } from 'vue-router'
import LoginForm from '@/components/molecules/LoginForm.vue'

const authStore = useAuthStore()
const router = useRouter()

const handleLogin = async (credentials) => {
  const result = await authStore.login(credentials)
  if (result.success) {
    await new Promise(resolve => setTimeout(resolve, 100))
    
    if (authStore.isAuthenticated) {
      console.log('Login successful, redirecting to home')
      router.push('/')
    } else {
      console.error('Login succeeded but user is not authenticated')
      window.location.href = '/'
    }
  }
}
</script>