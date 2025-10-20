import { describe, it, expect, beforeEach, vi } from 'vitest'
import {
  getPropriedadesPorMunicipio,
  getAnimaisPorEspecie,
  getHectaresPorCultura
} from '../reportService'
import { api } from '../api'

vi.mock('../api', () => ({
  api: {
    get: vi.fn(),
    post: vi.fn(),
    put: vi.fn(),
    delete: vi.fn(),
    interceptors: {
      request: { use: vi.fn() },
      response: { use: vi.fn() }
    }
  }
}))

describe('ReportService', () => {
  beforeEach(() => {
    vi.clearAllMocks()
  })

  describe('getPropriedadesPorMunicipio', () => {
    it('deve buscar propriedades por município sem filtros', async () => {
      const mockApiData = [
        { municipio: 'Fortaleza', uf: 'CE', total_propriedades: 150, area_total_ha: 1000, total_produtores: 100 },
        { municipio: 'Caucaia', uf: 'CE', total_propriedades: 75, area_total_ha: 500, total_produtores: 50 }
      ]
      const expectedData = [
        { municipio: 'Fortaleza', uf: 'CE', total: 150 },
        { municipio: 'Caucaia', uf: 'CE', total: 75 }
      ]

      vi.mocked(api.get).mockResolvedValue({ data: { data: mockApiData } } as any as any)

      const result = await getPropriedadesPorMunicipio()

      expect(api.get).toHaveBeenCalledWith('/relatorios/propriedades-por-municipio', {
        params: {}
      })
      expect(result).toEqual(expectedData)
    })

    it('deve buscar propriedades filtradas por UF', async () => {
      const mockApiData = [
        { municipio: 'Fortaleza', uf: 'CE', total_propriedades: 150, area_total_ha: 1000, total_produtores: 100 }
      ]
      const expectedData = [
        { municipio: 'Fortaleza', uf: 'CE', total: 150 }
      ]

      vi.mocked(api.get).mockResolvedValue({ data: { data: mockApiData } } as any)

      const result = await getPropriedadesPorMunicipio('CE')

      expect(api.get).toHaveBeenCalledWith('/relatorios/propriedades-por-municipio', {
        params: { uf: 'CE' }
      })
      expect(result).toEqual(expectedData)
    })

    it('deve buscar propriedades filtradas por UF e município', async () => {
      const mockApiData = [
        { municipio: 'Fortaleza', uf: 'CE', total_propriedades: 150, area_total_ha: 1000, total_produtores: 100 }
      ]
      const expectedData = [
        { municipio: 'Fortaleza', uf: 'CE', total: 150 }
      ]

      vi.mocked(api.get).mockResolvedValue({ data: { data: mockApiData } } as any)

      const result = await getPropriedadesPorMunicipio('CE', 'Fortaleza')

      expect(api.get).toHaveBeenCalledWith('/relatorios/propriedades-por-municipio', {
        params: { uf: 'CE', municipio: 'Fortaleza' }
      })
      expect(result).toEqual(expectedData)
    })

    it('deve lançar erro em caso de falha na API', async () => {
      vi.mocked(api.get).mockRejectedValue(new Error('Network error'))

      await expect(getPropriedadesPorMunicipio()).rejects.toThrow('Network error')
    })
  })

  describe('getAnimaisPorEspecie', () => {
    it('deve buscar animais por espécie sem filtros', async () => {
      const mockApiData = [
        { especie: 'Bovino', total_animais: 5000, total_rebanhos: 10, total_propriedades: 5, media_animais_por_rebanho: 500 },
        { especie: 'Suíno', total_animais: 2500, total_rebanhos: 5, total_propriedades: 3, media_animais_por_rebanho: 500 },
        { especie: 'Caprino', total_animais: 1200, total_rebanhos: 4, total_propriedades: 2, media_animais_por_rebanho: 300 }
      ]
      const expectedData = [
        { especie: 'Bovino', total: 5000 },
        { especie: 'Suíno', total: 2500 },
        { especie: 'Caprino', total: 1200 }
      ]

      vi.mocked(api.get).mockResolvedValue({ data: { data: mockApiData } } as any)

      const result = await getAnimaisPorEspecie()

      expect(api.get).toHaveBeenCalledWith('/relatorios/animais-por-especie', {
        params: {}
      })
      expect(result).toEqual(expectedData)
    })

    it('deve buscar animais filtrados por espécie', async () => {
      const mockApiData = [
        { especie: 'Bovino', total_animais: 5000, total_rebanhos: 10, total_propriedades: 5, media_animais_por_rebanho: 500 }
      ]
      const expectedData = [
        { especie: 'Bovino', total: 5000 }
      ]

      vi.mocked(api.get).mockResolvedValue({ data: { data: mockApiData } } as any)

      const result = await getAnimaisPorEspecie('Bovino')

      expect(api.get).toHaveBeenCalledWith('/relatorios/animais-por-especie', {
        params: { especie: 'Bovino' }
      })
      expect(result).toEqual(expectedData)
    })

    it('deve lançar erro em caso de falha na API', async () => {
      vi.mocked(api.get).mockRejectedValue(new Error('Network error'))

      await expect(getAnimaisPorEspecie()).rejects.toThrow('Network error')
    })
  })

  describe('getHectaresPorCultura', () => {
    it('deve buscar hectares por cultura sem filtros', async () => {
      const mockApiData = [
        { cultura: 'Soja', total_hectares: 15000, total_unidades: 10, total_propriedades: 8, media_hectares_por_unidade: 1500 },
        { cultura: 'Milho', total_hectares: 12000, total_unidades: 8, total_propriedades: 6, media_hectares_por_unidade: 1500 },
        { cultura: 'Arroz', total_hectares: 8000, total_unidades: 5, total_propriedades: 4, media_hectares_por_unidade: 1600 }
      ]
      const expectedData = [
        { cultura: 'Soja', hectares: 15000 },
        { cultura: 'Milho', hectares: 12000 },
        { cultura: 'Arroz', hectares: 8000 }
      ]

      vi.mocked(api.get).mockResolvedValue({ data: { data: mockApiData } } as any)

      const result = await getHectaresPorCultura()

      expect(api.get).toHaveBeenCalledWith('/relatorios/hectares-por-cultura', {
        params: {}
      })
      expect(result).toEqual(expectedData)
    })

    it('deve buscar hectares filtrados por cultura', async () => {
      const mockApiData = [
        { cultura: 'Soja', total_hectares: 15000, total_unidades: 10, total_propriedades: 8, media_hectares_por_unidade: 1500 }
      ]
      const expectedData = [
        { cultura: 'Soja', hectares: 15000 }
      ]

      vi.mocked(api.get).mockResolvedValue({ data: { data: mockApiData } } as any)

      const result = await getHectaresPorCultura('Soja')

      expect(api.get).toHaveBeenCalledWith('/relatorios/hectares-por-cultura', {
        params: { cultura: 'Soja' }
      })
      expect(result).toEqual(expectedData)
    })

    it('deve lançar erro em caso de falha na API', async () => {
      vi.mocked(api.get).mockRejectedValue(new Error('Network error'))

      await expect(getHectaresPorCultura()).rejects.toThrow('Network error')
    })
  })
})
