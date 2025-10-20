import { describe, it, expect, beforeEach, vi } from 'vitest'
import { mount } from '@vue/test-utils'
import { createPinia, setActivePinia } from 'pinia'
import PrimeVue from 'primevue/config'
import { useAuthStore } from '@/stores/auth'
import Header from '../Header.vue'
import api from '@/services/api'

// Mock router
const mockPush = vi.fn()
vi.mock('vue-router', () => ({
  useRouter: () => ({
    push: mockPush
  })
}))

vi.mock('@/services/api')

describe('Header', () => {
  beforeEach(() => {
    setActivePinia(createPinia())
    mockPush.mockClear()
    vi.clearAllMocks()
  })

  const mountWithPrimeVue = (options = {}) => {
    return mount(Header, {
      global: {
        plugins: [PrimeVue],
      },
      ...options,
    })
  }

  it('should render header component', () => {
    const wrapper = mountWithPrimeVue()
    expect(wrapper.exists()).toBe(true)
  })

  it('should display user name when authenticated', async () => {
    const authStore = useAuthStore()
    const mockUser = {
      id: 1,
      name: 'João Silva',
      email: 'joao@test.com',
      role: 'admin'
    }
    vi.mocked(api.post).mockResolvedValue({ data: { token: 'fake-token', user: mockUser } })
    await authStore.login({ email: 'joao@test.com', password: 'password' })

    const wrapper = mountWithPrimeVue()
    expect(wrapper.text()).toContain('João Silva')
  })

  it('should display user role when authenticated', async () => {
    const authStore = useAuthStore()
    const mockUser = {
      id: 1,
      name: 'João Silva',
      email: 'joao@test.com',
      role: 'admin'
    }
    vi.mocked(api.post).mockResolvedValue({ data: { token: 'fake-token', user: mockUser } })
    await authStore.login({ email: 'joao@test.com', password: 'password' })

    const wrapper = mountWithPrimeVue()
    expect(wrapper.text()).toContain('admin')
  })

  it('should have logout button', () => {
    const wrapper = mountWithPrimeVue()
    // Find logout button by icon class or aria-label
    const logoutButton = wrapper.find('button[aria-label="Sair do sistema"]')
    expect(logoutButton.exists()).toBe(true)
    // Check for icon
    expect(logoutButton.html()).toContain('pi-sign-out')
  })

  it('should call logout and redirect to login when logout button is clicked', async () => {
    const authStore = useAuthStore()
    const mockUser = {
      id: 1,
      name: 'João Silva',
      email: 'joao@test.com',
      role: 'admin'
    }
    vi.mocked(api.post).mockResolvedValue({ data: { token: 'fake-token', user: mockUser } })
    await authStore.login({ email: 'joao@test.com', password: 'password' })

    const wrapper = mountWithPrimeVue()
    const logoutButton = wrapper.find('button[aria-label="Sair do sistema"]')

    await logoutButton.trigger('click')

    expect(authStore.isAuthenticated).toBe(false)
    expect(mockPush).toHaveBeenCalledWith('/login')
  })
})
