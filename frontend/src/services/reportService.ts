import { api } from './api'

// API Response Types (real structure from backend)
interface ApiPropriedadePorMunicipio {
  municipio: string
  uf: string
  total_propriedades: number
  area_total_ha: number
  total_produtores: number
}

interface ApiAnimalPorEspecie {
  especie: string
  total_animais: number
  total_rebanhos: number
  total_propriedades: number
  media_animais_por_rebanho: number
}

interface ApiHectarePorCultura {
  cultura: string
  total_hectares: number
  total_unidades: number
  total_propriedades: number
  media_hectares_por_unidade: number
}

// Frontend Types (normalized structure for components)
export interface PropriedadePorMunicipio {
  municipio: string
  uf: string
  total: number
}

export interface AnimalPorEspecie {
  especie: string
  total: number
}

export interface HectarePorCultura {
  cultura: string
  hectares: number
}

/**
 * Busca relatório de propriedades por município
 * @param uf - Filtro opcional por UF
 * @param municipio - Filtro opcional por município
 */
export async function getPropriedadesPorMunicipio(
  uf?: string,
  municipio?: string
): Promise<PropriedadePorMunicipio[]> {
  const params: Record<string, string> = {}

  if (uf) params.uf = uf
  if (municipio) params.municipio = municipio

  const response = await api.get('/relatorios/propriedades-por-municipio', { params })
  const apiData = response.data.data as ApiPropriedadePorMunicipio[]

  // Map API response to frontend format
  return apiData.map(item => ({
    municipio: item.municipio,
    uf: item.uf,
    total: item.total_propriedades
  }))
}

/**
 * Busca relatório de animais por espécie
 * @param especie - Filtro opcional por espécie
 */
export async function getAnimaisPorEspecie(
  especie?: string
): Promise<AnimalPorEspecie[]> {
  const params: Record<string, string> = {}

  if (especie) params.especie = especie

  const response = await api.get('/relatorios/animais-por-especie', { params })
  const apiData = response.data.data as ApiAnimalPorEspecie[]

  // Map API response to frontend format
  return apiData.map(item => ({
    especie: item.especie,
    total: item.total_animais
  }))
}

/**
 * Busca relatório de hectares por cultura
 * @param cultura - Filtro opcional por cultura
 */
export async function getHectaresPorCultura(
  cultura?: string
): Promise<HectarePorCultura[]> {
  const params: Record<string, string> = {}

  if (cultura) params.cultura = cultura

  const response = await api.get('/relatorios/hectares-por-cultura', { params })
  const apiData = response.data.data as ApiHectarePorCultura[]

  // Map API response to frontend format
  return apiData.map(item => ({
    cultura: item.cultura,
    hectares: item.total_hectares
  }))
}
