<template>
  <div class="space-y-6">
    <!-- Modern Header -->
    <div class="text-center space-y-2">
      <h1 class="text-2xl font-bold tracking-tight text-primary">
        {{ task ? 'Modifier la tâche' : 'Créer une nouvelle tâche' }}
      </h1>
      <p class="text-muted-foreground">
        {{ task ? 'Mettez à jour les informations de votre tâche' : 'Remplissez les informations pour créer votre tâche' }}
      </p>
    </div>

    <!-- Modern Form -->
    <form @submit.prevent="handleSubmit" class="space-y-6">
      <!-- Basic Information Section -->
      <div class="bg-background border border-border rounded-lg p-6">
        <div class="space-y-2 mb-4">
          <h2 class="text-lg font-semibold text-foreground flex items-center gap-2">
            <svg class="w-5 h-5 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            Informations de base
          </h2>
          <p class="text-sm text-muted-foreground">Définissez le titre et la description de votre tâche</p>
        </div>

        <div class="space-y-4">
          <!-- Title Field -->
          <div class="space-y-2">
            <label class="text-sm font-medium text-foreground flex items-center gap-2">
              <span class="text-destructive">*</span>
              Titre de la tâche
            </label>
            <div class="relative">
              <BaseInput
                v-model="form.title"
                placeholder="Ex: Développer la page d'accueil"
                :error="errors.title"
                class="pl-10"
                required
              />
              <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                <svg class="w-5 h-5 text-muted-foreground" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                </svg>
              </div>
            </div>
            <div v-if="errors.title" class="flex items-center gap-2 text-sm text-destructive">
              <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
              </svg>
              {{ errors.title }}
            </div>
          </div>

          <!-- Description Field -->
          <div class="space-y-2">
            <label class="text-sm font-medium text-foreground flex items-center gap-2">
              <svg class="w-4 h-4 text-muted-foreground" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 8h10M7 12h4m1 8l-4-4H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-3l-4 4z" />
              </svg>
              Description
            </label>
            <div class="relative">
              <textarea
                v-model="form.description"
                rows="4"
                class="flex w-full rounded-lg border border-input bg-background px-4 py-3 text-sm ring-offset-background placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-primary/20 focus-visible:border-primary transition-colors resize-none"
                placeholder="Décrivez les détails de votre tâche..."
              ></textarea>
              <div class="absolute bottom-2 right-2 text-xs text-muted-foreground">
                {{ form.description.length }}/500
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Task Settings Section -->
      <div class="bg-background border border-border rounded-lg p-6">
        <div class="space-y-2 mb-4">
          <h2 class="text-lg font-semibold text-foreground flex items-center gap-2">
            <svg class="w-5 h-5 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
            </svg>
            Paramètres de la tâche
          </h2>
          <p class="text-sm text-muted-foreground">Configurez la priorité, le statut et la date d'échéance</p>
        </div>

        <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
          <!-- Priority Field -->
          <div class="space-y-2">
            <label class="text-sm font-medium text-foreground flex items-center gap-2">
              <span class="text-destructive">*</span>
              Priorité
            </label>
            <div class="relative">
              <select
                v-model="form.priority"
                class="appearance-none w-full rounded-lg border border-input bg-background px-4 py-3 pr-10 text-sm focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary transition-colors"
                :class="{ 'border-destructive': errors.priority }"
              >
                <option value="">Sélectionner une priorité</option>
                <option value="low">🟢 Basse</option>
                <option value="medium">🟡 Moyenne</option>
                <option value="high">🔴 Haute</option>
              </select>
              <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
                <svg class="w-4 h-4 text-muted-foreground" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                </svg>
              </div>
            </div>
            <div v-if="errors.priority" class="flex items-center gap-2 text-sm text-destructive">
              <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
              </svg>
              {{ errors.priority }}
            </div>
          </div>

          <!-- Status Field -->
          <div class="space-y-2">
            <label class="text-sm font-medium text-foreground flex items-center gap-2">
              Statut
            </label>
            <div class="relative">
              <select
                v-model="form.status"
                class="appearance-none w-full rounded-lg border border-input bg-background px-4 py-3 pr-10 text-sm focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary transition-colors"
              >
                <option value="pending">⏳ En attente</option>
                <option value="in_progress">🔄 En cours</option>
                <option value="completed">✅ Terminé</option>
                <option value="cancelled">❌ Annulé</option>
              </select>
              <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
                <svg class="w-4 h-4 text-muted-foreground" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                </svg>
              </div>
            </div>
          </div>

          <!-- Due Date Field -->
          <div class="space-y-2">
            <label class="text-sm font-medium text-foreground flex items-center gap-2">
              <svg class="w-4 h-4 text-muted-foreground" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
              </svg>
              Date d'échéance
            </label>
            <div class="relative">
              <BaseInput
                v-model="form.due_date"
                type="date"
                :error="errors.due_date"
                class="pl-10"
              />
              <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                <svg class="w-5 h-5 text-muted-foreground" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                </svg>
              </div>
            </div>
            <div v-if="errors.due_date" class="flex items-center gap-2 text-sm text-destructive">
              <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
              </svg>
              {{ errors.due_date }}
            </div>
          </div>
        </div>
      </div>

      <!-- Task Preview Section -->
      <div v-if="form.title || form.description" class="bg-background border border-border rounded-lg p-6">
        <div class="space-y-2 mb-4">
          <h2 class="text-lg font-semibold text-foreground flex items-center gap-2">
            <svg class="w-5 h-5 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
            </svg>
            Aperçu de la tâche
          </h2>
          <p class="text-sm text-muted-foreground">Voici comment votre tâche apparaîtra</p>
        </div>

        <div class="bg-muted/30 rounded-lg p-4 space-y-3">
          <div class="flex items-start justify-between">
            <h3 class="font-medium text-foreground">
              {{ form.title || 'Titre de la tâche' }}
            </h3>
            <div class="flex items-center gap-2">
              <BaseBadge v-if="form.priority" :variant="getPriorityVariant(form.priority)" size="sm">
                {{ getPriorityLabel(form.priority) }}
              </BaseBadge>
              <BaseBadge v-if="form.status" :variant="getStatusVariant(form.status)" size="sm">
                {{ getStatusLabel(form.status) }}
              </BaseBadge>
            </div>
          </div>
          <p v-if="form.description" class="text-sm text-muted-foreground">
            {{ form.description }}
          </p>
          <div v-if="form.due_date" class="flex items-center gap-2 text-xs text-muted-foreground">
            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
            </svg>
            <span>Échéance: {{ formatDate(form.due_date) }}</span>
          </div>
        </div>
      </div>

      <!-- Action Buttons -->
      <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 pt-6 border-t border-border">
        <div class="flex items-center gap-2 text-sm text-muted-foreground">
          <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
          </svg>
          <span>Les champs marqués d'un * sont obligatoires</span>
        </div>
        
        <div class="flex items-center gap-3">
          <BaseButton
            type="button"
            variant="outline"
            @click="$emit('cancel')"
            class="flex items-center gap-2"
          >
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
            </svg>
            Annuler
          </BaseButton>
          <BaseButton
            type="submit"
            :loading="loading"
            :disabled="loading"
            class="flex items-center gap-2"
          >
            <svg v-if="!loading" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
            </svg>
            {{ task ? 'Mettre à jour' : 'Créer la tâche' }}
          </BaseButton>
        </div>
      </div>
    </form>
  </div>
</template>

<script setup>
import { reactive, watch, computed } from 'vue'
import { BaseInput, BaseButton, BaseBadge } from '@/components'

const props = defineProps({
  task: {
    type: Object,
    default: null
  },
  loading: {
    type: Boolean,
    default: false
  }
})

const emit = defineEmits(['submit', 'cancel'])

const form = reactive({
  title: '',
  description: '',
  priority: '',
  status: 'pending',
  due_date: ''
})

const errors = reactive({
  title: '',
  priority: '',
  due_date: ''
})

// Populate form when editing
watch(() => props.task, (newTask) => {
  if (newTask) {
    form.title = newTask.title || ''
    form.description = newTask.description || ''
    form.priority = newTask.priority || ''
    form.status = newTask.status || 'pending'
    form.due_date = newTask.due_date ? newTask.due_date.split('T')[0] : ''
  }
}, { immediate: true })

// Computed properties for preview
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

const getStatusVariant = (status) => {
  const variants = {
    pending: 'secondary',
    in_progress: 'default',
    completed: 'outline',
    cancelled: 'destructive'
  }
  return variants[status] || 'secondary'
}

const getStatusLabel = (status) => {
  const labels = {
    pending: 'En attente',
    in_progress: 'En cours',
    completed: 'Terminé',
    cancelled: 'Annulé'
  }
  return labels[status] || status
}

const formatDate = (date) => {
  if (!date) return ''
  return new Date(date).toLocaleDateString('fr-FR', {
    day: '2-digit',
    month: '2-digit',
    year: 'numeric'
  })
}

const validateForm = () => {
  errors.title = ''
  errors.priority = ''
  errors.due_date = ''
  
  if (!form.title.trim()) {
    errors.title = 'Le titre est requis'
  }
  
  if (!form.priority) {
    errors.priority = 'La priorité est requise'
  }
  
  if (form.due_date && new Date(form.due_date) < new Date()) {
    errors.due_date = 'La date d\'échéance ne peut pas être dans le passé'
  }
  
  return !errors.title && !errors.priority && !errors.due_date
}

const handleSubmit = () => {
  if (validateForm()) {
    const taskData = {
      title: form.title.trim(),
      description: form.description.trim(),
      priority: form.priority,
      status: form.status,
      due_date: form.due_date || null
    }
    
    emit('submit', taskData)
  }
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

/* Character counter animation */
textarea:focus + div {
  color: hsl(var(--primary));
  transition: color 0.2s ease;
}
</style> 