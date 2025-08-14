<template>
  <div id="app">
    <router-view />
  </div>
</template>

<script setup>
import { onMounted, onErrorCaptured } from 'vue'
import { useToast } from 'vue-toastification'

const toast = useToast()

// Global error handler
onErrorCaptured((error, instance, info) => {
  console.error('Global error caught:', error, info)
  
  // Handle authentication errors
  if (error.response?.status === 401) {
    toast.error('Session expirée. Veuillez vous reconnecter.')
    return false // Prevent error from propagating
  }
  
  // Handle network errors
  if (error.code === 'NETWORK_ERROR' || error.code === 'ERR_NETWORK') {
    toast.error('Erreur de connexion. Vérifiez votre connexion internet.')
    return false
  }
  
  // Handle other errors
  if (error.response?.status >= 500) {
    toast.error('Erreur serveur. Veuillez réessayer plus tard.')
    return false
  }
  
  // For other errors, show a generic message
  toast.error('Une erreur inattendue s\'est produite.')
  return false
})

onMounted(() => {
  // Add global unhandled promise rejection handler
  window.addEventListener('unhandledrejection', (event) => {
    console.error('Unhandled promise rejection:', event.reason)
    
    // Handle authentication errors
    if (event.reason?.response?.status === 401) {
      toast.error('Session expirée. Veuillez vous reconnecter.')
      event.preventDefault()
      return
    }
    
    // Handle network errors
    if (event.reason?.code === 'NETWORK_ERROR' || event.reason?.code === 'ERR_NETWORK') {
      toast.error('Erreur de connexion. Vérifiez votre connexion internet.')
      event.preventDefault()
      return
    }
    
    // Handle other errors
    if (event.reason?.response?.status >= 500) {
      toast.error('Erreur serveur. Veuillez réessayer plus tard.')
      event.preventDefault()
      return
    }
    
    // For other errors, show a generic message
    toast.error('Une erreur inattendue s\'est produite.')
    event.preventDefault()
  })
})
</script>

<style>
#app {
  min-height: 100vh;
}
</style> 