<template>
  <div class="w-full max-w-4xl mx-auto">
    <div class="bg-white/80 backdrop-blur-sm rounded-2xl shadow-xl border border-white/20 p-8">
      <div class="text-center mb-8">
        <h2 class="text-2xl font-bold bg-gradient-to-r from-primary to-primary/70 bg-clip-text text-transparent">
          Créer votre compte
        </h2>
        <p class="text-muted-foreground mt-2">Rejoignez notre communauté</p>
      </div>

      <form @submit.prevent="handleSubmit" class="space-y-8">
        <!-- Personal Information Section -->
        <div class="space-y-6">
          <div class="flex items-center gap-2 mb-6">
            <div class="w-1 h-6 bg-gradient-to-b from-primary to-primary/60 rounded-full"></div>
            <h3 class="font-semibold text-foreground">Informations personnelles</h3>
          </div>
          
          <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <FormField label="Nom complet" required>
              <BaseInput
                v-model="form.full_name"
                type="text"
                placeholder="Votre nom complet"
                :error="errors.full_name"
                required
                class="transition-all duration-200 hover:shadow-md focus:shadow-lg"
              />
            </FormField>
            
            <FormField label="Adresse email" required>
              <BaseInput
                v-model="form.email"
                type="email"
                placeholder="votre@email.com"
                :error="errors.email"
                required
                class="transition-all duration-200 hover:shadow-md focus:shadow-lg"
              />
            </FormField>
            
            <FormField label="Numéro de téléphone">
              <BaseInput
                v-model="form.phone_number"
                type="tel"
                placeholder="+33 6 12 34 56 78"
                :error="errors.phone_number"
                class="transition-all duration-200 hover:shadow-md focus:shadow-lg"
              />
            </FormField>
            
            <FormField label="Photo de profil">
              <div class="space-y-3">
                <div class="relative">
                  <input
                    ref="imageInput"
                    type="file"
                    accept="image/*"
                    @change="handleImageChange"
                    class="absolute inset-0 w-full h-full opacity-0 cursor-pointer"
                  />
                  <div class="border-2 border-dashed border-muted-foreground/30 rounded-xl p-4 text-center hover:border-primary/50 transition-colors duration-200">
                    <div class="flex flex-col items-center gap-2">
                      <svg class="w-6 h-6 text-muted-foreground" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12" />
                      </svg>
                      <div>
                        <p class="text-sm font-medium text-foreground">Sélectionner une photo</p>
                        <p class="text-xs text-muted-foreground">PNG, JPG jusqu'à 5MB</p>
                      </div>
                    </div>
                  </div>
                </div>
                
                <div v-if="imagePreview" class="flex items-center gap-3 p-3 bg-muted/30 rounded-xl">
                  <img :src="imagePreview" alt="Preview" class="h-12 w-12 rounded-full object-cover border-2 border-primary/20 shadow-md" />
                  <div class="flex-1">
                    <p class="text-sm font-medium text-foreground">Image sélectionnée</p>
                    <p class="text-xs text-muted-foreground">Cliquez à nouveau pour changer</p>
                  </div>
                  <button
                    type="button"
                    @click="removeImage"
                    class="p-1 text-muted-foreground hover:text-destructive transition-colors duration-200"
                  >
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                  </button>
                </div>
              </div>
            </FormField>
          </div>
          
          <FormField label="Adresse">
            <textarea
              v-model="form.address"
              rows="3"
              class="flex w-full rounded-xl border border-input bg-background/50 px-4 py-3 text-sm ring-offset-background placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-primary/20 focus-visible:ring-offset-2 transition-all duration-200 hover:shadow-md focus:shadow-lg resize-none"
              :class="{ 'border-destructive focus-visible:ring-destructive/20': errors.address }"
              placeholder="Votre adresse complète"
            ></textarea>
            <div v-if="errors.address" class="mt-2 text-sm text-destructive flex items-center gap-1">
              <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
              </svg>
              {{ errors.address }}
            </div>
          </FormField>
        </div>

        <!-- Security Section -->
        <div class="space-y-6">
          <div class="flex items-center gap-2 mb-6">
            <div class="w-1 h-6 bg-gradient-to-b from-primary to-primary/60 rounded-full"></div>
            <h3 class="font-semibold text-foreground">Sécurité</h3>
          </div>
          
          <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <FormField label="Mot de passe" required>
              <div class="relative">
                <BaseInput
                  v-model="form.password"
                  :type="showPassword ? 'text' : 'password'"
                  placeholder="Mot de passe"
                  :error="errors.password"
                  required
                  class="transition-all duration-200 hover:shadow-md focus:shadow-lg pr-12"
                />
                <button
                  type="button"
                  @click="showPassword = !showPassword"
                  class="absolute right-3 top-1/2 -translate-y-1/2 text-muted-foreground hover:text-foreground transition-colors duration-200"
                >
                  <svg v-if="showPassword" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.878 9.878L3 3m6.878 6.878L21 21" />
                  </svg>
                  <svg v-else class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                  </svg>
                </button>
              </div>
              
              <!-- Password Strength Indicator -->
              <div v-if="form.password" class="mt-3 space-y-2">
                <div class="flex items-center gap-2">
                  <div class="flex-1 h-2 bg-muted rounded-full overflow-hidden">
                    <div 
                      class="h-full transition-all duration-300 rounded-full"
                      :class="passwordStrength.color"
                      :style="{ width: passwordStrength.percentage + '%' }"
                    ></div>
                  </div>
                  <span class="text-xs font-medium" :class="passwordStrength.color">
                    {{ passwordStrength.label }}
                  </span>
                </div>
                <div class="grid grid-cols-2 gap-2 text-xs text-muted-foreground">
                  <div class="flex items-center gap-1">
                    <div class="w-1.5 h-1.5 rounded-full" :class="passwordChecks.length ? 'bg-green-500' : 'bg-muted-foreground'"></div>
                    Au moins 8 caractères
                  </div>
                  <div class="flex items-center gap-1">
                    <div class="w-1.5 h-1.5 rounded-full" :class="passwordChecks.uppercase ? 'bg-green-500' : 'bg-muted-foreground'"></div>
                    Une majuscule
                  </div>
                  <div class="flex items-center gap-1">
                    <div class="w-1.5 h-1.5 rounded-full" :class="passwordChecks.lowercase ? 'bg-green-500' : 'bg-muted-foreground'"></div>
                    Une minuscule
                  </div>
                  <div class="flex items-center gap-1">
                    <div class="w-1.5 h-1.5 rounded-full" :class="passwordChecks.number ? 'bg-green-500' : 'bg-muted-foreground'"></div>
                    Un chiffre
                  </div>
                </div>
              </div>
            </FormField>
            
            <FormField label="Confirmer le mot de passe" required>
              <div class="relative">
                <BaseInput
                  v-model="form.password_confirmation"
                  :type="showPasswordConfirmation ? 'text' : 'password'"
                  placeholder="Confirmer le mot de passe"
                  :error="errors.password_confirmation"
                  required
                  class="transition-all duration-200 hover:shadow-md focus:shadow-lg pr-12"
                />
                <button
                  type="button"
                  @click="showPasswordConfirmation = !showPasswordConfirmation"
                  class="absolute right-3 top-1/2 -translate-y-1/2 text-muted-foreground hover:text-foreground transition-colors duration-200"
                >
                  <svg v-if="showPasswordConfirmation" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.878 9.878L3 3m6.878 6.878L21 21" />
                  </svg>
                  <svg v-else class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                  </svg>
                </button>
              </div>
              
              <!-- Password Match Indicator -->
              <div v-if="form.password_confirmation" class="mt-2 flex items-center gap-2">
                <div class="w-1.5 h-1.5 rounded-full" :class="passwordsMatch ? 'bg-green-500' : 'bg-red-500'"></div>
                <span class="text-xs" :class="passwordsMatch ? 'text-green-600' : 'text-red-600'">
                  {{ passwordsMatch ? 'Les mots de passe correspondent' : 'Les mots de passe ne correspondent pas' }}
                </span>
              </div>
            </FormField>
          </div>
        </div>

        <!-- Submit Button -->
        <div class="pt-6">
          <BaseButton
            type="submit"
            :loading="loading"
            :disabled="loading || !isFormValid"
            class="w-full h-12 text-base font-medium rounded-xl bg-gradient-to-r from-primary to-primary/80 hover:from-primary/90 hover:to-primary/70 transition-all duration-200 shadow-lg hover:shadow-xl transform hover:-translate-y-0.5"
          >
            <div v-if="loading" class="flex items-center gap-2">
              <svg class="animate-spin w-5 h-5" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
              </svg>
              Création du compte...
            </div>
            <div v-else class="flex items-center gap-2">
              <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
              </svg>
              Créer le compte
            </div>
          </BaseButton>
        </div>
      </form>
    </div>
  </div>
</template>

<script setup>
import { ref, reactive, computed, watch } from 'vue'
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

const imageInput = ref(null)
const imagePreview = ref(null)
const showPassword = ref(false)
const showPasswordConfirmation = ref(false)

const form = reactive({
  full_name: '',
  email: '',
  phone_number: '',
  address: '',
  image: null,
  password: '',
  password_confirmation: ''
})

const errors = reactive({
  full_name: '',
  email: '',
  phone_number: '',
  address: '',
  password: '',
  password_confirmation: ''
})

// Password strength computation
const passwordChecks = computed(() => ({
  length: form.password.length >= 8,
  uppercase: /[A-Z]/.test(form.password),
  lowercase: /[a-z]/.test(form.password),
  number: /\d/.test(form.password)
}))

const passwordStrength = computed(() => {
  const checks = Object.values(passwordChecks.value)
  const passedChecks = checks.filter(Boolean).length
  
  if (passedChecks === 0) return { percentage: 0, label: 'Très faible', color: 'bg-red-500' }
  if (passedChecks === 1) return { percentage: 25, label: 'Faible', color: 'bg-orange-500' }
  if (passedChecks === 2) return { percentage: 50, label: 'Moyen', color: 'bg-yellow-500' }
  if (passedChecks === 3) return { percentage: 75, label: 'Bon', color: 'bg-blue-500' }
  return { percentage: 100, label: 'Excellent', color: 'bg-green-500' }
})

const passwordsMatch = computed(() => {
  return form.password && form.password_confirmation && form.password === form.password_confirmation
})

const isFormValid = computed(() => {
  return form.full_name.trim() && 
         form.email && 
         /\S+@\S+\.\S+/.test(form.email) &&
         form.password && 
         form.password.length >= 8 &&
         passwordsMatch.value
})

const handleImageChange = (event) => {
  const file = event.target.files[0]
  if (file) {
    // Validate file size (5MB limit)
    if (file.size > 5 * 1024 * 1024) {
      alert('Le fichier est trop volumineux. Taille maximum : 5MB')
      return
    }
    
    form.image = file
    const reader = new FileReader()
    reader.onload = (e) => {
      imagePreview.value = e.target.result
    }
    reader.readAsDataURL(file)
  }
}

const removeImage = () => {
  form.image = null
  imagePreview.value = null
  if (imageInput.value) {
    imageInput.value.value = ''
  }
}

const validateForm = () => {
  // Clear previous errors
  Object.keys(errors).forEach(key => errors[key] = '')
  
  let isValid = true
  
  if (!form.full_name.trim()) {
    errors.full_name = 'Le nom complet est requis'
    isValid = false
  }
  
  if (!form.email) {
    errors.email = 'L\'email est requis'
    isValid = false
  } else if (!/\S+@\S+\.\S+/.test(form.email)) {
    errors.email = 'L\'email n\'est pas valide'
    isValid = false
  }
  
  if (form.phone_number && !/^\+?[\d\s\-\(\)]+$/.test(form.phone_number)) {
    errors.phone_number = 'Le numéro de téléphone n\'est pas valide'
    isValid = false
  }
  
  if (!form.password) {
    errors.password = 'Le mot de passe est requis'
    isValid = false
  } else if (form.password.length < 8) {
    errors.password = 'Le mot de passe doit contenir au moins 8 caractères'
    isValid = false
  } else if (!passwordChecks.value.uppercase || !passwordChecks.value.lowercase || !passwordChecks.value.number) {
    errors.password = 'Le mot de passe doit contenir au moins une majuscule, une minuscule et un chiffre'
    isValid = false
  }
  
  if (!form.password_confirmation) {
    errors.password_confirmation = 'La confirmation du mot de passe est requise'
    isValid = false
  } else if (!passwordsMatch.value) {
    errors.password_confirmation = 'Les mots de passe ne correspondent pas'
    isValid = false
  }
  
  return isValid
}

const handleSubmit = () => {
  if (validateForm()) {
    emit('submit', { ...form })
  }
}

// Auto-validate on input changes
watch(() => form.password, () => {
  if (errors.password) validateForm()
})

watch(() => form.password_confirmation, () => {
  if (errors.password_confirmation) validateForm()
})
</script> 