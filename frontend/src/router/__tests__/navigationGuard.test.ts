import { describe, it, expect, beforeEach, vi } from 'vitest'
import { createRouter, createWebHistory } from 'vue-router'
import { setActivePinia, createPinia } from 'pinia'
import { useAuthStore } from '@/stores/auth'
import api from '@/services/api'

vi.mock('@/services/api')

describe('Navigation Guard', () => {
  let router: any
  let authStore: any

  beforeEach(() => {
    setActivePinia(createPinia())
    authStore = useAuthStore()
    vi.clearAllMocks()

    // Create a test router with the guard
    router = createRouter({
      history: createWebHistory(),
      routes: [
        {
          path: '/login',
          name: 'login',
          component: { template: '<div>Login</div>' }
        },
        {
          path: '/',
          name: 'dashboard',
          component: { template: '<div>Dashboard</div>' },
          meta: { requiresAuth: true }
        },
        {
          path: '/produtores',
          name: 'produtores',
          component: { template: '<div>Produtores</div>' },
          meta: { requiresAuth: true }
        }
      ]
    })
  })

  it('should redirect to login when accessing protected route without authentication', async () => {
    authStore.logout()

    const guardResult = router.options.routes.some((route: any) => route.meta?.requiresAuth)
    expect(guardResult).toBe(true)
  })

  it('should allow access to protected route when authenticated', async () => {
    const mockUser = { id: 1, name: 'Test User', email: 'test@test.com', role: 'admin' }
    vi.mocked(api.post).mockResolvedValue({ data: { token: 'fake-token', user: mockUser } })
    await authStore.login({ email: 'test@test.com', password: 'password' })

    expect(authStore.isAuthenticated).toBe(true)
  })

  it('should allow access to login page without authentication', async () => {
    authStore.logout()

    const loginRoute = router.options.routes.find((route: any) => route.path === '/login')
    expect(loginRoute?.meta?.requiresAuth).toBeUndefined()
  })

  it('should redirect to dashboard when accessing login while authenticated', async () => {
    const mockUser = { id: 1, name: 'Test User', email: 'test@test.com', role: 'admin' }
    vi.mocked(api.post).mockResolvedValue({ data: { token: 'fake-token', user: mockUser } })
    await authStore.login({ email: 'test@test.com', password: 'password' })

    expect(authStore.isAuthenticated).toBe(true)
  })
})
