import { describe, it, expect, beforeEach, vi } from 'vitest'
import { mount } from '@vue/test-utils'
import AnimaisPorEspecieChart from '../AnimaisPorEspecieChart.vue'
import * as reportService from '@/services/reportService'

vi.mock('@/services/reportService')

// Mock apexcharts component
vi.mock('vue3-apexcharts', () => ({
  default: {
    name: 'apexchart',
    template: '<div class="apexchart-mock"></div>',
    props: ['type', 'height', 'options', 'series']
  }
}))

describe('AnimaisPorEspecieChart', () => {
  beforeEach(() => {
    vi.clearAllMocks()
  })

  it('deve renderizar o componente corretamente', () => {
    vi.spyOn(reportService, 'getAnimaisPorEspecie').mockResolvedValue([])

    const wrapper = mount(AnimaisPorEspecieChart)

    expect(wrapper.find('.chart-title').text()).toBe('Animais por Espécie')
  })

  it('deve exibir loading state ao carregar dados', async () => {
    vi.spyOn(reportService, 'getAnimaisPorEspecie').mockImplementation(
      () => new Promise(() => {})
    )

    const wrapper = mount(AnimaisPorEspecieChart)
    await wrapper.vm.$nextTick()

    expect(wrapper.find('.chart-loading').exists()).toBe(true)
    expect(wrapper.find('.chart-loading').text()).toContain('Carregando dados...')
  })

  it('deve exibir dados quando carregados com sucesso', async () => {
    const mockData = [
      { especie: 'Bovino', total: 5000 },
      { especie: 'Suíno', total: 2500 }
    ]

    vi.spyOn(reportService, 'getAnimaisPorEspecie').mockResolvedValue(mockData)

    const wrapper = mount(AnimaisPorEspecieChart, {
      global: {
        stubs: {
          apexchart: {
            template: '<div class="apexchart-mock"></div>'
          }
        }
      }
    })
    await wrapper.vm.$nextTick()
    await new Promise(resolve => setTimeout(resolve, 0))

    expect(wrapper.find('.chart-loading').exists()).toBe(false)
    expect(wrapper.find('.chart-error').exists()).toBe(false)
    expect(wrapper.find('.chart-empty').exists()).toBe(false)
  })

  it('deve exibir mensagem de erro ao falhar', async () => {
    vi.spyOn(reportService, 'getAnimaisPorEspecie').mockRejectedValue(
      new Error('Network error')
    )

    const wrapper = mount(AnimaisPorEspecieChart)
    await wrapper.vm.$nextTick()
    await new Promise(resolve => setTimeout(resolve, 0))

    expect(wrapper.find('.chart-error').exists()).toBe(true)
    expect(wrapper.find('.chart-error').text()).toContain('Erro ao carregar dados')
  })

  it('deve exibir mensagem quando não há dados', async () => {
    vi.spyOn(reportService, 'getAnimaisPorEspecie').mockResolvedValue([])

    const wrapper = mount(AnimaisPorEspecieChart)
    await wrapper.vm.$nextTick()
    await new Promise(resolve => setTimeout(resolve, 0))

    expect(wrapper.find('.chart-empty').exists()).toBe(true)
    expect(wrapper.find('.chart-empty').text()).toContain('Nenhum dado disponível')
  })

  it('deve popular filtro de espécies com dados únicos', async () => {
    const mockData = [
      { especie: 'Bovino', total: 5000 },
      { especie: 'Suíno', total: 2500 },
      { especie: 'Caprino', total: 1200 }
    ]

    vi.spyOn(reportService, 'getAnimaisPorEspecie').mockResolvedValue(mockData)

    const wrapper = mount(AnimaisPorEspecieChart)
    await wrapper.vm.$nextTick()
    await new Promise(resolve => setTimeout(resolve, 0))

    // Click filter button to open dropdown
    const filterButton = wrapper.find('.filter-toggle-button')
    await filterButton.trigger('click')
    await wrapper.vm.$nextTick()

    // Check checkboxes for species options
    const checkboxes = wrapper.find('.filter-dropdown').findAll('.filter-checkbox')
    expect(checkboxes.length).toBe(3) // Bovino + Caprino + Suíno
  })

  it('deve filtrar por espécie selecionada', async () => {
    const mockData = [
      { especie: 'Bovino', total: 5000 },
      { especie: 'Suíno', total: 2500 }
    ]

    const getAnimaisSpy = vi
      .spyOn(reportService, 'getAnimaisPorEspecie')
      .mockResolvedValue(mockData)

    const wrapper = mount(AnimaisPorEspecieChart)
    await wrapper.vm.$nextTick()
    await new Promise(resolve => setTimeout(resolve, 0))

    // Click filter button to open dropdown
    const filterButton = wrapper.find('.filter-toggle-button')
    await filterButton.trigger('click')
    await wrapper.vm.$nextTick()

    // Check Bovino checkbox
    const checkboxes = wrapper.find('.filter-dropdown').findAll('.filter-checkbox')
    expect(checkboxes).toBeDefined()
    await checkboxes?.[0]?.setValue(true) // Bovino checkbox
    await wrapper.vm.$nextTick()

    // The service should only be called once on mount (no API call for frontend filtering)
    expect(getAnimaisSpy).toHaveBeenCalledTimes(1)
    expect(getAnimaisSpy).toHaveBeenCalledWith()
  })

  it('deve recarregar dados ao clicar em "Tentar novamente"', async () => {
    const getAnimaisSpy = vi
      .spyOn(reportService, 'getAnimaisPorEspecie')
      .mockRejectedValueOnce(new Error('Network error'))
      .mockResolvedValueOnce([])

    const wrapper = mount(AnimaisPorEspecieChart)
    await wrapper.vm.$nextTick()
    await new Promise(resolve => setTimeout(resolve, 0))

    expect(wrapper.find('.chart-error').exists()).toBe(true)

    const retryButton = wrapper.find('.retry-button')
    await retryButton.trigger('click')
    await wrapper.vm.$nextTick()
    await new Promise(resolve => setTimeout(resolve, 0))

    expect(getAnimaisSpy).toHaveBeenCalledTimes(2)
  })
})
