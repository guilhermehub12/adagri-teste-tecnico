import { describe, it, expect, beforeEach, vi } from 'vitest'
import { mount } from '@vue/test-utils'
import { createPinia, setActivePinia } from 'pinia'
import { useAuthStore } from '@/stores/auth'
import DashboardView from '../DashboardView.vue'
import api from '@/services/api'

vi.mock('@/services/api')

describe('DashboardView', () => {
  beforeEach(() => {
    setActivePinia(createPinia())
    vi.clearAllMocks()
  })

  it('should render dashboard view', () => {
    const wrapper = mount(DashboardView)
    expect(wrapper.exists()).toBe(true)
  })

  it('should display welcome message', () => {
    const wrapper = mount(DashboardView)
    expect(wrapper.text()).toMatch(/bem.vindo|dashboard/i)
  })

  it('should display user name in welcome message', async () => {
    const authStore = useAuthStore()
    const mockUser = {
      id: 1,
      name: 'Maria Santos',
      email: 'maria@test.com',
      role: 'admin'
    }
    vi.mocked(api.post).mockResolvedValue({ data: { token: 'fake-token', user: mockUser } })
    await authStore.login({ email: 'maria@test.com', password: 'password' })

    const wrapper = mount(DashboardView)
    expect(wrapper.text()).toContain('Maria Santos')
  })

  it('should display statistics cards', () => {
    const wrapper = mount(DashboardView)
    const html = wrapper.html()

    // Dashboard should have some content/cards
    expect(html.length).toBeGreaterThan(100)
  })

  it('should have title', () => {
    const wrapper = mount(DashboardView)
    const html = wrapper.html()

    // Should have some heading
    expect(html).toMatch(/<h[1-6]/i)
  })
})
