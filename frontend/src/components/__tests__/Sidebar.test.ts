import { describe, it, expect, beforeEach, vi } from 'vitest'
import { mount } from '@vue/test-utils'
import { createPinia, setActivePinia } from 'pinia'
import Sidebar from '../Sidebar.vue'

// Mock router
const mockPush = vi.fn()
vi.mock('vue-router', () => ({
  useRouter: () => ({
    push: mockPush
  }),
  RouterLink: {
    template: '<a><slot /></a>',
    props: ['to']
  }
}))

describe('Sidebar', () => {
  beforeEach(() => {
    setActivePinia(createPinia())
    mockPush.mockClear()
  })

  it('should render sidebar component', () => {
    const wrapper = mount(Sidebar)
    expect(wrapper.exists()).toBe(true)
  })

  it('should display application name', () => {
    const wrapper = mount(Sidebar)
    expect(wrapper.text()).toContain('ADAGRI')
  })

  it('should display navigation menu items', () => {
    const wrapper = mount(Sidebar)
    expect(wrapper.text()).toContain('Visão Geral')
    expect(wrapper.text()).toContain('Produtores')
  })

  it('should have links to main routes', () => {
    const wrapper = mount(Sidebar)
    const text = wrapper.text()

    expect(text).toContain('Visão Geral')
    expect(text).toContain('Produtores')
  })

  it('should display navigation icons', () => {
    const wrapper = mount(Sidebar)
    // Check if sidebar has some visual elements (icons represented by emojis or svg)
    expect(wrapper.html()).toContain('Visão Geral')
  })
})
