import { defineStore } from 'pinia'
import { ref, computed } from 'vue'
import { useToast } from 'vue-toastification'
import api from '@/services/api'

export const useNotificationsStore = defineStore('notifications', () => {
  const notifications = ref([])
  const loading = ref(false)
  const currentPage = ref(1)
  const totalPages = ref(1)
  const unreadCount = ref(0)
  let refreshInterval = null

  const toast = useToast()

  // Computed properties
  const hasUnreadNotifications = computed(() => {
    return unreadCount.value > 0
  })

  // Fetch notifications
  const fetchNotifications = async (page = 1) => {
    loading.value = true
    try {
      const response = await api.get(`/notifications?page=${page}`)
      notifications.value = response.data.data
      currentPage.value = response.data.current_page
      totalPages.value = response.data.last_page
      
      // Update unread count based on fetched notifications
      unreadCount.value = notifications.value.filter(notification => !notification.read_at).length
    } catch (error) {
      toast.error('Erreur lors du chargement des notifications')
    } finally {
      loading.value = false
    }
  }

  // Fetch unread count only
  const fetchUnreadCount = async () => {
    try {
      const response = await api.get('/notifications/unread-count')
      unreadCount.value = response.data.count
      return response.data.count
    } catch (error) {
      console.error('Erreur lors du chargement du compteur de notifications:', error)
      return 0
    }
  }

  // Start auto-refresh
  const startAutoRefresh = () => {
    if (refreshInterval) {
      clearInterval(refreshInterval)
    }
    refreshInterval = setInterval(async () => {
      await fetchUnreadCount()
    }, 10000) // Refresh every 10 seconds instead of 30 for better responsiveness
  }

  // Stop auto-refresh
  const stopAutoRefresh = () => {
    if (refreshInterval) {
      clearInterval(refreshInterval)
      refreshInterval = null
    }
  }

  // Force refresh count (for immediate updates)
  const forceRefreshCount = async () => {
    await fetchUnreadCount()
  }

  // Refresh notifications and count
  const refreshAll = async () => {
    await Promise.all([
      fetchNotifications(1),
      fetchUnreadCount()
    ])
  }

  // Mark notification as read
  const markAsRead = async (id) => {
    try {
      await api.post(`/notifications/${id}/mark-as-read`)
      const notification = notifications.value.find(n => n.id === id)
      if (notification) {
        notification.read_at = new Date().toISOString()
        // Update unread count
        unreadCount.value = Math.max(0, unreadCount.value - 1)
      }
      toast.success('Notification marquée comme lue')
      // Immediately refresh count to ensure accuracy
      await fetchUnreadCount()
      return true
    } catch (error) {
      toast.error('Erreur lors de la mise à jour')
      return false
    }
  }

  // Mark all notifications as read
  const markAllAsRead = async () => {
    try {
      await api.post('/notifications/mark-all-as-read')
      notifications.value.forEach(notification => {
        notification.read_at = new Date().toISOString()
      })
      unreadCount.value = 0
      toast.success('Toutes les notifications ont été marquées comme lues')
      // Immediately refresh count to ensure accuracy
      await fetchUnreadCount()
      return true
    } catch (error) {
      toast.error('Erreur lors de la mise à jour')
      return false
    }
  }

  // Delete notification
  const deleteNotification = async (id) => {
    try {
      await api.delete(`/notifications/${id}`)
      const notification = notifications.value.find(n => n.id === id)
      if (notification && !notification.read_at) {
        // Update unread count if the deleted notification was unread
        unreadCount.value = Math.max(0, unreadCount.value - 1)
      }
      notifications.value = notifications.value.filter(n => n.id !== id)
      toast.success('Notification supprimée')
      // Immediately refresh count to ensure accuracy
      await fetchUnreadCount()
      return true
    } catch (error) {
      toast.error('Erreur lors de la suppression')
      return false
    }
  }

  // Clear all notifications
  const clearAllNotifications = async () => {
    if (confirm('Êtes-vous sûr de vouloir effacer toutes les notifications ?')) {
      try {
        await api.delete('/notifications/clear-all')
        notifications.value = []
        unreadCount.value = 0
        toast.success('Toutes les notifications ont été effacées')
        // Immediately refresh count to ensure accuracy
        await fetchUnreadCount()
        return true
      } catch (error) {
        toast.error('Erreur lors de la suppression')
        return false
      }
    }
    return false
  }

  // Add new notification (for real-time updates)
  const addNotification = (notification) => {
    notifications.value.unshift(notification)
    if (!notification.read_at) {
      unreadCount.value++
    }
  }

  // Remove notification (for real-time updates)
  const removeNotification = (id) => {
    const notification = notifications.value.find(n => n.id === id)
    if (notification && !notification.read_at) {
      unreadCount.value = Math.max(0, unreadCount.value - 1)
    }
    notifications.value = notifications.value.filter(n => n.id !== id)
  }

  // Update notification (for real-time updates)
  const updateNotification = (id, updates) => {
    const index = notifications.value.findIndex(n => n.id === id)
    if (index !== -1) {
      const oldNotification = notifications.value[index]
      const newNotification = { ...oldNotification, ...updates }
      
      // Update unread count if read status changed
      if (oldNotification.read_at !== newNotification.read_at) {
        if (newNotification.read_at) {
          unreadCount.value = Math.max(0, unreadCount.value - 1)
        } else {
          unreadCount.value++
        }
      }
      
      notifications.value[index] = newNotification
    }
  }

  return {
    // State
    notifications,
    loading,
    currentPage,
    totalPages,
    unreadCount,
    
    // Computed
    hasUnreadNotifications,
    
    // Actions
    fetchNotifications,
    fetchUnreadCount,
    startAutoRefresh,
    stopAutoRefresh,
    forceRefreshCount,
    refreshAll,
    markAsRead,
    markAllAsRead,
    deleteNotification,
    clearAllNotifications,
    addNotification,
    removeNotification,
    updateNotification
  }
}) 