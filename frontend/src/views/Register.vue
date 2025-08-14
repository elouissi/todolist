<template>
  <div class="min-h-screen flex items-center justify-center relative overflow-hidden py-12 px-4 sm:px-6 lg:px-8">
    <!-- Animated background -->
    <div class="absolute inset-0 bg-gradient-to-br from-indigo-900 via-purple-900 to-pink-900">
      <div class="absolute inset-0 bg-black/20"></div>
      <!-- Floating orbs -->
      <div class="absolute top-1/4 left-1/4 w-64 h-64 bg-blue-500/20 rounded-full blur-3xl animate-pulse"></div>
      <div class="absolute bottom-1/4 right-1/4 w-96 h-96 bg-purple-500/20 rounded-full blur-3xl animate-pulse delay-1000"></div>
      <div class="absolute top-3/4 left-1/2 w-48 h-48 bg-pink-500/20 rounded-full blur-3xl animate-pulse delay-500"></div>
      <div class="absolute top-1/2 left-1/8 w-32 h-32 bg-cyan-500/20 rounded-full blur-3xl animate-pulse delay-700"></div>
    </div>

    <!-- Main content -->
    <div class="relative z-10 w-full max-w-6xl space-y-8">
      <!-- Logo/Brand area -->
      <div class="text-center">
        <div class="inline-flex items-center justify-center w-16 h-16 bg-white/10 backdrop-blur-lg rounded-2xl mb-4 border border-white/20">
          <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/>
          </svg>
        </div>
        <h2 class="text-4xl font-bold text-white mb-2">
          Rejoignez-nous
        </h2>
        <p class="text-white/70 text-lg">
          Créez votre compte en quelques instants
        </p>
      </div>
      
      <!-- Register card -->
      <div class="bg-white/10 backdrop-blur-xl border border-white/20 rounded-3xl p-8 shadow-2xl mx-auto max-w-2xl">
        <RegisterForm
          :loading="authStore.loading"
          @submit="handleRegister"
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
        
        <!-- Login link -->
        <div class="text-center">
          <p class="text-white/70 mb-4">Vous avez déjà un compte ?</p>
          <button
            class="w-full bg-white/5 hover:bg-white/10 border border-white/20 hover:border-white/40 text-white transition-all duration-300 hover:scale-105 hover:shadow-lg backdrop-blur-sm rounded-xl py-3 px-6 font-medium"
            @click="$router.push('/login')"
          >
            Se connecter à votre compte
          </button>
        </div>
      </div>
      
      <!-- Footer -->
      <div class="text-center text-white/50 text-sm">
        <p>© 2025 Votre Entreprise. Tous droits réservés.</p>
      </div>
    </div>
  </div>
</template>

<script setup>
import { useRouter } from 'vue-router'
import { useAuthStore } from '@/stores/auth'
import RegisterForm from '@/components/molecules/RegisterForm.vue'

const router = useRouter()
const authStore = useAuthStore()

const handleRegister = async (formData) => {
  const result = await authStore.register(formData)
  if (result.success) {
    if (result.needsLogin) {
      // Redirect to login page after successful registration
      router.push('/login')
    } else {
      // If somehow we got a token, go to home
      router.push('/')
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

.delay-700 {
  animation-delay: 0.7s;
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