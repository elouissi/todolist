import { defineStore } from 'pinia'
import { ref, computed } from 'vue'
import { useToast } from 'vue-toastification'
import api from '@/services/api'

export const useTasksStore = defineStore('tasks', () => {
  const tasks = ref([])
  const currentTask = ref(null)
  const loading = ref(false)
  const statistics = ref({
    total: 0,
    pending: 0,
    in_progress: 0,
    completed: 0,
    canceled: 0,
    overdue: 0
  })

  const toast = useToast()

  // Computed properties
  const pendingTasks = computed(() => tasks.value.filter(task => task.status === 'pending'))
  const inProgressTasks = computed(() => tasks.value.filter(task => task.status === 'in_progress'))
  const completedTasks = computed(() => tasks.value.filter(task => task.status === 'completed'))
  const canceledTasks = computed(() => tasks.value.filter(task => task.status === 'canceled'))
  const overdueTasks = computed(() => tasks.value.filter(task => {
    if (task.status === 'completed') return false
    return task.due_date && new Date(task.due_date) < new Date()
  }))

  // Fetch all tasks
  const fetchTasks = async () => {
    loading.value = true
    try {
      const response = await api.get('/tasks')
      tasks.value = response.data.data.data || response.data.data
    } catch (error) {
      toast.error('Erreur lors du chargement des tâches')
    } finally {
      loading.value = false
    }
  }

  // Fetch task by ID
  const fetchTask = async (id) => {
    loading.value = true
    try {
      const response = await api.get(`/tasks/${id}`)
      currentTask.value = response.data.data
      return response.data.data
    } catch (error) {
      toast.error('Erreur lors du chargement de la tâche')
      return null
    } finally {
      loading.value = false
    }
  }

  // Create task
  const createTask = async (taskData) => {
    loading.value = true
    try {
      const response = await api.post('/tasks', taskData)
      const newTask = response.data.data
      tasks.value.unshift(newTask)
      toast.success(response.data.message)
      
      // Update statistics after creating a task
      await fetchStatistics()
      
      return { success: true, task: newTask }
    } catch (error) {
      const message = error.response?.data?.message || 'Erreur lors de la création de la tâche'
      toast.error(message)
      return { success: false, message }
    } finally {
      loading.value = false
    }
  }

  // Update task
  const updateTask = async (id, taskData) => {
    loading.value = true
    try {
      const response = await api.put(`/tasks/${id}`, taskData)
      const updatedTask = response.data.data
      
      const index = tasks.value.findIndex(task => task.id === id)
      if (index !== -1) {
        tasks.value[index] = updatedTask
      }
      
      if (currentTask.value && currentTask.value.id === id) {
        currentTask.value = updatedTask
      }
      
      toast.success(response.data.message)
      return { success: true, task: updatedTask }
    } catch (error) {
      const message = error.response?.data?.message || 'Erreur lors de la mise à jour de la tâche'
      toast.error(message)
      return { success: false, message }
    } finally {
      loading.value = false
    }
  }

  // Delete task
  const deleteTask = async (id) => {
    loading.value = true
    try {
      await api.delete(`/tasks/${id}`)
      tasks.value = tasks.value.filter(task => task.id !== id)
      
      if (currentTask.value && currentTask.value.id === id) {
        currentTask.value = null
      }
      
      toast.success('Tâche supprimée avec succès')
      return { success: true }
    } catch (error) {
      const message = error.response?.data?.message || 'Erreur lors de la suppression de la tâche'
      toast.error(message)
      return { success: false, message }
    } finally {
      loading.value = false
    }
  }

  // Update task status
  const updateTaskStatus = async (id, status) => {
    try {
      const response = await api.patch(`/tasks/${id}/status`, { status })
      const updatedTask = response.data.data
      
      const index = tasks.value.findIndex(task => task.id === id)
      if (index !== -1) {
        tasks.value[index] = updatedTask
      }
      
      if (currentTask.value && currentTask.value.id === id) {
        currentTask.value = updatedTask
      }
      
      toast.success(response.data.message)
      return { success: true, task: updatedTask }
    } catch (error) {
      const message = error.response?.data?.message || 'Erreur lors de la mise à jour du statut'
      toast.error(message)
      return { success: false, message }
    }
  }

  // Fetch tasks by status
  const fetchTasksByStatus = async (status) => {
    loading.value = true
    try {
      const response = await api.get(`/tasks/status/${status}`)
      tasks.value = response.data.data
    } catch (error) {
      toast.error('Erreur lors du chargement des tâches')
    } finally {
      loading.value = false
    }
  }

  // Fetch overdue tasks
  const fetchOverdueTasks = async () => {
    loading.value = true
    try {
      const response = await api.get('/tasks/overdue')
      tasks.value = response.data.data
    } catch (error) {
      toast.error('Erreur lors du chargement des tâches en retard')
    } finally {
      loading.value = false
    }
  }

  // Fetch statistics
  const fetchStatistics = async () => {
    try {
      const response = await api.get('/tasks/statistics')
      // Handle both response structures: { data: stats } and { stats }
      statistics.value = response.data.data || response.data
    } catch (error) {
      console.error('Erreur lors du chargement des statistiques:', error)
    }
  }

  // Add task from websocket
  const addTaskFromWebSocket = (task) => {
    tasks.value.unshift(task)
  }

  // Update task from websocket
  const updateTaskFromWebSocket = (task) => {
    const index = tasks.value.findIndex(t => t.id === task.id)
    if (index !== -1) {
      tasks.value[index] = task
    }
    
    if (currentTask.value && currentTask.value.id === task.id) {
      currentTask.value = task
    }
  }

  // Remove task from websocket
  const removeTaskFromWebSocket = (taskId) => {
    tasks.value = tasks.value.filter(task => task.id !== taskId)
    
    if (currentTask.value && currentTask.value.id === taskId) {
      currentTask.value = null
    }
  }

  return {
    // State
    tasks,
    currentTask,
    loading,
    statistics,
    
    // Computed
    pendingTasks,
    inProgressTasks,
    completedTasks,
    canceledTasks,
    overdueTasks,
    
    // Actions
    fetchTasks,
    fetchTask,
    createTask,
    updateTask,
    deleteTask,
    updateTaskStatus,
    fetchTasksByStatus,
    fetchOverdueTasks,
    fetchStatistics,
    addTaskFromWebSocket,
    updateTaskFromWebSocket,
    removeTaskFromWebSocket
  }
}) 