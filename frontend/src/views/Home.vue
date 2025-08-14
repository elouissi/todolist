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
      <!-- Statistics -->
      <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-5 mb-8">
        <!-- Total Tasks -->
        <div class="bg-white/10 backdrop-blur-xl border border-white/20 rounded-2xl p-6 hover:bg-white/15 transition-all duration-300 hover:scale-105 hover:shadow-xl">
          <div class="flex items-center">
            <div class="flex-shrink-0">
              <div class="w-12 h-12 bg-white/20 rounded-xl flex items-center justify-center backdrop-blur-sm">
                <ClipboardDocumentListIcon class="h-6 w-6 text-white" />
              </div>
            </div>
            <div class="ml-4">
              <p class="text-sm font-medium text-white/70">Total des tâches</p>
              <p class="text-2xl font-bold text-white">
                {{ tasksStore.loading ? '...' : tasksStore.statistics.total }}
              </p>
            </div>
          </div>
        </div>

        <!-- Pending Tasks -->
        <div class="bg-white/10 backdrop-blur-xl border border-white/20 rounded-2xl p-6 hover:bg-white/15 transition-all duration-300 hover:scale-105 hover:shadow-xl">
          <div class="flex items-center">
            <div class="flex-shrink-0">
              <div class="w-12 h-12 bg-yellow-500/30 rounded-xl flex items-center justify-center backdrop-blur-sm">
                <ClockIcon class="h-6 w-6 text-yellow-300" />
              </div>
            </div>
            <div class="ml-4">
              <p class="text-sm font-medium text-white/70">En attente</p>
              <p class="text-2xl font-bold text-white">
                {{ tasksStore.loading ? '...' : tasksStore.statistics.pending }}
              </p>
            </div>
          </div>
        </div>

        <!-- In Progress Tasks -->
        <div class="bg-white/10 backdrop-blur-xl border border-white/20 rounded-2xl p-6 hover:bg-white/15 transition-all duration-300 hover:scale-105 hover:shadow-xl">
          <div class="flex items-center">
            <div class="flex-shrink-0">
              <div class="w-12 h-12 bg-blue-500/30 rounded-xl flex items-center justify-center backdrop-blur-sm">
                <BoltIcon class="h-6 w-6 text-blue-300" />
              </div>
            </div>
            <div class="ml-4">
              <p class="text-sm font-medium text-white/70">En cours</p>
              <p class="text-2xl font-bold text-white">
                {{ tasksStore.loading ? '...' : tasksStore.statistics.in_progress }}
              </p>
            </div>
          </div>
        </div>

        <!-- Completed Tasks -->
        <div class="bg-white/10 backdrop-blur-xl border border-white/20 rounded-2xl p-6 hover:bg-white/15 transition-all duration-300 hover:scale-105 hover:shadow-xl">
          <div class="flex items-center">
            <div class="flex-shrink-0">
              <div class="w-12 h-12 bg-green-500/30 rounded-xl flex items-center justify-center backdrop-blur-sm">
                <CheckCircleIcon class="h-6 w-6 text-green-300" />
              </div>
            </div>
            <div class="ml-4">
              <p class="text-sm font-medium text-white/70">Terminées</p>
              <p class="text-2xl font-bold text-white">
                {{ tasksStore.loading ? '...' : tasksStore.statistics.completed }}
              </p>
            </div>
          </div>
        </div>

        <!-- Canceled Tasks -->
        <div class="bg-white/10 backdrop-blur-xl border border-white/20 rounded-2xl p-6 hover:bg-white/15 transition-all duration-300 hover:scale-105 hover:shadow-xl">
          <div class="flex items-center">
            <div class="flex-shrink-0">
              <div class="w-12 h-12 bg-red-500/30 rounded-xl flex items-center justify-center backdrop-blur-sm">
                <XCircleIcon class="h-6 w-6 text-red-300" />
              </div>
            </div>
            <div class="ml-4">
              <p class="text-sm font-medium text-white/70">Annulées</p>
              <p class="text-2xl font-bold text-white">
                {{ tasksStore.loading ? '...' : (tasksStore.statistics.canceled || 0) }}
              </p>
            </div>
          </div>
        </div>
      </div>

      <!-- Recent tasks -->
      <div class="bg-white/10 backdrop-blur-xl border border-white/20 rounded-3xl p-8 shadow-2xl">
        <!-- Header -->
        <div class="flex items-center justify-between mb-6">
          <h3 class="text-xl font-semibold text-white">Tâches récentes</h3>
          <div class="w-8 h-8 bg-white/20 rounded-lg flex items-center justify-center">
            <ClipboardDocumentListIcon class="h-5 w-5 text-white" />
          </div>
        </div>
        
        <!-- Loading State -->
        <div v-if="tasksStore.loading" class="flex justify-center py-12">
          <div class="w-8 h-8 border-2 border-white/30 border-t-white rounded-full animate-spin"></div>
        </div>
        
        <!-- Empty State -->
        <div v-else-if="tasksStore.tasks.length === 0" class="text-center py-12">
          <div class="mx-auto w-16 h-16 bg-white/20 rounded-full flex items-center justify-center mb-4 backdrop-blur-sm">
            <ClipboardDocumentListIcon class="h-8 w-8 text-white" />
          </div>
          <h3 class="text-lg font-medium text-white mb-2">Aucune tâche</h3>
          <p class="text-white/70 mb-6">Commencez par créer votre première tâche.</p>
          <button
            @click="showCreateModal = true"
            class="inline-flex items-center gap-2 bg-white/20 hover:bg-white/30 border border-white/30 hover:border-white/50 text-white px-6 py-3 rounded-xl font-medium transition-all duration-300 hover:scale-105 backdrop-blur-sm"
          >
            <PlusIcon class="h-4 w-4" />
            Créer une tâche
          </button>
        </div>
        
        <!-- Tasks List -->
        <div v-else class="space-y-4">
          <div
            v-for="task in tasksStore.tasks.slice(0, 5)"
            :key="task.id"
            class="bg-white/5 border border-white/20 rounded-2xl p-6 hover:bg-white/10 transition-all duration-300 cursor-pointer group hover:scale-102 backdrop-blur-sm"
            @click="$router.push(`/tasks/${task.id}`)"
          >
            <div class="flex items-center justify-between">
              <div class="flex-1">
                <div class="flex items-center gap-3 mb-3">
                  <h4 class="text-lg font-medium text-white group-hover:text-white/90 transition-colors">
                    {{ task.title }}
                  </h4>
                  <div class="flex items-center gap-2">
                    <!-- Status Badge -->
                    <span :class="getStatusClasses(task.status)" class="inline-flex items-center gap-1 px-3 py-1 rounded-full text-xs font-medium backdrop-blur-sm">
                      <ClockIcon v-if="task.status === 'pending'" class="h-3 w-3" />
                      <BoltIcon v-else-if="task.status === 'in_progress'" class="h-3 w-3" />
                      <CheckCircleIcon v-else-if="task.status === 'completed'" class="h-3 w-3" />
                      {{ getStatusLabel(task.status) }}
                    </span>
                    <!-- Priority Badge -->
                    <span :class="getPriorityClasses(task.priority)" class="inline-flex items-center gap-1 px-3 py-1 rounded-full text-xs font-medium backdrop-blur-sm">
                      <ExclamationTriangleIcon v-if="task.priority === 'high'" class="h-3 w-3" />
                      <ExclamationCircleIcon v-else-if="task.priority === 'medium'" class="h-3 w-3" />
                      <MinusIcon v-else class="h-3 w-3" />
                      {{ getPriorityLabel(task.priority) }}
                    </span>
                  </div>
                </div>
                <p v-if="task.description" class="text-white/70 mb-3 line-clamp-2">
                  {{ task.description }}
                </p>
                <div v-if="task.due_date" class="flex items-center gap-2 text-sm text-white/60">
                  <CalendarIcon class="h-4 w-4" />
                  <span>Échéance: {{ formatDate(task.due_date) }}</span>
                </div>
              </div>
              <div class="flex items-center ml-4">
                <button
                  class="w-10 h-10 bg-white/10 hover:bg-white/20 rounded-xl flex items-center justify-center opacity-0 group-hover:opacity-100 transition-all duration-300 backdrop-blur-sm"
                  @click.stop="$router.push(`/tasks/${task.id}`)"
                >
                  <EyeIcon class="h-5 w-5 text-white" />
                </button>
              </div>
            </div>
          </div>
          
          <!-- View All Button -->
          <div class="text-center pt-6">
            <button
              @click="$router.push('/tasks')"
              class="inline-flex items-center gap-2 bg-white/10 hover:bg-white/20 border border-white/30 hover:border-white/50 text-white px-6 py-3 rounded-xl font-medium transition-all duration-300 hover:scale-105 backdrop-blur-sm"
            >
              <ArrowRightIcon class="h-4 w-4" />
              Voir toutes les tâches
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
import { useRouter } from 'vue-router'
import { useAuthStore } from '@/stores/auth'
import { useTasksStore } from '@/stores/tasks'
import { useNotificationsStore } from '@/stores/notifications'
import {
  BaseModal,
  AppNavigation
} from '@/components'
import TaskForm from '@/components/organisms/TaskForm.vue'
import { 
  ClipboardDocumentListIcon, 
  ClockIcon, 
  BoltIcon, 
  CheckCircleIcon, 
  ExclamationTriangleIcon, 
  ExclamationCircleIcon, 
  MinusIcon, 
  CalendarIcon, 
  ArrowRightIcon, 
  EyeIcon, 
  PlusIcon, 
  XCircleIcon 
} from '@heroicons/vue/24/outline'

const router = useRouter()
const authStore = useAuthStore()
const tasksStore = useTasksStore()
const notificationsStore = useNotificationsStore()

const showCreateModal = ref(false)
const formLoading = ref(false)

onMounted(async () => {
  await tasksStore.fetchTasks()
  await tasksStore.fetchStatistics()
})

const handleFormSubmit = async (taskData) => {
  formLoading.value = true
  try {
    const result = await tasksStore.createTask(taskData)
    if (result.success) {
      showCreateModal.value = false
      await tasksStore.fetchTasks()
      await tasksStore.fetchStatistics()
      await notificationsStore.forceRefreshCount()
    }
  } finally {
    formLoading.value = false
  }
}

const getStatusLabel = (status) => {
  const labels = {
    pending: 'En attente',
    in_progress: 'En cours',
    completed: 'Terminée'
  }
  return labels[status] || status
}

const getPriorityLabel = (priority) => {
  const labels = {
    low: 'Faible',
    medium: 'Moyenne',
    high: 'Élevée'
  }
  return labels[priority] || priority
}

const getStatusClasses = (status) => {
  const classes = {
    pending: 'bg-yellow-500/20 text-yellow-300 border border-yellow-500/30',
    in_progress: 'bg-blue-500/20 text-blue-300 border border-blue-500/30',
    completed: 'bg-green-500/20 text-green-300 border border-green-500/30'
  }
  return classes[status] || 'bg-white/20 text-white border border-white/30'
}

const getPriorityClasses = (priority) => {
  const classes = {
    low: 'bg-gray-500/20 text-gray-300 border border-gray-500/30',
    medium: 'bg-orange-500/20 text-orange-300 border border-orange-500/30',
    high: 'bg-red-500/20 text-red-300 border border-red-500/30'
  }
  return classes[priority] || 'bg-white/20 text-white border border-white/30'
}

const formatDate = (dateString) => {
  const date = new Date(dateString)
  return date.toLocaleDateString()
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

/* Line clamp utility */
.line-clamp-2 {
  display: -webkit-box;
  -webkit-line-clamp: 2;
  -webkit-box-orient: vertical;
  overflow: hidden;
}
</style>