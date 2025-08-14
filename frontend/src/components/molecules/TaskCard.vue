<template>
  <BaseCard class="group hover:shadow-lg transition-all duration-200 cursor-pointer border-l-4 border-l-transparent hover:border-l-primary/20" @click="$emit('click')">
    <template #header>
      <div class="flex items-start justify-between">
        <div class="flex-1 min-w-0">
          <h3 class="text-lg font-semibold leading-tight tracking-tight text-foreground group-hover:text-primary transition-colors">
            {{ task.title }}
          </h3>
          <p v-if="task.description" class="text-sm text-muted-foreground mt-1 line-clamp-2">
            {{ task.description }}
          </p>
        </div>
        <div class="flex items-center space-x-2 ml-3">
          <BaseBadge :variant="priorityVariant" :size="'sm'" class="shrink-0">
            <template #icon>
              <ExclamationTriangleIcon v-if="task.priority === 'high'" class="h-3 w-3" />
              <MinusIcon v-else-if="task.priority === 'low'" class="h-3 w-3" />
              <ExclamationCircleIcon v-else class="h-3 w-3" />
            </template>
            {{ getPriorityLabel(task.priority) }}
          </BaseBadge>
          <BaseBadge :variant="statusVariant" :size="'sm'" class="shrink-0">
            <template #icon>
              <ClockIcon v-if="task.status === 'pending'" class="h-3 w-3" />
              <PlayIcon v-else-if="task.status === 'in_progress'" class="h-3 w-3" />
              <CheckCircleIcon v-else-if="task.status === 'completed'" class="h-3 w-3" />
              <XCircleIcon v-else-if="task.status === 'canceled'" class="h-3 w-3" />
            </template>
            {{ getStatusLabel(task.status) }}
          </BaseBadge>
        </div>
      </div>
    </template>
    
    <div class="space-y-3">
      <!-- Task metadata -->
      <div class="flex items-center justify-between text-sm">
        <div class="flex items-center space-x-4 text-muted-foreground">
          <div class="flex items-center space-x-1">
            <UserIcon class="h-4 w-4" />
            <span>{{ task.user?.full_name || 'Utilisateur' }}</span>
          </div>
          <div v-if="task.due_date" class="flex items-center space-x-1">
            <CalendarIcon class="h-4 w-4" />
            <span>{{ formatDate(task.due_date) }}</span>
          </div>
        </div>
        
        <!-- Action buttons - hidden by default, shown on hover -->
        <div class="flex items-center space-x-1 opacity-0 group-hover:opacity-100 transition-opacity duration-200">
          <BaseButton
            variant="ghost"
            size="sm"
            class="h-8 w-8 p-0 hover:bg-primary/10"
            @click.stop="$emit('edit')"
          >
            <PencilIcon class="h-4 w-4" />
          </BaseButton>
          <BaseButton
            variant="ghost"
            size="sm"
            class="h-8 w-8 p-0 hover:bg-destructive/10 hover:text-destructive"
            @click.stop="$emit('delete')"
          >
            <TrashIcon class="h-4 w-4" />
          </BaseButton>
          <BaseButton
            variant="ghost"
            size="sm"
            class="h-8 w-8 p-0 hover:bg-primary/10"
            @click.stop="$emit('view')"
          >
            <EyeIcon class="h-4 w-4" />
          </BaseButton>
        </div>
      </div>
      
      <!-- Progress indicator for in_progress tasks -->
      <div v-if="task.status === 'in_progress'" class="w-full bg-muted rounded-full h-1">
        <div class="bg-primary h-1 rounded-full animate-pulse" style="width: 60%"></div>
      </div>
      
      <!-- Completion indicator -->
      <div v-if="task.status === 'completed'" class="flex items-center space-x-2 text-sm text-green-600">
        <CheckCircleIcon class="h-4 w-4" />
        <span>Terminée le {{ formatDate(task.updated_at) }}</span>
      </div>
    </div>
  </BaseCard>
</template>

<script setup>
import { computed } from 'vue'
import {
  PencilIcon,
  TrashIcon,
  EyeIcon,
  UserIcon,
  CalendarIcon,
  ClockIcon,
  PlayIcon,
  CheckCircleIcon,
  XCircleIcon,
  ExclamationTriangleIcon,
  ExclamationCircleIcon,
  MinusIcon
} from '@heroicons/vue/24/outline'
import BaseCard from '@/components/atoms/BaseCard.vue'
import BaseButton from '@/components/atoms/BaseButton.vue'
import BaseBadge from '@/components/atoms/BaseBadge.vue'

const props = defineProps({
  task: {
    type: Object,
    required: true
  }
})

const emit = defineEmits(['click', 'edit', 'delete', 'view'])

const statusVariant = computed(() => {
  const statusMap = {
    'pending': 'secondary',
    'in_progress': 'default',
    'completed': 'outline',
    'canceled': 'destructive'
  }
  return statusMap[props.task.status] || 'secondary'
})

const priorityVariant = computed(() => {
  const priorityMap = {
    'low': 'secondary',
    'medium': 'default',
    'high': 'destructive'
  }
  return priorityMap[props.task.priority] || 'secondary'
})

const getStatusLabel = (status) => {
  const labels = {
    'pending': 'À faire',
    'in_progress': 'En cours',
    'completed': 'Terminé',
    'canceled': 'Annulé'
  }
  return labels[status] || status
}

const getPriorityLabel = (priority) => {
  const labels = {
    'low': 'Basse',
    'medium': 'Moyenne',
    'high': 'Haute'
  }
  return labels[priority] || priority
}

const formatDate = (dateString) => {
  if (!dateString) return ''
  const date = new Date(dateString)
  if (isNaN(date.getTime())) return ''
  
  return date.toLocaleDateString('fr-FR', {
    day: '2-digit',
    month: '2-digit',
    year: 'numeric'
  })
}
</script> 