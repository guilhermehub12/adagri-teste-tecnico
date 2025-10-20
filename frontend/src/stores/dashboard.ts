import { defineStore } from 'pinia'
import { ref } from 'vue'
import { getDashboardData } from '@/services/dashboardService'

export const useDashboardStore = defineStore('dashboard', () => {
  const totalProdutores = ref(0)
  const totalPropriedades = ref(0)
  const totalAnimais = ref(0)
  const totalHectares = ref(0)
  const loading = ref(false)

  async function fetchDashboardData() {
    loading.value = true
    try {
      const data = await getDashboardData()
      totalProdutores.value = data.totalProdutores
      totalPropriedades.value = data.totalPropriedades
      totalAnimais.value = data.totalAnimais
      totalHectares.value = data.totalHectares
    } catch (error) {
      console.error('Error fetching dashboard data:', error)
    } finally {
      loading.value = false
    }
  }

  return {
    totalProdutores,
    totalPropriedades,
    totalAnimais,
    totalHectares,
    loading,
    fetchDashboardData
  }
})
