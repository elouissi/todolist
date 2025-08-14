<template>
  <div class="min-h-screen relative overflow-hidden">
    <!-- Animated background -->
    <div class="absolute inset-0 bg-gradient-to-br from-indigo-900 via-purple-900 to-pink-900">
      <div class="absolute inset-0 bg-black/20"></div>
      <!-- Floating orbs -->
      <div class="absolute top-1/4 left-1/4 w-64 h-64 bg-blue-500/20 rounded-full blur-3xl animate-pulse"></div>
      <div class="absolute bottom-1/4 right-1/4 w-96 h-96 bg-purple-500/20 rounded-full blur-3xl animate-pulse delay-1000"></div>
      <div class="absolute top-3/4 left-1/2 w-48 h-48 bg-pink-500/20 rounded-full blur-3xl animate-pulse delay-500"></div>
      <div class="absolute top-1/2 left-1/8 w-32 h-32 bg-cyan-500/20 rounded-full blur-3xl animate-pulse delay-700"></div>
      <div class="absolute bottom-1/2 right-1/8 w-40 h-40 bg-emerald-500/20 rounded-full blur-3xl animate-pulse delay-300"></div>
    </div>

    <!-- Navigation -->
    <div class="relative z-10">
      <AppNavigation
        @create-task="showCreateModal = true"
        class="bg-white/5 backdrop-blur-xl border-b border-white/10"
      />
    </div>

    <!-- Main content -->
    <main class="relative z-10 container mx-auto px-4 py-8">
      <!-- Header -->
      <div class="flex items-center justify-between mb-8">
        <div class="bg-white/10 backdrop-blur-xl border border-white/20 rounded-2xl p-6">
          <h1 class="text-3xl font-bold text-white flex items-center gap-3">
            <div class="w-10 h-10 bg-white/20 rounded-xl flex items-center justify-center backdrop-blur-sm">
              <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-5 5v-5zM4.19 4.19A4 4 0 004 6v10a4 4 0 004 4h10a4 4 0 00-4-4V6a4 4 0 00-2.83 1.17z" />
              </svg>
            </div>
            Notifications
          </h1>
          <p class="text-white/70 mt-2">
            Gérez vos notifications et restez informé de vos tâches
          </p>
        </div>
        
        <div class="flex gap-3">
          <button
            @click="markAllAsRead"
            :disabled="!notificationsStore.hasUnreadNotifications"
            class="bg-white/10 hover:bg-white/20 disabled:bg-white/5 border border-white/20 hover:border-white/40 disabled:border-white/10 text-white disabled:text-white/40 px-6 py-3 rounded-xl font-medium transition-all duration-300 hover:scale-105 backdrop-blur-sm disabled:hover:scale-100 disabled:cursor-not-allowed"
          >
            Tout marquer comme lu
          </button>
          <button
            @click="clearAllNotifications"
            :disabled="notificationsStore.notifications.length === 0"
            class="bg-red-500/20 hover:bg-red-500/30 disabled:bg-red-500/10 border border-red-500/30 hover:border-red-500/50 disabled:border-red-500/20 text-red-300 disabled:text-red-400/40 px-6 py-3 rounded-xl font-medium transition-all duration-300 hover:scale-105 backdrop-blur-sm disabled:hover:scale-100 disabled:cursor-not-allowed"
          >
            Tout effacer
          </button>
        </div>
      </div>

      <!-- Loading State -->
      <div v-if="notificationsStore.loading" class="flex justify-center py-12">
        <div class="w-10 h-10 border-2 border-white/30 border-t-white rounded-full animate-spin"></div>
      </div>

      <!-- Empty State -->
      <div v-else-if="notificationsStore.notifications.length === 0" class="text-center py-16">
        <div class="bg-white/10 backdrop-blur-xl border border-white/20 rounded-3xl p-12 max-w-md mx-auto">
          <div class="mx-auto h-20 w-20 text-white/60 mb-6">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" class="w-full h-full">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-5 5v-5zM4.19 4.19A4 4 0 004 6v10a4 4 0 004 4h10a4 4 0 00-4-4V6a4 4 0 00-2.83 1.17z" />
            </svg>
          </div>
          <h3 class="text-xl font-medium text-white mb-3">Aucune notification</h3>
          <p class="text-white/70">
            Vous n'avez pas encore de notifications. Elles apparaîtront ici.
          </p>
        </div>
      </div>

      <!-- Notifications List -->
      <div v-else class="space-y-4">
        <div
          v-for="notification in notificationsStore.notifications"
          :key="notification.id"
          class="bg-white/10 backdrop-blur-xl border border-white/20 rounded-2xl p-6 transition-all duration-300 hover:bg-white/15 hover:scale-102 group"
          :class="{ 'ring-2 ring-blue-400/50 bg-white/15': !notification.read_at }"
        >
          <div class="flex items-start justify-between">
            <div class="flex items-start space-x-4 flex-1">
              <!-- Notification Type Indicator -->
              <div class="flex-shrink-0 mt-1">
                <div
                  class="w-4 h-4 rounded-full backdrop-blur-sm border border-white/20"
                  :class="{
                    'bg-blue-500/50 border-blue-400/50': notification.type === 'task_created',
                    'bg-green-500/50 border-green-400/50': notification.type === 'task_completed',
                    'bg-yellow-500/50 border-yellow-400/50': notification.type === 'task_updated',
                    'bg-red-500/50 border-red-400/50': notification.type === 'task_deleted'
                  }"
                ></div>
              </div>
              
              <!-- Notification Content -->
              <div class="flex-1 min-w-0">
                <p class="text-lg font-medium text-white mb-2">
                  {{ getNotificationTitle(notification) }}
                </p>
                <p class="text-white/70 mb-4">
                  {{ notification.data.message || notification.data.description }}
                </p>
                <div class="flex items-center space-x-4">
                  <span class="text-sm text-white/60">
                    {{ formatDate(notification.created_at) }}
                  </span>
                  <span
                    v-if="!notification.read_at"
                    class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-blue-500/20 text-blue-300 border border-blue-500/30 backdrop-blur-sm"
                  >
                    Nouveau
                  </span>
                </div>
              </div>
            </div>
            
            <!-- Action Buttons -->
            <div class="flex items-center space-x-2 ml-4 opacity-0 group-hover:opacity-100 transition-opacity duration-300">
              <button
                v-if="!notification.read_at"
                @click="markAsRead(notification.id)"
                class="bg-white/10 hover:bg-white/20 border border-white/20 hover:border-white/40 text-white px-4 py-2 rounded-lg text-sm font-medium transition-all duration-300 hover:scale-105 backdrop-blur-sm"
              >
                Marquer comme lu
              </button>
              <button
                @click="deleteNotification(notification.id)"
                class="bg-red-500/20 hover:bg-red-500/30 border border-red-500/30 hover:border-red-500/50 text-red-300 px-4 py-2 rounded-lg text-sm font-medium transition-all duration-300 hover:scale-105 backdrop-blur-sm"
              >
                Supprimer
              </button>
            </div>
          </div>
        </div>
      </div>

      <!-- Pagination -->
      <div v-if="notificationsStore.totalPages > 1" class="flex justify-center mt-12">
        <div class="bg-white/10 backdrop-blur-xl border border-white/20 rounded-2xl p-4">
          <div class="flex items-center space-x-4">
            <button
              :disabled="notificationsStore.currentPage === 1"
              @click="changePage(notificationsStore.currentPage - 1)"
              class="bg-white/10 hover:bg-white/20 disabled:bg-white/5 border border-white/20 hover:border-white/40 disabled:border-white/10 text-white disabled:text-white/40 px-4 py-2 rounded-lg text-sm font-medium transition-all duration-300 hover:scale-105 backdrop-blur-sm disabled:hover:scale-100 disabled:cursor-not-allowed"
            >
              Précédent
            </button>
            <span class="text-white/70 font-medium">
              Page {{ notificationsStore.currentPage }} sur {{ notificationsStore.totalPages }}
            </span>
            <button
              :disabled="notificationsStore.currentPage === notificationsStore.totalPages"
              @click="changePage(notificationsStore.currentPage + 1)"
              class="bg-white/10 hover:bg-white/20 disabled:bg-white/5 border border-white/20 hover:border-white/40 disabled:border-white/10 text-white disabled:text-white/40 px-4 py-2 rounded-lg text-sm font-medium transition-all duration-300 hover:scale-105 backdrop-blur-sm disabled:hover:scale-100 disabled:cursor-not-allowed"
            >
              Suivant
            </button>
          </div>
        </div>
      </div>
    </main>

    <!-- Create Task Modal -->
    <BaseModal
      v-model="showCreateModal"
      title="Nouvelle tâche"
      size="lg"
      class="backdrop-blur-xl"
    >
      <TaskForm
        :loading="formLoading"
        @submit="handleFormSubmit"
        @cancel="showCreateModal = false"
      />
    </BaseModal>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { useToast } from 'vue-toastification'
import { useTasksStore } from '@/stores/tasks'
import { useNotificationsStore } from '@/stores/notifications'
import {
  BaseModal,
  AppNavigation
} from '@/components'
import TaskForm from '@/components/organisms/TaskForm.vue'

const toast = useToast()
const tasksStore = useTasksStore()
const notificationsStore = useNotificationsStore()

const showCreateModal = ref(false)
const formLoading = ref(false)

const markAsRead = async (id) => {
  await notificationsStore.markAsRead(id)
  await notificationsStore.forceRefreshCount()
}

const markAllAsRead = async () => {
  await notificationsStore.markAllAsRead()
  await notificationsStore.forceRefreshCount()
}

const deleteNotification = async (id) => {
  await notificationsStore.deleteNotification(id)
  await notificationsStore.forceRefreshCount()
}

const clearAllNotifications = async () => {
  await notificationsStore.clearAllNotifications()
  await notificationsStore.forceRefreshCount()
}

const changePage = (page) => {
  notificationsStore.fetchNotifications(page)
}

const getNotificationTitle = (notification) => {
  const titles = {
    task_created: 'Nouvelle tâche créée',
    task_updated: 'Tâche mise à jour',
    task_completed: 'Tâche terminée',
    task_deleted: 'Tâche supprimée'
  }
  return titles[notification.type] || 'Notification'
}

const formatDate = (dateString) => {
  const date = new Date(dateString)
  const now = new Date()
  const diffInHours = (now - date) / (1000 * 60 * 60)
  
  if (diffInHours < 1) {
    return 'Il y a quelques minutes'
  } else if (diffInHours < 24) {
    return `Il y a ${Math.floor(diffInHours)} heure${Math.floor(diffInHours) > 1 ? 's' : ''}`
  } else {
    return date.toLocaleDateString('fr-FR', {
      day: 'numeric',
      month: 'long',
      year: 'numeric',
      hour: '2-digit',
      minute: '2-digit'
    })
  }
}

const handleFormSubmit = async (taskData) => {
  formLoading.value = true
  try {
    const result = await tasksStore.createTask(taskData)
    if (result.success) {
      showCreateModal.value = false
      await notificationsStore.fetchNotifications()
      await notificationsStore.forceRefreshCount()
    }
  } finally {
    formLoading.value = false
  }
}

onMounted(() => {
  notificationsStore.fetchNotifications()
})
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

.delay-300 {
  animation-delay: 0.3s;
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

/* Custom hover scale */
.hover\:scale-102:hover {
  transform: scale(1.02);
}

.hover\:scale-105:hover {
  transform: scale(1.05);
}
</style>