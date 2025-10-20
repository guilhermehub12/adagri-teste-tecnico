import { describe, it, expect, beforeEach, vi } from 'vitest'
import { mount } from '@vue/test-utils'
import HectaresPorCulturaChart from '../HectaresPorCulturaChart.vue'
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

describe('HectaresPorCulturaChart', () => {
  beforeEach(() => {
    vi.clearAllMocks()
  })

  it('deve renderizar o componente corretamente', () => {
    vi.spyOn(reportService, 'getHectaresPorCultura').mockResolvedValue([])

    const wrapper = mount(HectaresPorCulturaChart)

    expect(wrapper.find('.chart-title').text()).toBe('Hectares por Cultura')
  })

  it('deve exibir loading state ao carregar dados', async () => {
    vi.spyOn(reportService, 'getHectaresPorCultura').mockImplementation(
      () => new Promise(() => {})
    )

    const wrapper = mount(HectaresPorCulturaChart)
    await wrapper.vm.$nextTick()

    expect(wrapper.find('.chart-loading').exists()).toBe(true)
    expect(wrapper.find('.chart-loading').text()).toContain('Carregando dados...')
  })

  it('deve exibir dados quando carregados com sucesso', async () => {
    const mockData = [
      { cultura: 'Soja', hectares: 15000 },
      { cultura: 'Milho', hectares: 12000 }
    ]

    vi.spyOn(reportService, 'getHectaresPorCultura').mockResolvedValue(mockData)

    const wrapper = mount(HectaresPorCulturaChart, {
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
    vi.spyOn(reportService, 'getHectaresPorCultura').mockRejectedValue(
      new Error('Network error')
    )

    const wrapper = mount(HectaresPorCulturaChart)
    await wrapper.vm.$nextTick()
    await new Promise(resolve => setTimeout(resolve, 0))

    expect(wrapper.find('.chart-error').exists()).toBe(true)
    expect(wrapper.find('.chart-error').text()).toContain('Erro ao carregar dados')
  })

  it('deve exibir mensagem quando não há dados', async () => {
    vi.spyOn(reportService, 'getHectaresPorCultura').mockResolvedValue([])

    const wrapper = mount(HectaresPorCulturaChart)
    await wrapper.vm.$nextTick()
    await new Promise(resolve => setTimeout(resolve, 0))

    expect(wrapper.find('.chart-empty').exists()).toBe(true)
    expect(wrapper.find('.chart-empty').text()).toContain('Nenhum dado disponível')
  })

  it('deve popular filtro de culturas com dados únicos', async () => {
    const mockData = [
      { cultura: 'Soja', hectares: 15000 },
      { cultura: 'Milho', hectares: 12000 },
      { cultura: 'Arroz', hectares: 8000 }
    ]

    vi.spyOn(reportService, 'getHectaresPorCultura').mockResolvedValue(mockData)

    const wrapper = mount(HectaresPorCulturaChart)
    await wrapper.vm.$nextTick()
    await new Promise(resolve => setTimeout(resolve, 0))

    // Click filter button to open dropdown
    const filterButton = wrapper.find('.filter-toggle-button')
    await filterButton.trigger('click')
    await wrapper.vm.$nextTick()

    // Check checkboxes for culture options
    const checkboxes = wrapper.find('.filter-dropdown').findAll('.filter-checkbox')
    expect(checkboxes.length).toBe(3) // Arroz + Milho + Soja
  })

  it('deve filtrar por cultura selecionada', async () => {
    const mockData = [
      { cultura: 'Soja', hectares: 15000 },
      { cultura: 'Milho', hectares: 12000 }
    ]

    const getHectaresSpy = vi
      .spyOn(reportService, 'getHectaresPorCultura')
      .mockResolvedValue(mockData)

    const wrapper = mount(HectaresPorCulturaChart)
    await wrapper.vm.$nextTick()
    await new Promise(resolve => setTimeout(resolve, 0))

    // Click filter button to open dropdown
    const filterButton = wrapper.find('.filter-toggle-button')
    await filterButton.trigger('click')
    await wrapper.vm.$nextTick()

    // Check Soja checkbox (sorted alphabetically, so index 1)
    const checkboxes = wrapper.find('.filter-dropdown').findAll('.filter-checkbox')
    expect(checkboxes).toBeDefined()
    await checkboxes?.[1]?.setValue(true) // Soja checkbox
    await wrapper.vm.$nextTick()

    // The service should only be called once on mount (no API call for frontend filtering)
    expect(getHectaresSpy).toHaveBeenCalledTimes(1)
    expect(getHectaresSpy).toHaveBeenCalledWith()
  })

  it('deve recarregar dados ao clicar em "Tentar novamente"', async () => {
    const getHectaresSpy = vi
      .spyOn(reportService, 'getHectaresPorCultura')
      .mockRejectedValueOnce(new Error('Network error'))
      .mockResolvedValueOnce([])

    const wrapper = mount(HectaresPorCulturaChart)
    await wrapper.vm.$nextTick()
    await new Promise(resolve => setTimeout(resolve, 0))

    expect(wrapper.find('.chart-error').exists()).toBe(true)

    const retryButton = wrapper.find('.retry-button')
    await retryButton.trigger('click')
    await wrapper.vm.$nextTick()
    await new Promise(resolve => setTimeout(resolve, 0))

    expect(getHectaresSpy).toHaveBeenCalledTimes(2)
  })
})
