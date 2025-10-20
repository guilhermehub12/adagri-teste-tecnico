import { describe, it, expect, beforeEach, vi } from 'vitest'
import { mount } from '@vue/test-utils'
import PropriedadesPorMunicipioChart from '../PropriedadesPorMunicipioChart.vue'
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

describe('PropriedadesPorMunicipioChart', () => {
  beforeEach(() => {
    vi.clearAllMocks()
  })

  it('deve renderizar o componente corretamente', () => {
    vi.spyOn(reportService, 'getPropriedadesPorMunicipio').mockResolvedValue([])

    const wrapper = mount(PropriedadesPorMunicipioChart)

    expect(wrapper.find('.chart-title').text()).toBe('Propriedades')
  })

  it('deve exibir loading state ao carregar dados', async () => {
    vi.spyOn(reportService, 'getPropriedadesPorMunicipio').mockImplementation(
      () => new Promise(() => {}) // Never resolves
    )

    const wrapper = mount(PropriedadesPorMunicipioChart)
    await wrapper.vm.$nextTick()

    expect(wrapper.find('.chart-loading').exists()).toBe(true)
    expect(wrapper.find('.chart-loading').text()).toContain('Carregando dados...')
  })

  it('deve exibir dados quando carregados com sucesso', async () => {
    const mockData = [
      { municipio: 'Fortaleza', uf: 'CE', total: 150 },
      { municipio: 'Caucaia', uf: 'CE', total: 75 }
    ]

    vi.spyOn(reportService, 'getPropriedadesPorMunicipio').mockResolvedValue(mockData)

    const wrapper = mount(PropriedadesPorMunicipioChart, {
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
    vi.spyOn(reportService, 'getPropriedadesPorMunicipio').mockRejectedValue(
      new Error('Network error')
    )

    const wrapper = mount(PropriedadesPorMunicipioChart)
    await wrapper.vm.$nextTick()
    await new Promise(resolve => setTimeout(resolve, 0))

    expect(wrapper.find('.chart-error').exists()).toBe(true)
    expect(wrapper.find('.chart-error').text()).toContain('Erro ao carregar dados')
  })

  it('deve exibir mensagem quando não há dados', async () => {
    vi.spyOn(reportService, 'getPropriedadesPorMunicipio').mockResolvedValue([])

    const wrapper = mount(PropriedadesPorMunicipioChart)
    await wrapper.vm.$nextTick()
    await new Promise(resolve => setTimeout(resolve, 0))

    expect(wrapper.find('.chart-empty').exists()).toBe(true)
    expect(wrapper.find('.chart-empty').text()).toContain('Nenhum dado disponível')
  })

  it('deve popular filtro de UF com dados únicos', async () => {
    const mockData = [
      { municipio: 'Fortaleza', uf: 'CE', total: 150 },
      { municipio: 'Caucaia', uf: 'CE', total: 75 },
      { municipio: 'Recife', uf: 'PE', total: 200 }
    ]

    vi.spyOn(reportService, 'getPropriedadesPorMunicipio').mockResolvedValue(mockData)

    const wrapper = mount(PropriedadesPorMunicipioChart)
    await wrapper.vm.$nextTick()
    await new Promise(resolve => setTimeout(resolve, 0))

    // Click UF filter button to open dropdown
    const ufFilterButton = wrapper.findAll('.filter-toggle-button')[0]
    expect(ufFilterButton).toBeDefined()
    await ufFilterButton?.trigger('click')
    await wrapper.vm.$nextTick()

    // Check checkboxes for UF options
    const ufDropdown = wrapper.findAll('.filter-dropdown')[0]
    expect(ufDropdown).toBeDefined()
    const ufCheckboxes = ufDropdown?.findAll('.filter-checkbox')
    expect(ufCheckboxes).toBeDefined()
    expect(ufCheckboxes?.length).toBe(2) // CE + PE
  })

  it('deve filtrar municípios por UF selecionada', async () => {
    const mockData = [
      { municipio: 'Fortaleza', uf: 'CE', total: 150 },
      { municipio: 'Caucaia', uf: 'CE', total: 75 },
      { municipio: 'Recife', uf: 'PE', total: 200 }
    ]

    vi.spyOn(reportService, 'getPropriedadesPorMunicipio').mockResolvedValue(mockData)

    const wrapper = mount(PropriedadesPorMunicipioChart)
    await wrapper.vm.$nextTick()
    await new Promise(resolve => setTimeout(resolve, 0))

    // Click UF filter button and select CE
    const ufFilterButton = wrapper.findAll('.filter-toggle-button')[0]
    expect(ufFilterButton).toBeDefined()
    await ufFilterButton?.trigger('click')
    await wrapper.vm.$nextTick()

    // Check CE checkbox
    const ufDropdown = wrapper.findAll('.filter-dropdown')[0]
    expect(ufDropdown).toBeDefined()
    const ufCheckboxes = ufDropdown?.findAll('.filter-checkbox')
    expect(ufCheckboxes).toBeDefined()
    await ufCheckboxes?.[0]?.setValue(true) // CE checkbox
    await wrapper.vm.$nextTick()

    // Open município filter
    const municipioFilterButton = wrapper.findAll('.filter-toggle-button')[1]
    expect(municipioFilterButton).toBeDefined()
    await municipioFilterButton?.trigger('click')
    await wrapper.vm.$nextTick()

    // Should only have Caucaia and Fortaleza
    const municipioDropdown = wrapper.findAll('.filter-dropdown')[1]
    expect(municipioDropdown).toBeDefined()
    const municipioCheckboxes = municipioDropdown?.findAll('.filter-checkbox')
    expect(municipioCheckboxes).toBeDefined()
    expect(municipioCheckboxes?.length).toBe(2)
  })

  it('deve recarregar dados ao clicar em "Tentar novamente"', async () => {
    const getPropriedadesSpy = vi
      .spyOn(reportService, 'getPropriedadesPorMunicipio')
      .mockRejectedValueOnce(new Error('Network error'))
      .mockResolvedValueOnce([])

    const wrapper = mount(PropriedadesPorMunicipioChart)
    await wrapper.vm.$nextTick()
    await new Promise(resolve => setTimeout(resolve, 0))

    expect(wrapper.find('.chart-error').exists()).toBe(true)

    const retryButton = wrapper.find('.retry-button')
    await retryButton.trigger('click')
    await wrapper.vm.$nextTick()
    await new Promise(resolve => setTimeout(resolve, 0))

    expect(getPropriedadesSpy).toHaveBeenCalledTimes(2)
  })
})
