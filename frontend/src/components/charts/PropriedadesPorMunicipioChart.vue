<template>
  <div class="chart-container">
    <div class="chart-header">
      <h3 class="chart-title">Propriedades</h3>
      <div class="chart-filters-wrapper">
        <!-- Filtro de UF -->
        <div class="chart-filters">
          <button
            @click="showUfFilters = !showUfFilters"
            class="filter-toggle-button"
          >
            <i class="pi pi-filter"></i>
            UFs
            <span v-if="selectedUfs.length > 0" class="filter-count">
              ({{ selectedUfs.length }})
            </span>
          </button>
          <div v-if="showUfFilters" class="filter-dropdown">
            <div class="filter-actions">
              <button @click="selectAllUfs" class="filter-action-btn">
                Selecionar Todos
              </button>
              <button @click="clearUfs" class="filter-action-btn">
                Limpar
              </button>
            </div>
            <div class="filter-options">
              <label
                v-for="uf in availableUfs"
                :key="uf"
                class="filter-option"
              >
                <input
                  type="checkbox"
                  :value="uf"
                  v-model="selectedUfs"
                  class="filter-checkbox"
                />
                <span>{{ uf }}</span>
              </label>
            </div>
          </div>
        </div>

        <!-- Filtro de Município -->
        <div class="chart-filters">
          <button
            @click="showMunicipioFilters = !showMunicipioFilters"
            class="filter-toggle-button"
          >
            <i class="pi pi-filter"></i>
            Municípios
            <span v-if="selectedMunicipios.length > 0" class="filter-count">
              ({{ selectedMunicipios.length }})
            </span>
          </button>
          <div v-if="showMunicipioFilters" class="filter-dropdown">
            <div class="filter-actions">
              <button @click="selectAllMunicipios" class="filter-action-btn">
                Selecionar Todos
              </button>
              <button @click="clearMunicipios" class="filter-action-btn">
                Limpar
              </button>
            </div>
            <div class="filter-options">
              <label
                v-for="municipio in availableMunicipios"
                :key="municipio"
                class="filter-option"
              >
                <input
                  type="checkbox"
                  :value="municipio"
                  v-model="selectedMunicipios"
                  class="filter-checkbox"
                />
                <span>{{ municipio }}</span>
              </label>
            </div>
          </div>
        </div>
      </div>
    </div>

    <div v-if="loading" class="chart-loading">
      <i class="pi pi-spin pi-spinner"></i>
      <p>Carregando dados...</p>
    </div>

    <div v-else-if="error" class="chart-error">
      <i class="pi pi-exclamation-triangle"></i>
      <p>{{ error }}</p>
      <button @click="loadData" class="retry-button">Tentar novamente</button>
    </div>

    <div v-else-if="chartData.length === 0" class="chart-empty">
      <i class="pi pi-chart-bar"></i>
      <p>Nenhum dado disponível</p>
    </div>

    <apexchart
      v-else
      type="bar"
      height="350"
      :options="chartOptions"
      :series="series"
    />
  </div>
</template>

<script setup lang="ts">
import { ref, computed, onMounted, watch } from 'vue'
import { getPropriedadesPorMunicipio, type PropriedadePorMunicipio } from '@/services/reportService'
import type { ApexOptions } from 'apexcharts'

const chartData = ref<PropriedadePorMunicipio[]>([])
const loading = ref(false)
const error = ref<string | null>(null)
const selectedUfs = ref<string[]>([])
const selectedMunicipios = ref<string[]>([])
const showUfFilters = ref(false)
const showMunicipioFilters = ref(false)

const availableUfs = computed(() => {
  const ufs = new Set(chartData.value.map(item => item.uf))
  return Array.from(ufs).sort()
})

const availableMunicipios = computed(() => {
  // If UFs are selected, only show municipalities from selected UFs
  if (selectedUfs.value.length > 0) {
    const municipios = chartData.value
      .filter(item => selectedUfs.value.includes(item.uf))
      .map(item => item.municipio)
    return Array.from(new Set(municipios)).sort()
  }

  // Otherwise show all municipalities
  const municipios = chartData.value.map(item => item.municipio)
  return Array.from(new Set(municipios)).sort()
})

const filteredData = computed(() => {
  let data = chartData.value

  // Filter by selected UFs
  if (selectedUfs.value.length > 0) {
    data = data.filter(item => selectedUfs.value.includes(item.uf))
  }

  // Filter by selected Municípios
  if (selectedMunicipios.value.length > 0) {
    data = data.filter(item => selectedMunicipios.value.includes(item.municipio))
  }

  return data
})

function selectAllUfs() {
  selectedUfs.value = [...availableUfs.value]
}

function clearUfs() {
  selectedUfs.value = []
  // Clear município selection when UF selection changes
  selectedMunicipios.value = []
}

function selectAllMunicipios() {
  selectedMunicipios.value = [...availableMunicipios.value]
}

function clearMunicipios() {
  selectedMunicipios.value = []
}

const series = computed(() => [{
  name: 'Propriedades',
  data: filteredData.value.map(item => item.total)
}])

const chartOptions = computed<ApexOptions>(() => ({
  chart: {
    type: 'bar',
    toolbar: {
      show: true,
      tools: {
        download: true,
        zoom: true,
        zoomin: true,
        zoomout: true,
        pan: true,
        reset: true
      }
    },
    animations: {
      enabled: true,
      easing: 'easeinout',
      speed: 800
    }
  },
  plotOptions: {
    bar: {
      horizontal: false,
      columnWidth: '55%',
      borderRadius: 4,
      dataLabels: {
        position: 'top'
      }
    }
  },
  dataLabels: {
    enabled: true,
    offsetY: -20,
    style: {
      fontSize: '12px',
      colors: [document.documentElement.classList.contains('dark') ? '#ffffff' : '#304758']
    }
  },
  xaxis: {
    categories: filteredData.value.map(item =>
      selectedUfs.value.length === 1 ? item.municipio : `${item.municipio} - ${item.uf}`
    ),
    labels: {
      rotate: -45,
      rotateAlways: false,
      style: {
        fontSize: '11px'
      }
    }
  },
  yaxis: {
    title: {
      text: 'Número de Propriedades'
    }
  },
  colors: ['#22c55e'],
  tooltip: {
    y: {
      formatter: (val: number) => `${val} propriedades`
    }
  },
  grid: {
    borderColor: '#e7e7e7',
    strokeDashArray: 4
  },
  theme: {
    mode: document.documentElement.classList.contains('dark') ? 'dark' : 'light'
  }
}))

async function loadData() {
  loading.value = true
  error.value = null

  try {
    // Busca todos os dados sem filtro - o filtro é aplicado no frontend
    chartData.value = await getPropriedadesPorMunicipio()
  } catch (e) {
    error.value = 'Erro ao carregar dados. Tente novamente.'
    console.error('Erro ao carregar propriedades por município:', e)
  } finally {
    loading.value = false
  }
}

onMounted(() => {
  loadData()
})

// Watch for theme changes
watch(() => document.documentElement.classList.contains('dark'), () => {
  // Force chart refresh on theme change
  loadData()
})
</script>

<style scoped>
.chart-container {
  background: white;
  border-radius: 8px;
  padding: 1.5rem;
  box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
}

.dark .chart-container {
  background: #1f2937;
}

.chart-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 1.5rem;
  flex-wrap: wrap;
  gap: 1rem;
}

.chart-title {
  font-size: 1.125rem;
  font-weight: 600;
  color: #111827;
}

.dark .chart-title {
  color: #f9fafb;
}

.chart-filters {
  display: flex;
  gap: 0.75rem;
  flex-wrap: wrap;
}

.chart-filter {
  padding: 0.5rem 1rem;
  border: 1px solid #d1d5db;
  border-radius: 6px;
  background: white;
  color: #374151;
  font-size: 0.875rem;
  cursor: pointer;
  transition: all 0.2s;
}

.chart-filter:hover:not(:disabled) {
  border-color: #9ca3af;
}

.chart-filter:disabled {
  opacity: 0.5;
  cursor: not-allowed;
}

.dark .chart-filter {
  background: #374151;
  border-color: #4b5563;
  color: #f9fafb;
}

.chart-loading,
.chart-error,
.chart-empty {
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  min-height: 350px;
  gap: 1rem;
}

.chart-loading i,
.chart-error i,
.chart-empty i {
  font-size: 3rem;
  color: #9ca3af;
}

.chart-loading p,
.chart-error p,
.chart-empty p {
  color: #6b7280;
  font-size: 1rem;
}

.dark .chart-loading p,
.dark .chart-error p,
.dark .chart-empty p {
  color: #9ca3af;
}

.chart-error i {
  color: #ef4444;
}

.retry-button {
  padding: 0.5rem 1rem;
  background: #3b82f6;
  color: white;
  border: none;
  border-radius: 6px;
  cursor: pointer;
  font-size: 0.875rem;
  transition: background 0.2s;
}

.retry-button:hover {
  background: #2563eb;
}

/* Filter Dropdown Styles */
.chart-filters-wrapper {
  display: flex;
  gap: 0.75rem;
  flex-wrap: wrap;
}

.chart-filters {
  position: relative;
}

.filter-toggle-button {
  display: flex;
  align-items: center;
  gap: 0.5rem;
  padding: 0.5rem 1rem;
  background: white;
  border: 1px solid #d1d5db;
  border-radius: 6px;
  color: #374151;
  font-size: 0.875rem;
  cursor: pointer;
  transition: all 0.2s;
}

.filter-toggle-button:hover {
  border-color: #3b82f6;
  background: #f9fafb;
}

.dark .filter-toggle-button {
  background: #374151;
  border-color: #4b5563;
  color: #f9fafb;
}

.dark .filter-toggle-button:hover {
  border-color: #3b82f6;
  background: #4b5563;
}

.filter-count {
  font-weight: 600;
  color: #3b82f6;
}

.filter-dropdown {
  position: absolute;
  top: 100%;
  right: 0;
  margin-top: 0.5rem;
  background: white;
  border: 1px solid #d1d5db;
  border-radius: 8px;
  box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
  min-width: 250px;
  max-height: 400px;
  overflow-y: auto;
  z-index: 50;
}

.dark .filter-dropdown {
  background: #374151;
  border-color: #4b5563;
}

.filter-actions {
  display: flex;
  gap: 0.5rem;
  padding: 0.75rem;
  border-bottom: 1px solid #e5e7eb;
}

.dark .filter-actions {
  border-bottom-color: #4b5563;
}

.filter-action-btn {
  flex: 1;
  padding: 0.375rem 0.75rem;
  background: #f3f4f6;
  border: none;
  border-radius: 4px;
  font-size: 0.75rem;
  cursor: pointer;
  transition: background 0.2s;
}

.filter-action-btn:hover {
  background: #e5e7eb;
}

.dark .filter-action-btn {
  background: #4b5563;
  color: #f9fafb;
}

.dark .filter-action-btn:hover {
  background: #6b7280;
}

.filter-options {
  padding: 0.5rem;
}

.filter-option {
  display: flex;
  align-items: center;
  gap: 0.5rem;
  padding: 0.5rem;
  cursor: pointer;
  border-radius: 4px;
  transition: background 0.2s;
}

.filter-option:hover {
  background: #f3f4f6;
}

.dark .filter-option:hover {
  background: #4b5563;
}

.filter-checkbox {
  width: 1rem;
  height: 1rem;
  cursor: pointer;
}

.filter-option span {
  font-size: 0.875rem;
  color: #374151;
}

.dark .filter-option span {
  color: #f9fafb;
}
</style>
