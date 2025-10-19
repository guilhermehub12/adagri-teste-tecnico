import { describe, it, expect, beforeEach } from 'vitest'
import { setActivePinia, createPinia } from 'pinia'
import { useAuthStore } from '../auth'

describe('AuthStore', () => {
  beforeEach(() => {
    setActivePinia(createPinia())
    localStorage.clear()
  })

  describe('initial state', () => {
    it('should not be authenticated initially', () => {
      const authStore = useAuthStore()
      expect(authStore.isAuthenticated).toBe(false)
    })

    it('should have null user initially', () => {
      const authStore = useAuthStore()
      expect(authStore.user).toBeNull()
    })

    it('should have empty token initially', () => {
      const authStore = useAuthStore()
      expect(authStore.token).toBe('')
    })
  })

  describe('login', () => {
    it('should store token on login', () => {
      const authStore = useAuthStore()
      const mockToken = 'mock-jwt-token'
      const mockUser = {
        id: 1,
        name: 'Test User',
        email: 'test@example.com',
        role: 'admin'
      }

      authStore.login(mockToken, mockUser)

      expect(authStore.token).toBe(mockToken)
      expect(authStore.user).toEqual(mockUser)
      expect(authStore.isAuthenticated).toBe(true)
    })

    it('should save token to localStorage', () => {
      const authStore = useAuthStore()
      const mockToken = 'mock-jwt-token'
      const mockUser = {
        id: 1,
        name: 'Test User',
        email: 'test@example.com',
        role: 'admin'
      }

      authStore.login(mockToken, mockUser)

      expect(localStorage.getItem('token')).toBe(mockToken)
      expect(localStorage.getItem('user')).toBe(JSON.stringify(mockUser))
    })
  })

  describe('logout', () => {
    it('should clear token on logout', () => {
      const authStore = useAuthStore()
      authStore.login('token', { id: 1, name: 'User', email: 'user@test.com', role: 'admin' })

      authStore.logout()

      expect(authStore.token).toBe('')
      expect(authStore.user).toBeNull()
      expect(authStore.isAuthenticated).toBe(false)
    })

    it('should remove token from localStorage on logout', () => {
      const authStore = useAuthStore()
      authStore.login('token', { id: 1, name: 'User', email: 'user@test.com', role: 'admin' })

      authStore.logout()

      expect(localStorage.getItem('token')).toBeNull()
      expect(localStorage.getItem('user')).toBeNull()
    })
  })

  describe('hydrate from localStorage', () => {
    it('should restore token from localStorage on init', () => {
      const mockToken = 'stored-token'
      const mockUser = { id: 1, name: 'User', email: 'user@test.com', role: 'admin' }
      localStorage.setItem('token', mockToken)
      localStorage.setItem('user', JSON.stringify(mockUser))

      const authStore = useAuthStore()
      authStore.hydrateFromStorage()

      expect(authStore.token).toBe(mockToken)
      expect(authStore.user).toEqual(mockUser)
      expect(authStore.isAuthenticated).toBe(true)
    })

    it('should handle missing token in localStorage', () => {
      const authStore = useAuthStore()
      authStore.hydrateFromStorage()

      expect(authStore.token).toBe('')
      expect(authStore.user).toBeNull()
      expect(authStore.isAuthenticated).toBe(false)
    })
  })
})
