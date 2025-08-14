<template>
  <form @submit.prevent="handleSubmit" class="space-y-6">
    <FormField label="Email" required>
      <BaseInput
        v-model="form.email"
        type="email"
        placeholder="votre@email.com"
        :error="errors.email"
        required
      />
    </FormField>
    
    <FormField label="Mot de passe" required>
      <BaseInput
        v-model="form.password"
        type="password"
        placeholder="Votre mot de passe"
        :error="errors.password"
        required
      />
    </FormField>
    
    <div class="flex items-center justify-between">
      <label class="flex items-center space-x-2">
        <input
          v-model="form.remember"
          type="checkbox"
          class="rounded border-gray-300 text-primary focus:ring-primary"
        />
        <span class="text-sm text-muted-foreground">Se souvenir de moi</span>
      </label>
    </div>
    
    <BaseButton
      type="submit"
      :loading="loading"
      :disabled="loading"
      class="w-full"
    >
      Se connecter
    </BaseButton>
  </form>
</template>

<script setup>
import { ref, reactive } from 'vue'
import FormField from '@/components/molecules/FormField.vue'
import BaseInput from '@/components/atoms/BaseInput.vue'
import BaseButton from '@/components/atoms/BaseButton.vue'

const props = defineProps({
  loading: {
    type: Boolean,
    default: false
  }
})

const emit = defineEmits(['submit'])

const form = reactive({
  email: '',
  password: '',
  remember: false
})

const errors = reactive({
  email: '',
  password: ''
})

const validateForm = () => {
  errors.email = ''
  errors.password = ''
  
  if (!form.email) {
    errors.email = 'L\'email est requis'
  } else if (!/\S+@\S+\.\S+/.test(form.email)) {
    errors.email = 'L\'email n\'est pas valide'
  }
  
  if (!form.password) {
    errors.password = 'Le mot de passe est requis'
  } else if (form.password.length < 6) {
    errors.password = 'Le mot de passe doit contenir au moins 6 caractères'
  }
  
  return !errors.email && !errors.password
}

const handleSubmit = () => {
  if (validateForm()) {
    emit('submit', {
      email: form.email,
      password: form.password,
      remember: form.remember
    })
  }
}
</script> 