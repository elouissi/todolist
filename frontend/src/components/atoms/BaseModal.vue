<template>
  <Teleport to="body">
    <div
      v-if="modelValue"
      class="fixed inset-0 z-50 flex items-center justify-center p-4"
      @click="handleBackdropClick"
    >
      <!-- Backdrop -->
      <div class="fixed inset-0 bg-black/50 backdrop-blur-sm" />
      
      <!-- Modal -->
      <div
        :class="[
          'relative bg-background rounded-lg shadow-lg w-full mx-auto max-h-[90vh] overflow-y-auto',
          size === 'sm' ? 'max-w-md' : '',
          size === 'md' ? 'max-w-2xl' : '',
          size === 'lg' ? 'max-w-4xl' : '',
          size === 'xl' ? 'max-w-6xl' : '',
          !size ? 'max-w-2xl' : ''
        ]"
        @click.stop
      >
        <!-- Header -->
        <div v-if="$slots.header || title" class="flex items-center justify-between p-6 border-b">
          <div v-if="title" class="text-lg font-semibold">
            {{ title }}
          </div>
          <slot name="header" />
          <button
            v-if="showCloseButton"
            @click="$emit('update:modelValue', false)"
            class="text-muted-foreground hover:text-foreground transition-colors"
          >
            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
            </svg>
          </button>
        </div>
        
        <!-- Content -->
        <div class="p-6">
          <slot />
        </div>
        
        <!-- Footer -->
        <div v-if="$slots.footer" class="flex items-center justify-end space-x-2 p-6 border-t">
          <slot name="footer" />
        </div>
      </div>
    </div>
  </Teleport>
</template>

<script setup>
const props = defineProps({
  modelValue: {
    type: Boolean,
    default: false
  },
  title: {
    type: String,
    default: ''
  },
  showCloseButton: {
    type: Boolean,
    default: true
  },
  closeOnBackdrop: {
    type: Boolean,
    default: true
  },
  size: {
    type: String,
    default: 'md',
    validator: (value) => ['sm', 'md', 'lg', 'xl'].includes(value)
  }
})

const emit = defineEmits(['update:modelValue'])

const handleBackdropClick = () => {
  if (props.closeOnBackdrop) {
    emit('update:modelValue', false)
  }
}
</script> 