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
      <div class="absolute top-1/8 right-1/3 w-56 h-56 bg-violet-500/20 rounded-full blur-3xl animate-pulse delay-900"></div>
    </div>

    <!-- Navigation -->
    <div class="relative z-10">
      <AppNavigation
        :notification-count="0"
        @create-task="showCreateModal = true"
        class="bg-white/5 backdrop-blur-xl border-b border-white/10"
      />
    </div>

    <!-- Main content -->
    <div class="relative z-10 container mx-auto px-4 py-8">
      <!-- Header with View Toggle -->
      <div class="flex items-center justify-between mb-8">
        <div class="bg-white/10 backdrop-blur-xl border border-white/20 rounded-2xl p-6">
          <h1 class="text-3xl font-bold text-white flex items-center gap-3">
            <div class="w-10 h-10 bg-white/20 rounded-xl flex items-center justify-center backdrop-blur-sm">
              <ListBulletIcon class="w-6 h-6 text-white" />
            </div>
            Mes Tâches
          </h1>
          <p class="text-white/70 mt-2">
            Organisez et suivez vos tâches efficacement
          </p>
        </div>

        <!-- View Mode Toggle -->
        <div class="bg-white/10 backdrop-blur-xl border border-white/20 rounded-2xl p-2 flex items-center gap-2">
          <button
            :class="[
              'flex items-center gap-2 px-4 py-2 rounded-xl font-medium transition-all duration-300 hover:scale-105',
              viewMode === 'list' 
                ? 'bg-white/20 text-white border border-white/30' 
                : 'text-white/70 hover:text-white hover:bg-white/10'
            ]"
            @click="viewMode = 'list'"
          >
            <ListBulletIcon class="w-4 h-4" />
            Liste
          </button>
          <button
            :class="[
              'flex items-center gap-2 px-4 py-2 rounded-xl font-medium transition-all duration-300 hover:scale-105',
              viewMode === 'kanban' 
                ? 'bg-white/20 text-white border border-white/30' 
                : 'text-white/70 hover:text-white hover:bg-white/10'
            ]"
            @click="viewMode = 'kanban'"
          >
            <Squares2X2Icon class="w-4 h-4" />
            Kanban
          </button>
        </div>
      </div>

      <!-- Vue Liste -->
      <div v-if="viewMode === 'list'" class="bg-white/10 backdrop-blur-xl border border-white/20 rounded-3xl p-8 shadow-2xl">
        <TaskList
          :tasks="tasks"
          :loading="loading"
          :current-page="currentPage"
          :total-pages="totalPages"
          @create="showCreateModal = true"
          @view="viewTask"
          @edit="editTask"
          @delete="deleteTask"
          @page-change="handlePageChange"
          class="glassmorphism-content"
        />
      </div>

      <!-- Vue Kanban -->
      <div v-else class="bg-white/10 backdrop-blur-xl border border-white/20 rounded-3xl p-8 shadow-2xl">
        <KanbanBoard
          :tasks="tasks"
          :loading="loading"
          @create="showCreateModal = true"
          @view="viewTask"
          @edit="editTask"
          @delete="deleteTask"
          @status-change="handleStatusChange"
          class="glassmorphism-content"
        />
      </div>
      
      <!-- Create/Edit Modal -->
      <BaseModal
        v-model="isModalOpen"
        :title="showEditModal ? 'Modifier la tâche' : 'Nouvelle tâche'"
        size="lg"
        class="backdrop-blur-xl"
      >
        <div class="bg-white/10 backdrop-blur-xl border border-white/20 rounded-2xl p-6">
          <TaskForm
            :task="editingTask"
            :loading="formLoading"
            @submit="handleFormSubmit"
            @cancel="closeModal"
            class="glassmorphism-form"
          />
        </div>
      </BaseModal>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import { useRouter } from 'vue-router'
import { useToast } from 'vue-toastification'
import { ListBulletIcon, Squares2X2Icon } from '@heroicons/vue/24/outline'
import api from '@/services/api'
import BaseModal from '@/components/atoms/BaseModal.vue'
import TaskList from '@/components/organisms/TaskList.vue'
import TaskForm from '@/components/organisms/TaskForm.vue'
import AppNavigation from '@/components/organisms/AppNavigation.vue'
import KanbanBoard from '@/components/organisms/KanbanBoard.vue'

const router = useRouter()
const toast = useToast()

const tasks = ref([])
const loading = ref(false)
const formLoading = ref(false)
const currentPage = ref(1)
const totalPages = ref(1)
const showCreateModal = ref(false)
const showEditModal = ref(false)
const editingTask = ref(null)
const viewMode = ref('kanban') // Par défaut en mode kanban

const isModalOpen = computed({
  get: () => showCreateModal.value || showEditModal.value,
  set: (value) => {
    if (!value) {
      closeModal()
    }
  }
})

const fetchTasks = async (page = 1) => {
  loading.value = true
  try {
    const response = await api.get(`/tasks?page=${page}`)
    tasks.value = response.data.data
    currentPage.value = response.data.current_page
    totalPages.value = response.data.last_page
  } catch (error) {
    toast.error('Erreur lors du chargement des tâches')
  } finally {
    loading.value = false
  }
}

const handlePageChange = (page) => {
  fetchTasks(page)
}

const viewTask = (task) => {
  router.push(`/tasks/${task.id}`)
}

const editTask = (task) => {
  editingTask.value = task
  showEditModal.value = true
}

const deleteTask = async (task) => {
  if (confirm('Êtes-vous sûr de vouloir supprimer cette tâche ?')) {
    try {
      await api.delete(`/tasks/${task.id}`)
      toast.success('Tâche supprimée avec succès')
      fetchTasks(currentPage.value)
    } catch (error) {
      toast.error('Erreur lors de la suppression')
    }
  }
}

const handleStatusChange = async ({ task, newStatus }) => {
  try {
    await api.put(`/tasks/${task.id}`, { ...task, status: newStatus })
    toast.success(`Tâche déplacée vers ${getStatusLabel(newStatus)}`)
    fetchTasks(currentPage.value)
  } catch (error) {
    toast.error('Erreur lors du changement de statut')
  }
}

const getStatusLabel = (status) => {
  const labels = {
    pending: 'À faire',
    in_progress: 'En cours',
    completed: 'Terminé',
    canceled: 'Annulé'
  }
  return labels[status] || status
}

const handleFormSubmit = async (taskData) => {
  formLoading.value = true
  try {
    if (showEditModal.value) {
      await api.put(`/tasks/${editingTask.value.id}`, taskData)
      toast.success('Tâche mise à jour avec succès')
    } else {
      await api.post('/tasks', taskData)
      toast.success('Tâche créée avec succès')
    }
    closeModal()
    fetchTasks(currentPage.value)
  } catch (error) {
    const message = error.response?.data?.message || 'Erreur lors de l\'opération'
    toast.error(message)
  } finally {
    formLoading.value = false
  }
}

const closeModal = () => {
  showCreateModal.value = false
  showEditModal.value = false
  editingTask.value = null
}

onMounted(() => {
  fetchTasks()
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

.delay-900 {
  animation-delay: 0.9s;
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
.hover\:scale-105:hover {
  transform: scale(1.05);
}

/* Global styles for child components */
:deep(.glassmorphism-content) {
  color: white;
}

:deep(.glassmorphism-content .task-card) {
  background: rgba(255, 255, 255, 0.1);
  backdrop-filter: blur(20px);
  border: 1px solid rgba(255, 255, 255, 0.2);
  border-radius: 16px;
}

:deep(.glassmorphism-content .kanban-column) {
  background: rgba(255, 255, 255, 0.05);
  backdrop-filter: blur(10px);
  border: 1px solid rgba(255, 255, 255, 0.15);
  border-radius: 20px;
}

:deep(.glassmorphism-form input),
:deep(.glassmorphism-form textarea),
:deep(.glassmorphism-form select) {
  background: rgba(255, 255, 255, 0.1);
  border: 1px solid rgba(255, 255, 255, 0.2);
  color: white;
  border-radius: 12px;
}

:deep(.glassmorphism-form input::placeholder),
:deep(.glassmorphism-form textarea::placeholder) {
  color: rgba(255, 255, 255, 0.6);
}

:deep(.glassmorphism-form label) {
  color: rgba(255, 255, 255, 0.9);
  font-weight: 500;
}

:deep(.glassmorphism-form button) {
  background: rgba(255, 255, 255, 0.1);
  border: 1px solid rgba(255, 255, 255, 0.2);
  color: white;
  border-radius: 12px;
  transition: all 0.3s ease;
}

:deep(.glassmorphism-form button:hover) {
  background: rgba(255, 255, 255, 0.2);
  border-color: rgba(255, 255, 255, 0.4);
  transform: scale(1.05);
}
</style>