import { describe, it, expect, beforeEach, vi } from 'vitest'
import { setActivePinia, createPinia } from 'pinia'
import { useAuthStore } from '../auth'
import api from '@/services/api'

vi.mock('@/services/api')

describe('AuthStore', () => {
  beforeEach(() => {
    setActivePinia(createPinia())
    localStorage.clear()
    vi.clearAllMocks()
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
    it('should store token on login', async () => {
      const authStore = useAuthStore()
      const mockToken = 'mock-jwt-token'
      const mockUser = {
        id: 1,
        name: 'Test User',
        email: 'test@example.com',
        role: 'admin'
      }

      vi.mocked(api.post).mockResolvedValue({ data: { token: mockToken, user: mockUser } })

      await authStore.login({ email: 'test@example.com', password: 'password' })

      expect(authStore.token).toBe(mockToken)
      expect(authStore.user).toEqual(mockUser)
      expect(authStore.isAuthenticated).toBe(true)
    })

    it('should save token to localStorage', async () => {
      const authStore = useAuthStore()
      const mockToken = 'mock-jwt-token'
      const mockUser = {
        id: 1,
        name: 'Test User',
        email: 'test@example.com',
        role: 'admin'
      }

      vi.mocked(api.post).mockResolvedValue({ data: { token: mockToken, user: mockUser } })

      await authStore.login({ email: 'test@example.com', password: 'password' })

      expect(localStorage.getItem('token')).toBe(mockToken)
      expect(localStorage.getItem('user')).toBe(JSON.stringify(mockUser))
    })
  })

  describe('logout', () => {
    it('should clear token on logout', async () => {
      const authStore = useAuthStore()
      const mockToken = 'mock-jwt-token'
      const mockUser = { id: 1, name: 'User', email: 'user@test.com', role: 'admin' }
      vi.mocked(api.post).mockResolvedValue({ data: { token: mockToken, user: mockUser } })
      await authStore.login({ email: 'user@test.com', password: 'password' })

      authStore.logout()

      expect(authStore.token).toBe('')
      expect(authStore.user).toBeNull()
      expect(authStore.isAuthenticated).toBe(false)
    })

    it('should remove token from localStorage on logout', async () => {
      const authStore = useAuthStore()
      const mockToken = 'mock-jwt-token'
      const mockUser = { id: 1, name: 'User', email: 'user@test.com', role: 'admin' }
      vi.mocked(api.post).mockResolvedValue({ data: { token: mockToken, user: mockUser } })
      await authStore.login({ email: 'user@test.com', password: 'password' })

      authStore.logout()

      expect(localStorage.getItem('token')).toBeNull()
      expect(localStorage.getItem('user')).toBeNull()
    })
  })

  describe('register', () => {
    it('should call register endpoint and then login', async () => {
      const authStore = useAuthStore()
      const credentials = { name: 'New User', email: 'new@example.com', password: 'password123' }
      const mockToken = 'new-token'
      const mockUser = { id: 2, name: 'New User', email: 'new@example.com', role: 'user' }

      vi.mocked(api.post)
        .mockResolvedValueOnce({ data: {} })
        .mockResolvedValueOnce({ data: { token: mockToken, user: mockUser } })

      await authStore.register(credentials)

      expect(api.post).toHaveBeenCalledWith('/auth/register', credentials)
      expect(api.post).toHaveBeenCalledWith('/auth/login', { email: credentials.email, password: credentials.password })
      expect(authStore.token).toBe(mockToken)
      expect(authStore.user).toEqual(mockUser)
      expect(authStore.isAuthenticated).toBe(true)
    })
  })

  describe('checkAuth', () => {
    it('should set user if token is valid', async () => {
      const mockToken = 'valid-token'
      const mockUser = { id: 1, name: 'Test User', email: 'test@example.com', role: 'admin' }
      localStorage.setItem('token', mockToken)
      const authStore = useAuthStore()

      vi.mocked(api.get).mockResolvedValue({ data: mockUser })

      await authStore.checkAuth()

      expect(api.get).toHaveBeenCalledWith('/auth/me')
      expect(authStore.user).toEqual(mockUser)
      expect(authStore.isAuthResolved).toBe(true)
    })

    it('should logout if token is invalid', async () => {
      localStorage.setItem('token', 'invalid-token')
      const authStore = useAuthStore()

      vi.mocked(api.get).mockRejectedValue(new Error('Invalid token'))

      await authStore.checkAuth()

      expect(authStore.token).toBe('')
      expect(authStore.user).toBeNull()
      expect(authStore.isAuthResolved).toBe(true)
    })
  })
})
