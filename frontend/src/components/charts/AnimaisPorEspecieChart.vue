<template>
  <div class="chart-container">
    <div class="chart-header">
      <h3 class="chart-title">Animais por Espécie</h3>
      <div class="chart-filters">
        <button
          @click="showFilters = !showFilters"
          class="filter-toggle-button"
        >
          <i class="pi pi-filter"></i>
          Filtros
          <span v-if="selectedEspecies.length > 0" class="filter-count">
            ({{ selectedEspecies.length }})
          </span>
        </button>
        <div v-if="showFilters" class="filter-dropdown">
          <div class="filter-actions">
            <button @click="selectAllEspecies" class="filter-action-btn">
              Selecionar Todos
            </button>
            <button @click="clearEspecies" class="filter-action-btn">
              Limpar
            </button>
          </div>
          <div class="filter-options">
            <label
              v-for="especie in availableEspecies"
              :key="especie"
              class="filter-option"
            >
              <input
                type="checkbox"
                :value="especie"
                v-model="selectedEspecies"
                class="filter-checkbox"
              />
              <span>{{ especie }}</span>
            </label>
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
      <i class="pi pi-chart-pie"></i>
      <p>Nenhum dado disponível</p>
    </div>

    <apexchart
      v-else
      type="donut"
      height="400"
      :options="chartOptions"
      :series="series"
    />
  </div>
</template>

<script setup lang="ts">
import { ref, computed, onMounted, watch } from 'vue'
import { getAnimaisPorEspecie, type AnimalPorEspecie } from '@/services/reportService'
import type { ApexOptions } from 'apexcharts'

const chartData = ref<AnimalPorEspecie[]>([])
const loading = ref(false)
const error = ref<string | null>(null)
const selectedEspecies = ref<string[]>([])
const showFilters = ref(false)

const availableEspecies = computed(() => {
  const especies = chartData.value.map(item => item.especie)
  return Array.from(new Set(especies)).sort()
})

const filteredData = computed(() => {
  if (selectedEspecies.value.length === 0) return chartData.value
  return chartData.value.filter(item => selectedEspecies.value.includes(item.especie))
})

function selectAllEspecies() {
  selectedEspecies.value = [...availableEspecies.value]
}

function clearEspecies() {
  selectedEspecies.value = []
}

const series = computed(() => filteredData.value.map(item => item.total))

const chartOptions = computed<ApexOptions>(() => ({
  chart: {
    type: 'donut',
    toolbar: {
      show: true,
      tools: {
        download: true
      }
    },
    animations: {
      enabled: true,
      easing: 'easeinout',
      speed: 800
    }
  },
  labels: filteredData.value.map(item => item.especie),
  colors: ['#3b82f6', '#22c55e', '#f59e0b', '#ef4444', '#8b5cf6', '#ec4899'],
  dataLabels: {
    enabled: true,
    formatter: (val: number) => `${val.toFixed(1)}%`
  },
  plotOptions: {
    pie: {
      donut: {
        size: '65%',
        labels: {
          show: true,
          total: {
            show: true,
            label: 'Total de Animais',
            fontSize: '16px',
            fontWeight: 600,
            formatter: () => {
              const total = filteredData.value.reduce((sum, item) => sum + item.total, 0)
              return total.toLocaleString('pt-BR')
            }
          },
          value: {
            fontSize: '22px',
            fontWeight: 700
          }
        }
      }
    }
  },
  legend: {
    position: 'bottom',
    horizontalAlign: 'center',
    fontSize: '14px',
    markers: {
      size: 8,
      shape: 'circle'
    },
    itemMargin: {
      horizontal: 10,
      vertical: 5
    }
  },
  tooltip: {
    y: {
      formatter: (val: number) => `${val.toLocaleString('pt-BR')} animais`
    }
  },
  responsive: [{
    breakpoint: 480,
    options: {
      chart: {
        height: 300
      },
      legend: {
        position: 'bottom'
      }
    }
  }],
  theme: {
    mode: document.documentElement.classList.contains('dark') ? 'dark' : 'light'
  }
}))

async function loadData() {
  loading.value = true
  error.value = null

  try {
    // Busca todos os dados sem filtro - o filtro é aplicado no frontend
    chartData.value = await getAnimaisPorEspecie()
  } catch (e) {
    error.value = 'Erro ao carregar dados. Tente novamente.'
    console.error('Erro ao carregar animais por espécie:', e)
  } finally {
    loading.value = false
  }
}

onMounted(() => {
  loadData()
})

// Watch for theme changes
watch(() => document.documentElement.classList.contains('dark'), () => {
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
  min-height: 400px;
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
