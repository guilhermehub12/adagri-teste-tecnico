import { defineStore } from 'pinia'
import { ref, computed } from 'vue'

interface User {
  id: number
  name: string
  email: string
  role: string
}

export const useAuthStore = defineStore('auth', () => {
  const token = ref('')
  const user = ref<User | null>(null)

  const isAuthenticated = computed(() => !!token.value)

  function login(authToken: string, userData: User) {
    token.value = authToken
    user.value = userData

    localStorage.setItem('token', authToken)
    localStorage.setItem('user', JSON.stringify(userData))
  }

  function logout() {
    token.value = ''
    user.value = null

    localStorage.removeItem('token')
    localStorage.removeItem('user')
  }

  function hydrateFromStorage() {
    const storedToken = localStorage.getItem('token')
    const storedUser = localStorage.getItem('user')

    if (storedToken && storedUser) {
      token.value = storedToken
      user.value = JSON.parse(storedUser)
    }
  }

  return {
    token,
    user,
    isAuthenticated,
    login,
    logout,
    hydrateFromStorage
  }
})
