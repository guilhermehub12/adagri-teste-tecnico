import api from './api'

export interface DashboardStats {
  totalProdutores: number
  totalPropriedades: number
  totalAnimais: number
  totalHectares: number
}

export const getDashboardData = async (): Promise<DashboardStats> => {
  try {
    const response = await api.get<DashboardStats>('/dashboard/stats')
    return response.data
  } catch (error) {
    console.error('Erro ao buscar dados do dashboard:', error)
    throw error
  }
}
