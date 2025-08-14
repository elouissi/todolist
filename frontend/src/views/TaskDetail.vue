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
        @create-task="showCreateModal = true"
        class="bg-white/5 backdrop-blur-xl border-b border-white/10"
      />
    </div>

    <main class="relative z-10 container mx-auto px-4 py-8">
      <!-- Loading State -->
      <div v-if="loading" class="flex justify-center py-16">
        <div class="w-12 h-12 border-2 border-white/30 border-t-white rounded-full animate-spin"></div>
      </div>

      <!-- Task Not Found -->
      <div v-else-if="!task" class="text-center py-16">
        <div class="bg-white/10 backdrop-blur-xl border border-white/20 rounded-3xl p-12 max-w-md mx-auto">
          <div class="mx-auto h-20 w-20 text-white/60 mb-6">
            <DocumentIcon class="h-full w-full" />
          </div>
          <h3 class="text-xl font-medium text-white mb-3">Tâche non trouvée</h3>
          <p class="text-white/70 mb-6">
            La tâche que vous recherchez n'existe pas ou a été supprimée.
          </p>
          <button
            @click="$router.push('/tasks')"
            class="bg-white/20 hover:bg-white/30 border border-white/30 hover:border-white/50 text-white px-6 py-3 rounded-xl font-medium transition-all duration-300 hover:scale-105 backdrop-blur-sm"
          >
            Retour aux tâches
          </button>
        </div>
      </div>

      <!-- Task Content -->
      <div v-else class="space-y-8">
        <!-- Header -->
        <div class="bg-white/10 backdrop-blur-xl border border-white/20 rounded-3xl p-8 shadow-2xl">
          <div class="flex items-start justify-between">
            <div class="flex-1">
              <div class="flex items-center space-x-3 mb-4">
                <button
                  @click="$router.push('/tasks')"
                  class="flex items-center gap-2 bg-white/10 hover:bg-white/20 border border-white/20 hover:border-white/40 text-white px-4 py-2 rounded-xl font-medium transition-all duration-300 hover:scale-105 backdrop-blur-sm"
                >
                  <ArrowLeftIcon class="h-4 w-4" />
                  Retour
                </button>
              </div>
              <h1 class="text-4xl font-bold text-white mb-4">{{ task.title }}</h1>
              <div class="flex items-center space-x-3">
                <span :class="getStatusClasses(task.status)" class="inline-flex items-center px-4 py-2 rounded-full text-sm font-medium backdrop-blur-sm">
                  {{ getStatusLabel(task.status) }}
                </span>
                <span :class="getPriorityClasses(task.priority)" class="inline-flex items-center px-4 py-2 rounded-full text-sm font-medium backdrop-blur-sm">
                  {{ getPriorityLabel(task.priority) }}
                </span>
              </div>
            </div>
            <div class="flex items-center space-x-3 ml-6">
              <button
                @click="editTask"
                class="flex items-center gap-2 bg-white/10 hover:bg-white/20 border border-white/20 hover:border-white/40 text-white px-4 py-3 rounded-xl font-medium transition-all duration-300 hover:scale-105 backdrop-blur-sm"
              >
                <PencilIcon class="h-4 w-4" />
                Modifier
              </button>
              <button
                @click="deleteTask"
                class="flex items-center gap-2 bg-red-500/20 hover:bg-red-500/30 border border-red-500/30 hover:border-red-500/50 text-red-300 px-4 py-3 rounded-xl font-medium transition-all duration-300 hover:scale-105 backdrop-blur-sm"
              >
                <TrashIcon class="h-4 w-4" />
                Supprimer
              </button>
            </div>
          </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
          <!-- Main content -->
          <div class="lg:col-span-2 space-y-8">
            <!-- Description -->
            <div class="bg-white/10 backdrop-blur-xl border border-white/20 rounded-3xl p-8 shadow-2xl">
              <h3 class="text-xl font-semibold text-white mb-6 flex items-center gap-3">
                <div class="w-8 h-8 bg-white/20 rounded-lg flex items-center justify-center">
                  <DocumentIcon class="w-5 h-5 text-white" />
                </div>
                Description
              </h3>
              <div class="prose prose-sm max-w-none">
                <p v-if="task.description" class="text-white/80 leading-relaxed text-lg">
                  {{ task.description }}
                </p>
                <p v-else class="text-white/50 italic text-lg">
                  Aucune description fournie
                </p>
              </div>
            </div>

            <!-- Comments section -->
            <div class="bg-white/10 backdrop-blur-xl border border-white/20 rounded-3xl p-8 shadow-2xl">
              <h3 class="text-xl font-semibold text-white mb-6 flex items-center gap-3">
                <div class="w-8 h-8 bg-white/20 rounded-lg flex items-center justify-center">
                  <ChatBubbleLeftRightIcon class="w-5 h-5 text-white" />
                </div>
                Commentaires
              </h3>
              
              <div class="space-y-6">
                <div v-if="comments.length === 0" class="text-center py-12">
                  <ChatBubbleLeftRightIcon class="h-16 w-16 text-white/40 mx-auto mb-4" />
                  <p class="text-white/60 text-lg">Aucun commentaire pour le moment</p>
                </div>
                
                <div v-else class="space-y-4">
                  <div
                    v-for="comment in comments"
                    :key="comment.id"
                    class="bg-white/5 border border-white/10 rounded-2xl p-6 backdrop-blur-sm"
                  >
                    <div class="flex items-start justify-between">
                      <div class="flex-1">
                        <p class="font-medium text-white mb-2">
                          {{ comment.user?.full_name || 'Utilisateur' }}
                        </p>
                        <p class="text-white/80 mb-3 leading-relaxed">
                          {{ comment.content }}
                        </p>
                        <p class="text-sm text-white/50">
                          {{ formatDate(comment.created_at) }}
                        </p>
                      </div>
                    </div>
                  </div>
                </div>
                
                <!-- Add comment form -->
                <div class="border-t border-white/20 pt-6">
                  <div class="space-y-4">
                    <textarea
                      v-model="newComment"
                      rows="3"
                      placeholder="Ajouter un commentaire..."
                      class="w-full bg-white/10 border border-white/20 rounded-xl px-4 py-3 text-white placeholder-white/50 focus:outline-none focus:ring-2 focus:ring-white/30 focus:border-white/40 transition-all duration-300 backdrop-blur-sm resize-none"
                    ></textarea>
                    <div class="flex justify-end">
                      <button
                        @click="addComment"
                        :disabled="!newComment.trim()"
                        class="flex items-center gap-2 bg-white/20 hover:bg-white/30 disabled:bg-white/10 border border-white/30 hover:border-white/50 disabled:border-white/20 text-white disabled:text-white/40 px-6 py-3 rounded-xl font-medium transition-all duration-300 hover:scale-105 backdrop-blur-sm disabled:hover:scale-100 disabled:cursor-not-allowed"
                      >
                        <PaperAirplaneIcon class="h-4 w-4" />
                        Ajouter un commentaire
                      </button>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <!-- Sidebar -->
          <div class="space-y-8">
            <!-- Task details -->
            <div class="bg-white/10 backdrop-blur-xl border border-white/20 rounded-3xl p-8 shadow-2xl">
              <h3 class="text-xl font-semibold text-white mb-6">Détails</h3>
              <div class="space-y-6">
                <div>
                  <label class="text-sm font-medium text-white/70 block mb-2">Statut</label>
                  <span :class="getStatusClasses(task.status)" class="inline-flex items-center px-3 py-2 rounded-full text-sm font-medium backdrop-blur-sm">
                    {{ getStatusLabel(task.status) }}
                  </span>
                </div>
                
                <div>
                  <label class="text-sm font-medium text-white/70 block mb-2">Priorité</label>
                  <span :class="getPriorityClasses(task.priority)" class="inline-flex items-center px-3 py-2 rounded-full text-sm font-medium backdrop-blur-sm">
                    {{ getPriorityLabel(task.priority) }}
                  </span>
                </div>
                
                <div>
                  <label class="text-sm font-medium text-white/70 block mb-2">Date de création</label>
                  <p class="text-white">
                    {{ formatDate(task.created_at) }}
                  </p>
                </div>
                
                <div v-if="task.due_date">
                  <label class="text-sm font-medium text-white/70 block mb-2">Date d'échéance</label>
                  <p class="text-white">
                    {{ formatDate(task.due_date) }}
                  </p>
                </div>
                
                <div v-if="task.updated_at">
                  <label class="text-sm font-medium text-white/70 block mb-2">Dernière modification</label>
                  <p class="text-white">
                    {{ formatDate(task.updated_at) }}
                  </p>
                </div>
              </div>
            </div>

            <!-- Actions -->
            <div class="bg-white/10 backdrop-blur-xl border border-white/20 rounded-3xl p-8 shadow-2xl">
              <h3 class="text-xl font-semibold text-white mb-6">Actions</h3>
              <div class="space-y-3">
                <button
                  v-if="task.status !== 'completed'"
                  @click="updateStatus('completed')"
                  class="w-full flex items-center gap-3 bg-green-500/20 hover:bg-green-500/30 border border-green-500/30 hover:border-green-500/50 text-green-300 px-4 py-3 rounded-xl font-medium transition-all duration-300 hover:scale-105 backdrop-blur-sm"
                >
                  <CheckIcon class="h-4 w-4" />
                  Marquer comme terminée
                </button>
                
                <button
                  v-if="task.status === 'pending'"
                  @click="updateStatus('in_progress')"
                  class="w-full flex items-center gap-3 bg-blue-500/20 hover:bg-blue-500/30 border border-blue-500/30 hover:border-blue-500/50 text-blue-300 px-4 py-3 rounded-xl font-medium transition-all duration-300 hover:scale-105 backdrop-blur-sm"
                >
                  <PlayIcon class="h-4 w-4" />
                  Commencer
                </button>
                
                <button
                  v-if="task.status === 'in_progress'"
                  @click="updateStatus('pending')"
                  class="w-full flex items-center gap-3 bg-yellow-500/20 hover:bg-yellow-500/30 border border-yellow-500/30 hover:border-yellow-500/50 text-yellow-300 px-4 py-3 rounded-xl font-medium transition-all duration-300 hover:scale-105 backdrop-blur-sm"
                >
                  <PauseIcon class="h-4 w-4" />
                  Mettre en pause
                </button>

                <button
                  v-if="task.status !== 'canceled'"
                  @click="updateStatus('canceled')"
                  class="w-full flex items-center gap-3 bg-red-500/20 hover:bg-red-500/30 border border-red-500/30 hover:border-red-500/50 text-red-300 px-4 py-3 rounded-xl font-medium transition-all duration-300 hover:scale-105 backdrop-blur-sm"
                >
                  <XMarkIcon class="h-4 w-4" />
                  Annuler la tâche
                </button>

                <button
                  v-if="task.status === 'canceled'"
                  @click="updateStatus('pending')"
                  class="w-full flex items-center gap-3 bg-white/10 hover:bg-white/20 border border-white/20 hover:border-white/40 text-white px-4 py-3 rounded-xl font-medium transition-all duration-300 hover:scale-105 backdrop-blur-sm"
                >
                  <ArrowPathIcon class="h-4 w-4" />
                  Réactiver
                </button>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Edit Modal -->
      <BaseModal
        v-model="showEditModal"
        title="Modifier la tâche"
        size="lg"
        class="backdrop-blur-xl"
      >
        <div class="bg-white/10 backdrop-blur-xl border border-white/20 rounded-2xl p-6">
          <TaskForm
            :task="task"
            :loading="formLoading"
            @submit="handleFormSubmit"
            @cancel="showEditModal = false"
            class="glassmorphism-form"
          />
        </div>
      </BaseModal>

      <!-- Create Task Modal -->
      <BaseModal
        v-model="showCreateModal"
        title="Nouvelle tâche"
        size="lg"
        class="backdrop-blur-xl"
      >
        <div class="bg-white/10 backdrop-blur-xl border border-white/20 rounded-2xl p-6">
          <TaskForm
            :loading="createFormLoading"
            @submit="handleCreateFormSubmit"
            @cancel="showCreateModal = false"
            class="glassmorphism-form"
          />
        </div>
      </BaseModal>
    </main>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import { useToast } from 'vue-toastification'
import { useTasksStore } from '@/stores/tasks'
import { useNotificationsStore } from '@/stores/notifications'
import { 
  ArrowLeftIcon,
  PencilIcon,
  TrashIcon,
  DocumentIcon,
  ChatBubbleLeftRightIcon,
  PaperAirplaneIcon,
  CheckIcon,
  PlayIcon,
  PauseIcon,
  XMarkIcon,
  ArrowPathIcon
} from '@heroicons/vue/24/outline'
import api from '@/services/api'
import {
  BaseModal,
  AppNavigation,
  TaskForm
} from '@/components'

const route = useRoute()
const router = useRouter()
const toast = useToast()
const tasksStore = useTasksStore()
const notificationsStore = useNotificationsStore()

const task = ref(null)
const comments = ref([])
const loading = ref(false)
const formLoading = ref(false)
const createFormLoading = ref(false)
const showEditModal = ref(false)
const showCreateModal = ref(false)
const newComment = ref('')

const fetchTask = async () => {
  loading.value = true
  try {
    const response = await api.get(`/tasks/${route.params.id}`)
    task.value = response.data.data
  } catch (error) {
    toast.error('Erreur lors du chargement de la tâche')
    router.push('/tasks')
  } finally {
    loading.value = false
  }
}

const fetchComments = async () => {
  try {
    const response = await api.get(`/tasks/${route.params.id}/comments`)
    comments.value = response.data.data
  } catch (error) {
    console.error('Erreur lors du chargement des commentaires:', error)
  }
}

const addComment = async () => {
  if (!newComment.value.trim()) return
  
  try {
    const response = await api.post(`/tasks/${route.params.id}/comments`, {
      content: newComment.value
    })
    comments.value.push(response.data.data)
    newComment.value = ''
    toast.success('Commentaire ajouté')
  } catch (error) {
    toast.error('Erreur lors de l\'ajout du commentaire')
  }
}

const updateStatus = async (status) => {
  try {
    const response = await api.put(`/tasks/${route.params.id}`, { status })
    task.value = response.data.data
    toast.success('Statut mis à jour')
  } catch (error) {
    toast.error('Erreur lors de la mise à jour du statut')
  }
}

const editTask = () => {
  showEditModal.value = true
}

const deleteTask = async () => {
  if (confirm('Êtes-vous sûr de vouloir supprimer cette tâche ?')) {
    try {
      await api.delete(`/tasks/${route.params.id}`)
      toast.success('Tâche supprimée')
      router.push('/tasks')
    } catch (error) {
      toast.error('Erreur lors de la suppression')
    }
  }
}

const handleFormSubmit = async (taskData) => {
  formLoading.value = true
  try {
    const response = await api.put(`/tasks/${route.params.id}`, taskData)
    task.value = response.data.data
    showEditModal.value = false
    toast.success('Tâche mise à jour')
  } catch (error) {
    toast.error('Erreur lors de la mise à jour')
  } finally {
    formLoading.value = false
  }
}

const handleCreateFormSubmit = async (taskData) => {
  createFormLoading.value = true
  try {
    const result = await tasksStore.createTask(taskData)
    if (result.success) {
      showCreateModal.value = false
      toast.success('Tâche créée avec succès')
    }
  } catch (error) {
    toast.error('Erreur lors de la création de la tâche')
  } finally {
    createFormLoading.value = false
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

const getPriorityLabel = (priority) => {
  const labels = {
    low: 'Basse',
    medium: 'Moyenne',
    high: 'Haute'
  }
  return labels[priority] || priority
}

const getStatusClasses = (status) => {
  const classes = {
    pending: 'bg-yellow-500/20 text-yellow-300 border border-yellow-500/30',
    in_progress: 'bg-blue-500/20 text-blue-300 border border-blue-500/30',
    completed: 'bg-green-500/20 text-green-300 border border-green-500/30',
    canceled: 'bg-red-500/20 text-red-300 border border-red-500/30'
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
  if (!dateString) return 'Date invalide'
  const date = new Date(dateString)
  if (isNaN(date.getTime())) return 'Date invalide'
  
  return date.toLocaleDateString('fr-FR', {
    day: 'numeric',
    month: 'long',
    year: 'numeric',
    hour: '2-digit',
    minute: '2-digit'
  })
}

onMounted(() => {
  fetchTask()
  fetchComments()
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

/* Form styling */
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