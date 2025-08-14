<template>
  <div
    v-if="modelValue"
    :class="alertClasses"
    role="alert"
  >
    <div class="flex items-start space-x-3">
      <!-- Icon -->
      <div v-if="icon" class="flex-shrink-0 mt-0.5">
        <component :is="icon" class="h-4 w-4" />
      </div>
      
      <!-- Content -->
      <div class="flex-1">
        <div v-if="title" class="font-medium">
          {{ title }}
        </div>
        <div v-if="$slots.default" class="mt-1 text-sm">
          <slot />
        </div>
      </div>
      
      <!-- Close button -->
      <button
        v-if="dismissible"
        @click="$emit('update:modelValue', false)"
        class="flex-shrink-0 ml-auto -mr-1 h-4 w-4 rounded-full inline-flex items-center justify-center text-muted-foreground hover:text-foreground focus:outline-none focus:ring-2 focus:ring-ring focus:ring-offset-2"
      >
        <svg class="h-3 w-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
        </svg>
      </button>
    </div>
  </div>
</template>

<script setup>
import { computed } from 'vue'
import { cn } from '@/lib/utils'

const props = defineProps({
  modelValue: {
    type: Boolean,
    default: true
  },
  variant: {
    type: String,
    default: 'default',
    validator: (value) => ['default', 'destructive', 'warning', 'success', 'info'].includes(value)
  },
  title: {
    type: String,
    default: ''
  },
  dismissible: {
    type: Boolean,
    default: false
  },
  class: {
    type: String,
    default: ''
  }
})

const emit = defineEmits(['update:modelValue'])

const alertClasses = computed(() => {
  const baseClasses = "rounded-lg border p-4"
  
  const variantClasses = {
    default: "bg-background text-foreground border-border",
    destructive: "bg-destructive/10 text-destructive border-destructive/20",
    warning: "bg-yellow-50 text-yellow-800 border-yellow-200",
    success: "bg-green-50 text-green-800 border-green-200",
    info: "bg-blue-50 text-blue-800 border-blue-200"
  }
  
  return cn(baseClasses, variantClasses[props.variant], props.class)
})

const icon = computed(() => {
  const icons = {
    default: null,
    destructive: 'AlertTriangle',
    warning: 'AlertTriangle',
    success: 'CheckCircle',
    info: 'Info'
  }
  return icons[props.variant]
})
</script> 