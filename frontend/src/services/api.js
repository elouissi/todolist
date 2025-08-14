import axios from 'axios'
import { useAuthStore } from '@/stores/auth'
import { useToast } from 'vue-toastification'

// Create axios instance
const api = axios.create({
  baseURL: import.meta.env.VITE_API_URL || 'http://localhost:8000/api',
  timeout: 10000,
  headers: {
    'Content-Type': 'application/json',
    'Accept': 'application/json'
  }
})

// Flag to prevent infinite refresh loops
let isRefreshing = false
let failedQueue = []

const processQueue = (error, token = null) => {
  failedQueue.forEach(prom => {
    if (error) {
      prom.reject(error)
    } else {
      prom.resolve(token)
    }
  })
  
  failedQueue = []
}

// Request interceptor
api.interceptors.request.use(
  (config) => {
    // Always get the token from localStorage to ensure it's available after refresh
    const token = localStorage.getItem('token')
    if (token) {
      config.headers.Authorization = `Bearer ${token}`
    }
    return config
  },
  (error) => {
    return Promise.reject(error)
  }
)

// Response interceptor
api.interceptors.response.use(
  (response) => {
    return response
  },
  async (error) => {
    const originalRequest = error.config
    const authStore = useAuthStore()
    const toast = useToast()
    
    // Handle new response structure for errors
    const responseData = error.response?.data
    const backendCode = responseData?.code
    const backendMessage = responseData?.message || responseData?.error || 'Une erreur est survenue'
    
    // Handle 401 errors (Unauthorized)
    if (error.response?.status === 401) {
      // If the backend says TOKEN_EXPIRED, try refresh
      if (backendCode === 'TOKEN_EXPIRED') {
        if (!originalRequest._retry &&
            !originalRequest.url.includes('/auth/login') &&
            !originalRequest.url.includes('/auth/register') &&
            !originalRequest.url.includes('/auth/refresh')) {
          if (isRefreshing) {
            return new Promise((resolve, reject) => {
              failedQueue.push({ resolve, reject })
            }).then(token => {
              originalRequest.headers.Authorization = `Bearer ${token}`
              return api(originalRequest)
            }).catch(err => {
              return Promise.reject(err)
            })
          }
          originalRequest._retry = true
          isRefreshing = true
          try {
            const refreshResult = await authStore.refreshToken()
            if (refreshResult.success) {
              processQueue(null, authStore.token)
              originalRequest.headers.Authorization = `Bearer ${authStore.token}`
              return api(originalRequest)
            } else {
              processQueue(new Error('Refresh failed'), null)
              authStore.forceLogout()
              toast.error(backendMessage || 'Session expirée. Veuillez vous reconnecter.')
              if (window.location.pathname !== '/login') {
                window.location.href = '/login'
              }
              return Promise.reject(error)
            }
          } catch (refreshError) {
            processQueue(refreshError, null)
            authStore.forceLogout()
            toast.error(backendMessage || 'Session expirée. Veuillez vous reconnecter.')
            if (window.location.pathname !== '/login') {
              window.location.href = '/login'
            }
            return Promise.reject(error)
          } finally {
            isRefreshing = false
          }
        }
      } else {
        // For any other 401 error, clear token and redirect
        authStore.forceLogout()
        toast.error(backendMessage || 'Session expirée. Veuillez vous reconnecter.')
        if (window.location.pathname !== '/login') {
          window.location.href = '/login'
        }
        return Promise.reject(error)
      }
    }
    // Handle other errors
    if (error.response?.status >= 500) {
      toast.error('Erreur serveur. Veuillez réessayer plus tard.')
    } else if (error.response?.status >= 400) {
      // Show backend error message for client errors
      toast.error(backendMessage)
    }
    return Promise.reject(error)
  }
)

export default api 