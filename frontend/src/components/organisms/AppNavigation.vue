<template>
  <nav class="bg-background border-b">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
      <div class="flex justify-between h-16">
        <!-- Logo et navigation principale -->
        <div class="flex">
          <div class="flex-shrink-0 flex items-center">
            <router-link to="/" class="text-xl font-bold text-foreground">
              Tasks Manager
            </router-link>
          </div>
          
          <div class="hidden sm:ml-6 sm:flex sm:space-x-8">
            <router-link
              v-for="item in navigationItems"
              :key="item.path"
              :to="item.path"
              :class="[
                'inline-flex items-center px-1 pt-1 border-b-2 text-sm font-medium transition-colors',
                $route.path === item.path
                  ? 'border-primary text-foreground'
                  : 'border-transparent text-muted-foreground hover:text-foreground hover:border-muted-foreground'
              ]"
            >
              {{ item.name }}
            </router-link>
          </div>
        </div>
        
        <!-- Actions et profil -->
        <div class="flex items-center space-x-4">
          <!-- Bouton nouvelle tâche -->
          <BaseButton
            @click="$emit('create-task')"
            size="sm"
            class="hidden sm:inline-flex"
          >
            Nouvelle tâche
          </BaseButton>
          
          <!-- Notifications -->
          <router-link
            to="/notifications"
            class="relative p-2 text-muted-foreground hover:text-foreground transition-colors"
            @click="forceRefreshNotifications"
          >
            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-5 5v-5z" />
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z" />
            </svg>
            <span
              v-if="notificationsStore.unreadCount > 0"
              class="absolute -top-1 -right-1 h-4 w-4 rounded-full bg-destructive text-destructive-foreground text-xs flex items-center justify-center"
            >
              {{ notificationsStore.unreadCount > 99 ? '99+' : notificationsStore.unreadCount }}
            </span>
          </router-link>
          
          <!-- Menu profil -->
          <div class="relative">
            <button
              @click="showProfileMenu = !showProfileMenu"
              class="flex items-center space-x-2 text-sm rounded-full focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary"
            >
              <img
                :src="user?.image_url || '/default-avatar.png'"
                :alt="user?.full_name"
                class="h-8 w-8 rounded-full object-cover"
              />
              <span class="hidden md:block text-muted-foreground">
                {{ user?.full_name }}
              </span>
              <svg
                class="h-4 w-4 text-muted-foreground"
                :class="{ 'rotate-180': showProfileMenu }"
                fill="none"
                stroke="currentColor"
                viewBox="0 0 24 24"
              >
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
              </svg>
            </button>
            
            <!-- Menu déroulant -->
            <div
              v-if="showProfileMenu"
              class="absolute right-0 mt-2 w-48 rounded-md shadow-lg bg-background ring-1 ring-black ring-opacity-5 z-50"
            >
              <div class="py-1">
                <router-link
                  to="/profile"
                  class="block px-4 py-2 text-sm text-muted-foreground hover:text-foreground hover:bg-accent"
                  @click="showProfileMenu = false"
                >
                  Mon profil
                </router-link>
                <button
                  @click="handleLogout"
                  class="block w-full text-left px-4 py-2 text-sm text-muted-foreground hover:text-foreground hover:bg-accent"
                >
                  Se déconnecter
                </button>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
    
    <!-- Menu mobile -->
    <div v-if="showMobileMenu" class="sm:hidden">
      <div class="pt-2 pb-3 space-y-1">
        <router-link
          v-for="item in navigationItems"
          :key="item.path"
          :to="item.path"
          :class="[
            'block pl-3 pr-4 py-2 border-l-4 text-base font-medium transition-colors',
            $route.path === item.path
              ? 'bg-primary/10 border-primary text-primary'
              : 'border-transparent text-muted-foreground hover:text-foreground hover:bg-accent hover:border-muted-foreground'
          ]"
          @click="showMobileMenu = false"
        >
          {{ item.name }}
        </router-link>
      </div>
    </div>
  </nav>
</template>

<script setup>
import { ref, computed, onMounted, onUnmounted, watch } from 'vue'
import { useRouter, useRoute } from 'vue-router'
import { useAuthStore } from '@/stores/auth'
import { useNotificationsStore } from '@/stores/notifications'
import BaseButton from '@/components/atoms/BaseButton.vue'

const router = useRouter()
const route = useRoute()
const authStore = useAuthStore()
const notificationsStore = useNotificationsStore()

const showProfileMenu = ref(false)
const showMobileMenu = ref(false)

const user = computed(() => authStore.user)

const navigationItems = [
  { name: 'Tableau de bord', path: '/' },
  { name: 'Tâches', path: '/tasks' },
  { name: 'Notifications', path: '/notifications' }
]

// Props pour la démonstration
const props = defineProps({
  notificationCount: {
    type: Number,
    default: 0
  }
})

const emit = defineEmits(['create-task'])

const handleLogout = async () => {
  await authStore.logout()
  router.push('/login')
  showProfileMenu.value = false
}

// Fermer les menus en cliquant à l'extérieur
const handleClickOutside = (event) => {
  if (!event.target.closest('.relative')) {
    showProfileMenu.value = false
  }
}

// Force refresh notifications count
const forceRefreshNotifications = () => {
  notificationsStore.fetchUnreadCount()
}

// Watch for route changes to refresh notification count
watch(() => route.path, async (newPath) => {
  if (authStore.isAuthenticated) {
    // Force refresh count when navigating to any page
    await notificationsStore.forceRefreshCount()
  }
})

// Fetch notifications count on mount
onMounted(async () => {
  if (authStore.isAuthenticated) {
    await notificationsStore.fetchUnreadCount()
    notificationsStore.startAutoRefresh()
  }
})

onUnmounted(() => {
  notificationsStore.stopAutoRefresh()
  document.removeEventListener('click', handleClickOutside)
})
</script> 