<template>
  <div class="min-h-screen flex items-center justify-center relative overflow-hidden">
    <!-- Animated background -->
    <div class="absolute inset-0 bg-gradient-to-br from-indigo-900 via-purple-900 to-pink-900">
      <div class="absolute inset-0 bg-black/20"></div>
      <!-- Floating orbs -->
      <div class="absolute top-1/4 left-1/4 w-64 h-64 bg-blue-500/20 rounded-full blur-3xl animate-pulse"></div>
      <div class="absolute bottom-1/4 right-1/4 w-96 h-96 bg-purple-500/20 rounded-full blur-3xl animate-pulse delay-1000"></div>
      <div class="absolute top-3/4 left-1/2 w-48 h-48 bg-pink-500/20 rounded-full blur-3xl animate-pulse delay-500"></div>
    </div>

    <!-- Main content -->
    <div class="relative z-10 max-w-md w-full mx-4">
      <!-- Logo/Brand area -->
      <div class="text-center mb-8">
        <div class="inline-flex items-center justify-center w-16 h-16 bg-white/10 backdrop-blur-lg rounded-2xl mb-4 border border-white/20">
          <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
          </svg>
        </div>
        <h2 class="text-4xl font-bold text-white mb-2">
          Bienvenue
        </h2>
        <p class="text-white/70 text-lg">
          Connectez-vous à votre espace
        </p>
      </div>
      
      <!-- Login card -->
      <div class="bg-white/10 backdrop-blur-xl border border-white/20 rounded-3xl p-8 shadow-2xl">
        <LoginForm
          :loading="authStore.loading"
          @submit="handleLogin"
          class="space-y-6"
        />
        
        <!-- Divider -->
        <div class="relative my-8">
          <div class="absolute inset-0 flex items-center">
            <div class="w-full border-t border-white/20"></div>
          </div>
          <div class="relative flex justify-center text-sm">
            <span class="px-4 bg-transparent text-white/70">Ou</span>
          </div>
        </div>
        
        <!-- Register link -->
        <div class="text-center">
          <p class="text-white/70 mb-4">Pas encore de compte ?</p>
          <BaseButton
            variant="outline"
            class="w-full bg-white/5 hover:bg-white/10 border-white/20 hover:border-white/40 text-white transition-all duration-300 hover:scale-105 hover:shadow-lg backdrop-blur-sm"
            @click="$router.push('/register')"
          >
            Créer un nouveau compte
          </BaseButton>
        </div>
      </div>
      
      <!-- Footer -->
      <div class="text-center mt-8 text-white/50 text-sm">
        <p>© 2025 Votre Entreprise. Tous droits réservés.</p>
      </div>
    </div>
  </div>
</template>

<script setup>
import { useAuthStore } from '@/stores/auth'
import { useRouter } from 'vue-router'
import BaseButton from '@/components/atoms/BaseButton.vue'
import LoginForm from '@/components/molecules/LoginForm.vue'

const authStore = useAuthStore()
const router = useRouter()

const handleLogin = async (credentials) => {
  const result = await authStore.login(credentials)
  if (result.success) {
    // Wait a bit to ensure the auth state is properly updated
    await new Promise(resolve => setTimeout(resolve, 100))
    
    // Double check that we're authenticated before redirecting
    if (authStore.isAuthenticated) {
      console.log('Login successful, redirecting to home')
      router.push('/')
    } else {
      console.error('Login succeeded but user is not authenticated')
      // Force a page reload to reinitialize auth state
      window.location.href = '/'
    }
  }
}
</script>

<style scoped>
/* Custom animations */
@keyframes pulse {
  0%, 100% {
    opacity: 0.4;
    transform: scale(1);
  }
  50% {
    opacity: 0.6;
    transform: scale(1.1);
  }
}

.animate-pulse {
  animation: pulse 4s ease-in-out infinite;
}

.delay-500 {
  animation-delay: 0.5s;
}

.delay-1000 {
  animation-delay: 1s;
}

/* Glassmorphism effect enhancement */
.backdrop-blur-xl {
  backdrop-filter: blur(20px);
  -webkit-backdrop-filter: blur(20px);
}

/* Smooth transitions */
* {
  transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
}
</style>