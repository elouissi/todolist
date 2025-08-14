<template>
  <div class="kanban-board">
    <!-- Header avec statistiques -->
    <div class="mb-6">
      <div class="flex items-center justify-between">
        <h2 class="text-2xl font-bold text-foreground">Tableau Kanban</h2>
        <div class="flex items-center gap-4">
          <BaseBadge variant="secondary" class="text-sm">
            {{ totalTasks }} tâches
          </BaseBadge>
          <BaseButton @click="$emit('create')" size="sm">
            <PlusIcon class="w-4 h-4 mr-2" />
            Nouvelle tâche
          </BaseButton>
        </div>
      </div>
    </div>

    <!-- Board avec colonnes -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
      <div
        v-for="status in statuses"
        :key="status.value"
        class="kanban-column"
      >
        <!-- Header de colonne -->
        <div class="flex items-center justify-between mb-4">
          <div class="flex items-center gap-2">
            <div 
              class="w-3 h-3 rounded-full"
              :class="getStatusColor(status.value)"
            ></div>
            <h3 class="font-semibold text-foreground">{{ status.label }}</h3>
          </div>
          <BaseBadge variant="outline" class="text-xs">
            {{ getTasksByStatus(status.value).length }}
          </BaseBadge>
        </div>

        <!-- Zone de drop -->
        <div
          class="kanban-drop-zone min-h-[400px] p-2 rounded-lg border-2 border-dashed border-muted-foreground/20 transition-colors"
          :class="{
            'border-primary/50 bg-primary/5': isDragOver === status.value
          }"
          @drop="handleDrop($event, status.value)"
          @dragover.prevent
          @dragenter.prevent="isDragOver = status.value"
          @dragleave.prevent="isDragOver = null"
        >
          <!-- Tâches dans cette colonne -->
          <TransitionGroup
            name="task"
            tag="div"
            class="space-y-3"
          >
            <div
              v-for="task in getTasksByStatus(status.value)"
              :key="task.id"
              class="kanban-task"
              draggable="true"
              @dragstart="handleDragStart($event, task)"
              @click="$emit('view', task)"
            >
              <BaseCard class="cursor-pointer hover:shadow-md transition-shadow">
                <div class="p-4">
                  <!-- Header de la tâche -->
                  <div class="flex items-start justify-between mb-3">
                    <h4 class="font-medium text-foreground line-clamp-2">
                      {{ task.title }}
                    </h4>
                    <div class="flex items-center gap-1">
                      <BaseBadge 
                        :variant="getPriorityVariant(task.priority)"
                        class="text-xs"
                      >
                        {{ getPriorityLabel(task.priority) }}
                      </BaseBadge>
                    </div>
                  </div>

                  <!-- Description -->
                  <p class="text-sm text-muted-foreground line-clamp-2 mb-3">
                    {{ task.description }}
                  </p>

                  <!-- Footer avec métadonnées -->
                  <div class="flex items-center justify-between text-xs text-muted-foreground">
                    <div class="flex items-center gap-2">
                      <CalendarIcon class="w-3 h-3" />
                      <span>{{ formatDate(task.due_date) }}</span>
                    </div>
                    <div class="flex items-center gap-1">
                      <UserIcon class="w-3 h-3" />
                      <span>{{ authStore.user?.full_name || 'Utilisateur' }}</span>
                    </div>
                  </div>

                  <!-- Actions -->
                  <div class="flex items-center justify-end gap-2 mt-3 pt-3 border-t border-border">
                    <BaseButton
                      variant="ghost"
                      size="sm"
                      @click.stop="$emit('edit', task)"
                    >
                      <PencilIcon class="w-3 h-3" />
                    </BaseButton>
                    <BaseButton
                      variant="ghost"
                      size="sm"
                      @click.stop="$emit('delete', task)"
                    >
                      <TrashIcon class="w-3 h-3" />
                    </BaseButton>
                  </div>
                </div>
              </BaseCard>
            </div>
          </TransitionGroup>

          <!-- Message si colonne vide -->
          <div
            v-if="getTasksByStatus(status.value).length === 0"
            class="flex items-center justify-center h-32 text-muted-foreground"
          >
            <p class="text-sm">Aucune tâche</p>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed } from 'vue'
import { 
  PlusIcon, 
  CalendarIcon, 
  UserIcon, 
  PencilIcon, 
  TrashIcon 
} from '@heroicons/vue/24/outline'
import { BaseCard, BaseButton, BaseBadge } from '@/components'
import { useAuthStore } from '@/stores/auth'

const props = defineProps({
  tasks: {
    type: Array,
    default: () => []
  },
  loading: {
    type: Boolean,
    default: false
  }
})

const emit = defineEmits(['create', 'view', 'edit', 'delete', 'status-change'])

const authStore = useAuthStore()
const isDragOver = ref(null)
const draggedTask = ref(null)

const statuses = [
  { value: 'pending', label: 'À faire' },
  { value: 'in_progress', label: 'En cours' },
  { value: 'completed', label: 'Terminé' },
  { value: 'canceled', label: 'Annulé' }
]

const totalTasks = computed(() => props.tasks.length)

const getTasksByStatus = (status) => {
  return props.tasks.filter(task => task.status === status)
}

const getStatusColor = (status) => {
  const colors = {
    pending: 'bg-yellow-500',
    in_progress: 'bg-blue-500',
    completed: 'bg-green-500',
    canceled: 'bg-red-500'
  }
  return colors[status] || 'bg-gray-500'
}

const getPriorityVariant = (priority) => {
  const variants = {
    low: 'secondary',
    medium: 'default',
    high: 'destructive'
  }
  return variants[priority] || 'secondary'
}

const getPriorityLabel = (priority) => {
  const labels = {
    low: 'Basse',
    medium: 'Moyenne',
    high: 'Haute'
  }
  return labels[priority] || priority
}

const formatDate = (date) => {
  if (!date) return 'Aucune date'
  return new Date(date).toLocaleDateString('fr-FR', {
    day: '2-digit',
    month: '2-digit'
  })
}

const handleDragStart = (event, task) => {
  draggedTask.value = task
  event.dataTransfer.effectAllowed = 'move'
  event.dataTransfer.setData('text/plain', task.id)
}

const handleDrop = (event, newStatus) => {
  event.preventDefault()
  isDragOver.value = null
  
  if (draggedTask.value && draggedTask.value.status !== newStatus) {
    emit('status-change', {
      task: draggedTask.value,
      newStatus: newStatus
    })
  }
  
  draggedTask.value = null
}
</script>

<style scoped>
.kanban-board {
  @apply w-full;
}

.kanban-column {
  @apply flex flex-col;
}

.kanban-drop-zone {
  @apply bg-muted/30;
}

.kanban-task {
  @apply transition-all duration-200;
}

.kanban-task:hover {
  @apply transform scale-[1.02];
}

/* Animations pour les tâches */
.task-enter-active,
.task-leave-active {
  transition: all 0.3s ease;
}

.task-enter-from {
  opacity: 0;
  transform: translateY(30px);
}

.task-leave-to {
  opacity: 0;
  transform: translateY(-30px);
}

.task-move {
  transition: transform 0.3s ease;
}

/* Line clamp pour le texte */
.line-clamp-2 {
  display: -webkit-box;
  -webkit-line-clamp: 2;
  -webkit-box-orient: vertical;
  overflow: hidden;
}
</style> 