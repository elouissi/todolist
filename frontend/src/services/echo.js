import Echo from 'laravel-echo'
import Pusher from 'pusher-js'
import { useAuthStore } from '@/stores/auth'
import { useTasksStore } from '@/stores/tasks'
import { useToast } from 'vue-toastification'

// Configure Pusher
window.Pusher = Pusher

const echo = new Echo({
  broadcaster: 'pusher',
  key: import.meta.env.VITE_PUSHER_APP_KEY,
  cluster: import.meta.env.VITE_PUSHER_APP_CLUSTER,
  forceTLS: true,
  encrypted: true,
  authEndpoint: `${import.meta.env.VITE_API_URL}/broadcasting/auth`,
  auth: {
    headers: {
      Authorization: `Bearer ${localStorage.getItem('token')}`
    }
  }
})

// Initialize WebSocket listeners
export const initializeEcho = () => {
  const authStore = useAuthStore()
  const tasksStore = useTasksStore()
  const toast = useToast()

  if (!authStore.user) return

  // Listen to private user channel
  echo.private(`user.${authStore.user.id}`)
    .listen('task.created', (e) => {
      console.log('Task created:', e)
      tasksStore.addTaskFromWebSocket(e.task)
      toast.success(e.message)
    })
    .listen('task.updated', (e) => {
      console.log('Task updated:', e)
      tasksStore.updateTaskFromWebSocket(e.task)
      toast.success(e.message)
    })
    .listen('task.deleted', (e) => {
      console.log('Task deleted:', e)
      tasksStore.removeTaskFromWebSocket(e.task_id)
      toast.success(e.message)
    })
}

// Disconnect Echo
export const disconnectEcho = () => {
  if (echo) {
    echo.disconnect()
  }
}

export default echo 