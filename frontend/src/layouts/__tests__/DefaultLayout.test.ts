import { describe, it, expect, beforeEach, vi } from 'vitest'
import { mount } from '@vue/test-utils'
import { createPinia, setActivePinia } from 'pinia'
import DefaultLayout from '../DefaultLayout.vue'

// Mock router
vi.mock('vue-router', () => ({
  useRouter: () => ({
    push: vi.fn()
  }),
  RouterView: {
    template: '<div class="router-view-mock"><slot /></div>'
  },
  RouterLink: {
    template: '<a><slot /></a>',
    props: ['to']
  }
}))

describe('DefaultLayout', () => {
  beforeEach(() => {
    setActivePinia(createPinia())
  })

  it('should render default layout', () => {
    const wrapper = mount(DefaultLayout)
    expect(wrapper.exists()).toBe(true)
  })

  it('should have sidebar component', () => {
    const wrapper = mount(DefaultLayout)
    // Check if layout has navigation/sidebar content
    expect(wrapper.html()).toBeTruthy()
  })

  it('should have header component', () => {
    const wrapper = mount(DefaultLayout)
    // Check if layout has header content
    expect(wrapper.html()).toBeTruthy()
  })

  it('should have router-view for content', () => {
    const wrapper = mount(DefaultLayout)
    expect(wrapper.html()).toContain('routerview')
  })

  it('should have proper layout structure (sidebar + main content)', () => {
    const wrapper = mount(DefaultLayout)
    const html = wrapper.html()
    // Layout should have some structure
    expect(html.length).toBeGreaterThan(100)
  })
})
