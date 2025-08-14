<template>
  <div class="min-h-screen bg-background">
    <!-- Navigation -->
    <AppNavigation
      :notification-count="3"
      @create-task="handleCreateTask"
    />
    
    <div class="container mx-auto px-4 py-8">
      <h1 class="text-3xl font-bold mb-8">Démonstration des Composants Atomiques</h1>
      
      <!-- Atoms Section -->
      <section class="mb-12">
        <h2 class="text-2xl font-semibold mb-6">🧪 Atoms</h2>
        
        <!-- Buttons -->
        <BaseCard class="mb-8">
          <template #header>
            <h3 class="text-lg font-medium">BaseButton</h3>
          </template>
          
          <div class="space-y-4">
            <div class="flex flex-wrap gap-4">
              <BaseButton variant="default">Default</BaseButton>
              <BaseButton variant="secondary">Secondary</BaseButton>
              <BaseButton variant="destructive">Destructive</BaseButton>
              <BaseButton variant="outline">Outline</BaseButton>
              <BaseButton variant="ghost">Ghost</BaseButton>
              <BaseButton variant="link">Link</BaseButton>
            </div>
            
            <div class="flex flex-wrap gap-4">
              <BaseButton size="sm">Small</BaseButton>
              <BaseButton size="default">Default</BaseButton>
              <BaseButton size="lg">Large</BaseButton>
              <BaseButton size="icon">🚀</BaseButton>
            </div>
            
            <div class="flex flex-wrap gap-4">
              <BaseButton :loading="true">Loading</BaseButton>
              <BaseButton :disabled="true">Disabled</BaseButton>
            </div>
          </div>
        </BaseCard>
        
        <!-- Inputs -->
        <BaseCard class="mb-8">
          <template #header>
            <h3 class="text-lg font-medium">BaseInput</h3>
          </template>
          
          <div class="space-y-4">
            <FormField label="Email" required>
              <BaseInput
                v-model="demoForm.email"
                type="email"
                placeholder="votre@email.com"
              />
            </FormField>
            
            <FormField label="Mot de passe" required>
              <BaseInput
                v-model="demoForm.password"
                type="password"
                placeholder="Votre mot de passe"
              />
            </FormField>
            
            <FormField label="Avec erreur">
              <BaseInput
                v-model="demoForm.errorField"
                placeholder="Champ avec erreur"
                error="Ce champ contient une erreur"
              />
            </FormField>
          </div>
        </BaseCard>
        
        <!-- Badges -->
        <BaseCard class="mb-8">
          <template #header>
            <h3 class="text-lg font-medium">BaseBadge</h3>
          </template>
          
          <div class="space-y-4">
            <div class="flex flex-wrap gap-4">
              <BaseBadge variant="default">Default</BaseBadge>
              <BaseBadge variant="secondary">Secondary</BaseBadge>
              <BaseBadge variant="destructive">Destructive</BaseBadge>
              <BaseBadge variant="outline">Outline</BaseBadge>
            </div>
            
            <div class="flex flex-wrap gap-4">
              <BaseBadge size="sm">Small</BaseBadge>
              <BaseBadge size="default">Default</BaseBadge>
              <BaseBadge size="lg">Large</BaseBadge>
            </div>
          </div>
        </BaseCard>
        
        <!-- Spinner -->
        <BaseCard class="mb-8">
          <template #header>
            <h3 class="text-lg font-medium">BaseSpinner</h3>
          </template>
          
          <div class="flex flex-wrap gap-4 items-center">
            <BaseSpinner size="16" />
            <BaseSpinner size="24" />
            <BaseSpinner size="32" />
            <BaseSpinner size="48" />
          </div>
        </BaseCard>
        
        <!-- Alert -->
        <BaseCard class="mb-8">
          <template #header>
            <h3 class="text-lg font-medium">BaseAlert</h3>
          </template>
          
          <div class="space-y-4">
            <BaseAlert
              v-model="showAlert"
              variant="default"
              title="Information"
              dismissible
            >
              Ceci est une alerte d'information.
            </BaseAlert>
            
            <BaseAlert
              variant="success"
              title="Succès"
            >
              Opération réussie !
            </BaseAlert>
            
            <BaseAlert
              variant="warning"
              title="Attention"
            >
              Attention, cette action est irréversible.
            </BaseAlert>
            
            <BaseAlert
              variant="destructive"
              title="Erreur"
            >
              Une erreur s'est produite.
            </BaseAlert>
          </div>
        </BaseCard>
      </section>
      
      <!-- Molecules Section -->
      <section class="mb-12">
        <h2 class="text-2xl font-semibold mb-6">🧬 Molecules</h2>
        
        <!-- FormField -->
        <BaseCard class="mb-8">
          <template #header>
            <h3 class="text-lg font-medium">FormField</h3>
          </template>
          
          <div class="space-y-4">
            <FormField
              label="Nom complet"
              description="Votre nom et prénom"
              required
            >
              <BaseInput
                v-model="demoForm.fullName"
                placeholder="Jean Dupont"
              />
            </FormField>
            
            <FormField
              label="Description"
              description="Une description optionnelle"
            >
              <textarea
                v-model="demoForm.description"
                rows="3"
                class="flex w-full rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2"
                placeholder="Votre description..."
              ></textarea>
            </FormField>
          </div>
        </BaseCard>
        
        <!-- TaskCard -->
        <BaseCard class="mb-8">
          <template #header>
            <h3 class="text-lg font-medium">TaskCard</h3>
          </template>
          
          <div class="grid gap-4 md:grid-cols-2">
            <TaskCard
              :task="demoTask"
              @click="handleTaskClick"
              @view="handleTaskClick"
              @edit="handleTaskEdit"
              @delete="handleTaskDelete"
            />
          </div>
        </BaseCard>
      </section>
      
      <!-- Organisms Section -->
      <section class="mb-12">
        <h2 class="text-2xl font-semibold mb-6">🦠 Organisms</h2>
        
        <!-- TaskList -->
        <BaseCard class="mb-8">
          <template #header>
            <h3 class="text-lg font-medium">TaskList</h3>
          </template>
          
          <TaskList
            :tasks="demoTasks"
            :loading="false"
            :current-page="1"
            :total-pages="1"
            @create="handleCreateTask"
            @view="handleViewTask"
            @edit="handleEditTask"
            @delete="handleDeleteTask"
            @page-change="handlePageChange"
          />
        </BaseCard>
      </section>
      
      <!-- Modal Demo -->
      <BaseCard class="mb-8">
        <template #header>
          <h3 class="text-lg font-medium">BaseModal</h3>
        </template>
        
        <div class="space-y-4">
          <BaseButton @click="showModal = true">
            Ouvrir la modal
          </BaseButton>
          
          <BaseModal
            v-model="showModal"
            title="Démonstration Modal"
          >
            <p class="mb-4">Ceci est une démonstration du composant BaseModal.</p>
            <p>Vous pouvez fermer cette modal en cliquant sur le bouton X ou en cliquant à l'extérieur.</p>
            
            <template #footer>
              <BaseButton variant="outline" @click="showModal = false">
                Fermer
              </BaseButton>
              <BaseButton @click="showModal = false">
                Confirmer
              </BaseButton>
            </template>
          </BaseModal>
        </div>
      </BaseCard>
    </div>
  </div>
</template>

<script setup>
import { ref, reactive } from 'vue'
import { useToast } from 'vue-toastification'
import {
  BaseButton,
  BaseInput,
  BaseCard,
  BaseBadge,
  BaseModal,
  BaseSpinner,
  BaseAlert,
  FormField,
  TaskCard,
  TaskList,
  AppNavigation
} from '@/components'

const toast = useToast()

const demoForm = reactive({
  email: '',
  password: '',
  errorField: 'Valeur avec erreur',
  fullName: '',
  description: ''
})

const showAlert = ref(true)
const showModal = ref(false)

const demoTask = {
  id: 1,
  title: 'Tâche de démonstration',
  description: 'Cette tâche sert à démontrer le composant TaskCard avec tous ses éléments.',
  status: 'in_progress',
  priority: 'high',
  due_date: '2024-12-31',
  user: {
    full_name: 'Jean Dupont'
  }
}

const demoTasks = [demoTask]

const handleTaskClick = () => {
  toast.info('Clic sur la tâche')
}

const handleTaskEdit = () => {
  toast.info('Édition de la tâche')
}

const handleTaskDelete = () => {
  toast.info('Suppression de la tâche')
}

const handleCreateTask = () => {
  toast.info('Création d\'une nouvelle tâche')
}

const handleViewTask = () => {
  toast.info('Affichage de la tâche')
}

const handleEditTask = () => {
  toast.info('Édition de la tâche')
}

const handleDeleteTask = () => {
  toast.info('Suppression de la tâche')
}

const handlePageChange = (page) => {
  toast.info(`Changement de page vers ${page}`)
}
</script> 