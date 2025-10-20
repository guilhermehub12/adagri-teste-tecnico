import { defineStore } from 'pinia'
import { ref, computed } from 'vue'
import api from '@/services/api'

interface User {
  id: number
  name: string
  email: string
  role: string
}

export const useAuthStore = defineStore('auth', () => {
  const token = ref(localStorage.getItem('token') || '')
  const user = ref<User | null>(JSON.parse(localStorage.getItem('user') || 'null'))
  const isAuthResolved = ref(false)

  const isAuthenticated = computed(() => !!token.value && !!user.value)

  async function login(credentials: any) {
    const response = await api.post('/auth/login', credentials)
    const { token: authToken, user: userData } = response.data

    token.value = authToken
    user.value = userData

    localStorage.setItem('token', authToken)
    localStorage.setItem('user', JSON.stringify(userData))
    api.defaults.headers.common['Authorization'] = `Bearer ${authToken}`
  }

  async function register(credentials: any) {
    await api.post('/auth/register', credentials)
    await login({ email: credentials.email, password: credentials.password })
  }

  function logout() {
    token.value = ''
    user.value = null
    localStorage.removeItem('token')
    localStorage.removeItem('user')
    delete api.defaults.headers.common['Authorization']
  }

  async function checkAuth() {
    if (token.value) {
      try {
        api.defaults.headers.common['Authorization'] = `Bearer ${token.value}`
        const { data } = await api.get('/auth/me')
        user.value = data.data
        localStorage.setItem('user', JSON.stringify(data.data))
      } catch (error) {
        logout()
      }
    }
    isAuthResolved.value = true
  }

  return {
    token,
    user,
    isAuthenticated,
    isAuthResolved,
    login,
    register,
    logout,
    checkAuth
  }
})
