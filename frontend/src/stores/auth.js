import { defineStore } from 'pinia'
import { ref, computed } from 'vue'
import { useToast } from 'vue-toastification'
import api from '@/services/api'

export const useAuthStore = defineStore('auth', () => {
  const user = ref(null)
  const token = ref(localStorage.getItem('token'))
  const loading = ref(false)

  const toast = useToast()

  const isAuthenticated = computed(() => {
    const authenticated = !!token.value && !!user.value
    console.log('isAuthenticated computed:', authenticated, 'token:', !!token.value, 'user:', !!user.value)
    return authenticated
  })

  // Initialize auth state
  const init = async () => {
    const storedToken = localStorage.getItem('token')
    
    if (storedToken) {
      // Set token in store and axios headers
      token.value = storedToken
      api.defaults.headers.common['Authorization'] = `Bearer ${storedToken}`
      
      try {
        // Try to get user profile
        const response = await api.get('/auth/profile')
        if (response.data && response.data.data) {
          // New structure: { success, message, data: { user } }
          user.value = response.data.data
        } else if (response.data && response.data.user) {
          // Old structure: { user }
          user.value = response.data.user
        } else if (response.data) {
          // If the response is the user object directly
          user.value = response.data
        }
        console.log('Auth initialized successfully')
        return true
      } catch (error) {
        console.warn('Failed to initialize auth state:', error)
        
        // Only logout if it's a 401 (invalid token)
        if (error.response?.status === 401) {
          console.log('Token is invalid, logging out')
          logout()
        }
        return false
      }
    }
    
    console.log('No token found, user not authenticated')
    return false
  }

  // Login
  const login = async (credentials) => {
    loading.value = true
    try {
      const response = await api.post('/auth/login', credentials)
      
      // Handle new response structure: { success, message, data: { token } }
      const responseData = response.data
      let tokenData
      
      if (responseData.data && responseData.data.token) {
        // New structure: { success, message, data: { token } }
        tokenData = responseData.data.token
      } else if (responseData.token) {
        // Old structure: { token }
        tokenData = responseData.token
      } else {
        throw new Error('Token not found in response')
      }
      
      // Set token first
      token.value = tokenData
      localStorage.setItem('token', tokenData)
      
      // Set token in axios headers
      api.defaults.headers.common['Authorization'] = `Bearer ${tokenData}`
      
      // Get user profile (but don't retry if it fails)
      try {
        const profileResponse = await api.get('/auth/profile')
        if (profileResponse.data && profileResponse.data.data) {
          // New structure: { success, message, data: { user } }
          user.value = profileResponse.data.data
        } else if (profileResponse.data && profileResponse.data.user) {
          // Old structure: { user }
          user.value = profileResponse.data.user
        } else if (profileResponse.data) {
          // If the response is the user object directly
          user.value = profileResponse.data
        }
        console.log('User profile loaded:', user.value)
      } catch (profileError) {
        console.warn('Could not fetch user profile:', profileError)
        // If profile fetch fails, we should still be able to login
        // but we need to set a minimal user object to make isAuthenticated work
        user.value = { id: 'temp', email: credentials.email }
        console.log('Set temporary user object for authentication')
      }
      
      toast.success('Connexion réussie')
      return { success: true }
    } catch (error) {
      const message = error.response?.data?.message || 'Erreur de connexion'
      toast.error(message)
      return { success: false, message }
    } finally {
      loading.value = false
    }
  }

  // Register
  const register = async (userData) => {
    loading.value = true
    try {
      const formData = new FormData()
      
      // Add all user data to form
      Object.keys(userData).forEach(key => {
        if (key === 'image' && userData[key]) {
          formData.append(key, userData[key])
        } else if (key !== 'image') {
          formData.append(key, userData[key])
        }
      })

      const response = await api.post('/auth/register', formData, {
        headers: {
          'Content-Type': 'multipart/form-data'
        }
      })
      
      const { user: newUser } = response.data
      
      user.value = newUser
      // For register, we don't get a token, so we need to login after registration
      toast.success('Compte créé avec succès. Veuillez vous connecter.')
      return { success: true, needsLogin: true }
    } catch (error) {
      const message = error.response?.data?.message || 'Erreur d\'inscription'
      toast.error(message)
      return { success: false, message }
    } finally {
      loading.value = false
    }
  }

  // Force logout (for auth errors without API call)
  const forceLogout = () => {
    console.log('Force logout - clearing all auth data')
    user.value = null
    token.value = null
    localStorage.removeItem('token')
    delete api.defaults.headers.common['Authorization']
  }

  // Logout
  const logout = async () => {
    try {
      if (token.value) {
        await api.post('/auth/logout')
      }
    } catch (error) {
      console.error('Logout error:', error)
    } finally {
      forceLogout()
      toast.success('Déconnexion réussie')
    }
  }

  // Refresh token
  const refreshToken = async () => {
    try {
      console.log('Attempting to refresh token...')
      
      // Check if we have a token to refresh
      const currentToken = localStorage.getItem('token')
      if (!currentToken) {
        console.log('No token found for refresh')
        return { success: false, error: 'No token to refresh' }
      }
      
      const response = await api.post('/auth/refresh')
      
      // Handle new response structure: { success, message, data: { token } }
      const responseData = response.data
      let newToken
      
      if (responseData.data && responseData.data.token) {
        // New structure: { success, message, data: { token } }
        newToken = responseData.data.token
      } else if (responseData.token) {
        // Old structure: { token }
        newToken = responseData.token
      } else {
        throw new Error('Token not found in response')
      }
      
      console.log('Token refreshed successfully')
      token.value = newToken
      localStorage.setItem('token', newToken)
      api.defaults.headers.common['Authorization'] = `Bearer ${newToken}`
      
      return { success: true }
    } catch (error) {
      console.error('Token refresh failed:', error)
      
      // Clear all auth data on refresh failure
      user.value = null
      token.value = null
      localStorage.removeItem('token')
      delete api.defaults.headers.common['Authorization']
      
      return { 
        success: false, 
        error: error.response?.data?.message || 'Token refresh failed' 
      }
    }
  }

  return {
    user,
    token,
    loading,
    isAuthenticated,
    init,
    login,
    register,
    logout,
    forceLogout,
    refreshToken
  }
}) 