<template>
  <div class="space-y-8">
    <!-- Modern Header with Stats -->
    <div class="bg-gradient-to-r from-primary/5 to-secondary/5 rounded-xl p-6 border border-border/50">
      <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
        <div class="space-y-2">
          <h1 class="text-3xl font-bold tracking-tight text-primary">
            Mes Tâches
          </h1>
          <p class="text-muted-foreground text-lg">
            Gérez vos tâches et suivez votre progression
          </p>
        </div>
        
        <!-- Quick Stats -->
        <div class="flex items-center gap-4">
          <div class="text-center">
            <div class="text-2xl font-bold text-primary">{{ totalTasks }}</div>
            <div class="text-xs text-muted-foreground">Total</div>
          </div>
          <div class="text-center">
            <div class="text-2xl font-bold text-blue-500">{{ pendingTasks }}</div>
            <div class="text-xs text-muted-foreground">En attente</div>
          </div>
          <div class="text-center">
            <div class="text-2xl font-bold text-green-500">{{ completedTasks }}</div>
            <div class="text-xs text-muted-foreground">Terminées</div>
          </div>
        </div>
      </div>
    </div>

    <!-- Modern Filters Section -->
    <div class="bg-background border border-border rounded-xl p-6 shadow-sm">
      <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
        <div class="flex flex-col sm:flex-row items-start sm:items-center gap-4">
          <!-- Status Filter -->
          <div class="flex items-center gap-2">
            <label class="text-sm font-medium text-foreground">Statut:</label>
            <div class="relative">
              <select
                v-model="filters.status"
                class="appearance-none bg-background border border-input rounded-lg px-4 py-2 pr-8 text-sm focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary transition-colors"
              >
                <option value="">Tous les statuts</option>
                <option value="pending">⏳ En attente</option>
                <option value="in_progress">🔄 En cours</option>
                <option value="completed">✅ Terminé</option>
                <option value="cancelled">❌ Annulé</option>
              </select>
              <div class="absolute inset-y-0 right-0 flex items-center pr-2 pointer-events-none">
                <svg class="w-4 h-4 text-muted-foreground" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                </svg>
              </div>
            </div>
          </div>
          
          <!-- Priority Filter -->
          <div class="flex items-center gap-2">
            <label class="text-sm font-medium text-foreground">Priorité:</label>
            <div class="relative">
              <select
                v-model="filters.priority"
                class="appearance-none bg-background border border-input rounded-lg px-4 py-2 pr-8 text-sm focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary transition-colors"
              >
                <option value="">Toutes les priorités</option>
                <option value="low">🟢 Basse</option>
                <option value="medium">🟡 Moyenne</option>
                <option value="high">🔴 Haute</option>
              </select>
              <div class="absolute inset-y-0 right-0 flex items-center pr-2 pointer-events-none">
                <svg class="w-4 h-4 text-muted-foreground" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                </svg>
              </div>
            </div>
          </div>
        </div>
        
        <!-- Action Buttons -->
        <div class="flex items-center gap-3">
          <BaseButton
            variant="outline"
            size="sm"
            @click="clearFilters"
            class="flex items-center gap-2"
          >
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
            </svg>
            Effacer
          </BaseButton>
          <BaseButton 
            @click="$emit('create')"
            class="flex items-center gap-2"
          >
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
            </svg>
            Nouvelle tâche
          </BaseButton>
        </div>
      </div>
    </div>
    
    <!-- Loading State -->
    <div v-if="loading" class="flex items-center justify-center py-16">
      <div class="text-center space-y-4">
        <div class="relative">
          <div class="w-12 h-12 border-4 border-primary/20 border-t-primary rounded-full animate-spin"></div>
        </div>
        <p class="text-muted-foreground">Chargement de vos tâches...</p>
      </div>
    </div>
    
    <!-- Empty State -->
    <div v-else-if="filteredTasks.length === 0" class="text-center py-16">
      <div class="mx-auto w-24 h-24 bg-gradient-to-br from-primary/10 to-secondary/10 rounded-full flex items-center justify-center mb-6">
        <svg class="w-12 h-12 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01" />
        </svg>
      </div>
      <h3 class="text-xl font-semibold text-foreground mb-2">
        {{ hasActiveFilters ? 'Aucune tâche trouvée' : 'Aucune tâche' }}
      </h3>
      <p class="text-muted-foreground mb-6 max-w-md mx-auto">
        {{ hasActiveFilters ? 'Essayez de modifier vos filtres ou créez une nouvelle tâche.' : 'Commencez par créer votre première tâche pour organiser votre travail.' }}
      </p>
      <div class="flex items-center justify-center gap-3">
        <BaseButton 
          v-if="hasActiveFilters"
          variant="outline" 
          @click="clearFilters"
        >
          Effacer les filtres
        </BaseButton>
        <BaseButton 
          @click="$emit('create')"
          class=""
        >
          Créer une tâche
        </BaseButton>
      </div>
    </div>
    
    <!-- Task Grid with Modern Layout -->
    <div v-else class="space-y-6">
      <!-- View Toggle -->
      <div class="flex items-center justify-between">
        <div class="flex items-center gap-2">
          <span class="text-sm text-muted-foreground">
            {{ filteredTasks.length }} tâche{{ filteredTasks.length > 1 ? 's' : '' }} trouvée{{ filteredTasks.length > 1 ? 's' : '' }}
          </span>
        </div>
        <div class="flex items-center gap-2">
          <button
            @click="viewMode = 'grid'"
            :class="[
              'p-2 rounded-lg transition-colors',
              viewMode === 'grid' 
                ? 'bg-primary text-primary-foreground' 
                : 'bg-muted text-muted-foreground hover:text-foreground'
            ]"
          >
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z" />
            </svg>
          </button>
          <button
            @click="viewMode = 'list'"
            :class="[
              'p-2 rounded-lg transition-colors',
              viewMode === 'list' 
                ? 'bg-primary text-primary-foreground' 
                : 'bg-muted text-muted-foreground hover:text-foreground'
            ]"
          >
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16" />
            </svg>
          </button>
        </div>
      </div>

      <!-- Grid View -->
      <div v-if="viewMode === 'grid'" class="grid gap-6 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4">
        <TaskCard
          v-for="task in paginatedTasks"
          :key="task.id"
          :task="task"
          @click="$emit('view', task)"
          @view="$emit('view', task)"
          @edit="$emit('edit', task)"
          @delete="$emit('delete', task)"
        />
      </div>

      <!-- List View -->
      <div v-else class="space-y-3">
        <div
          v-for="task in paginatedTasks"
          :key="task.id"
          class="bg-background border border-border rounded-xl p-4 hover:shadow-md transition-all duration-200 cursor-pointer group"
          @click="$emit('view', task)"
        >
          <div class="flex items-center justify-between">
            <div class="flex items-center gap-4 flex-1 min-w-0">
              <div class="flex-shrink-0">
                <div class="w-3 h-3 rounded-full" :class="getStatusColor(task.status)"></div>
              </div>
              <div class="flex-1 min-w-0">
                <h3 class="font-medium text-foreground truncate group-hover:text-primary transition-colors">
                  {{ task.title }}
                </h3>
                <p v-if="task.description" class="text-sm text-muted-foreground truncate mt-1">
                  {{ task.description }}
                </p>
              </div>
            </div>
            <div class="flex items-center gap-3 flex-shrink-0">
              <BaseBadge :variant="getPriorityVariant(task.priority)" size="sm">
                {{ getPriorityLabel(task.priority) }}
              </BaseBadge>
              <span v-if="task.due_date" class="text-sm text-muted-foreground">
                {{ formatDate(task.due_date) }}
              </span>
              <div class="flex items-center gap-1 opacity-0 group-hover:opacity-100 transition-opacity">
                <BaseButton
                  variant="ghost"
                  size="sm"
                  @click.stop="$emit('edit', task)"
                >
                  <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                  </svg>
                </BaseButton>
                <BaseButton
                  variant="ghost"
                  size="sm"
                  @click.stop="$emit('delete', task)"
                >
                  <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                  </svg>
                </BaseButton>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
    
    <!-- Modern Pagination -->
    <div v-if="totalPages > 1" class="flex items-center justify-center">
      <div class="flex items-center gap-2 bg-background border border-border rounded-xl p-2">
        <BaseButton
          variant="ghost"
          size="sm"
          :disabled="currentPage === 1"
          @click="$emit('page-change', currentPage - 1)"
          class="flex items-center gap-2"
        >
          <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
          </svg>
          Précédent
        </BaseButton>
        
        <div class="flex items-center gap-1">
          <button
            v-for="page in visiblePages"
            :key="page"
            @click="$emit('page-change', page)"
            :class="[
              'px-3 py-1 rounded-lg text-sm font-medium transition-colors',
              page === currentPage
                ? 'bg-primary text-primary-foreground'
                : 'text-muted-foreground hover:text-foreground hover:bg-muted'
            ]"
          >
            {{ page }}
          </button>
        </div>
        
        <BaseButton
          variant="ghost"
          size="sm"
          :disabled="currentPage === totalPages"
          @click="$emit('page-change', currentPage + 1)"
          class="flex items-center gap-2"
        >
          Suivant
          <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
          </svg>
        </BaseButton>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed } from 'vue'
import { BaseButton, BaseBadge, TaskCard } from '@/components'
import { cn } from '@/lib/utils'

const props = defineProps({
  tasks: {
    type: Array,
    default: () => []
  },
  loading: {
    type: Boolean,
    default: false
  },
  currentPage: {
    type: Number,
    default: 1
  },
  totalPages: {
    type: Number,
    default: 1
  }
})

const emit = defineEmits(['create', 'view', 'edit', 'delete', 'page-change'])

const filters = ref({
  status: '',
  priority: ''
})

const viewMode = ref('grid')

// Computed properties
const filteredTasks = computed(() => {
  let filtered = [...props.tasks]
  
  if (filters.value.status) {
    filtered = filtered.filter(task => task.status === filters.value.status)
  }
  
  if (filters.value.priority) {
    filtered = filtered.filter(task => task.priority === filters.value.priority)
  }
  
  return filtered
})

const hasActiveFilters = computed(() => {
  return filters.value.status || filters.value.priority
})

const totalTasks = computed(() => props.tasks.length)
const pendingTasks = computed(() => props.tasks.filter(task => task.status === 'pending').length)
const completedTasks = computed(() => props.tasks.filter(task => task.status === 'completed').length)

const paginatedTasks = computed(() => {
  const itemsPerPage = viewMode.value === 'grid' ? 12 : 20
  const start = (props.currentPage - 1) * itemsPerPage
  const end = start + itemsPerPage
  return filteredTasks.value.slice(start, end)
})

const visiblePages = computed(() => {
  const pages = []
  const maxVisible = 5
  let start = Math.max(1, props.currentPage - Math.floor(maxVisible / 2))
  let end = Math.min(props.totalPages, start + maxVisible - 1)
  
  if (end - start + 1 < maxVisible) {
    start = Math.max(1, end - maxVisible + 1)
  }
  
  for (let i = start; i <= end; i++) {
    pages.push(i)
  }
  
  return pages
})

// Methods
const clearFilters = () => {
  filters.value.status = ''
  filters.value.priority = ''
}

const getStatusColor = (status) => {
  const colors = {
    pending: 'bg-yellow-500',
    in_progress: 'bg-blue-500',
    completed: 'bg-green-500',
    cancelled: 'bg-red-500'
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
  if (!date) return ''
  return new Date(date).toLocaleDateString('fr-FR', {
    day: '2-digit',
    month: '2-digit'
  })
}
</script>

<style scoped>
/* Custom scrollbar for better UX */
::-webkit-scrollbar {
  width: 6px;
}

::-webkit-scrollbar-track {
  background: transparent;
}

::-webkit-scrollbar-thumb {
  background: hsl(var(--muted-foreground) / 0.3);
  border-radius: 3px;
}

::-webkit-scrollbar-thumb:hover {
  background: hsl(var(--muted-foreground) / 0.5);
}

/* Smooth transitions */
.transition-all {
  transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
}
</style> 