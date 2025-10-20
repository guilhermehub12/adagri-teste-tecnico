<template>
  <div class="dashboard-container">
    <!-- Content Area -->
    <div class="dashboard-content">
      <!-- Welcome Header -->
      <div class="dashboard-header">
        <div>
          <h1 class="dashboard-title">
            Bem-vindo, {{ authStore.user?.name }}!
          </h1>
          <p class="dashboard-subtitle">
            Aqui está um resumo das informações do sistema
          </p>
        </div>
      </div>

      <!-- Loading Skeleton -->
      <div v-if="dashboardStore.loading" class="stats-grid">
        <div v-for="n in 4" :key="n" class="stat-card animate-pulse-soft">
          <div class="h-4 bg-gray-300 dark:bg-gray-700 rounded w-3/4"></div>
          <div class="h-10 bg-gray-300 dark:bg-gray-700 rounded w-1/2 mt-4"></div>
        </div>
      </div>

      <!-- Statistics Cards -->
      <div v-else class="stats-grid">
        <!-- Card: Produtores -->
        <div class="stat-card stat-card-primary">
          <div class="stat-header">
            <span class="stat-label">Produtores Rurais</span>
            <i class="pi pi-users stat-icon-small"></i>
          </div>
          <div class="stat-body">
            <div class="stat-value">{{ dashboardStore.totalProdutores }}</div>
          </div>
        </div>

        <!-- Card: Propriedades -->
        <div class="stat-card stat-card-dark">
          <div class="stat-header">
            <span class="stat-label">Propriedades</span>
            <i class="pi pi-home stat-icon-small"></i>
          </div>
          <div class="stat-body">
            <div class="stat-value">{{ dashboardStore.totalPropriedades }}</div>
          </div>
        </div>

        <!-- Card: Animais -->
        <div class="stat-card stat-card-info">
          <div class="stat-header">
            <span class="stat-label">Animais</span>
            <i class="pi pi-server stat-icon-small"></i>
          </div>
          <div class="stat-body">
            <div class="stat-value">{{ dashboardStore.totalAnimais }}</div>
          </div>
        </div>

        <!-- Card: Hectares -->
        <div class="stat-card stat-card-secondary">
          <div class="stat-header">
            <span class="stat-label">Hectares Cultivados</span>
            <i class="pi pi-th-large stat-icon-small"></i>
          </div>
          <div class="stat-body">
            <div class="stat-value">{{ dashboardStore.totalHectares }}</div>
          </div>
        </div>
      </div>

      <!-- Charts Section -->
      <div class="charts-section">
        <PropriedadesPorMunicipioChart />
        <AnimaisPorEspecieChart />
        <HectaresPorCulturaChart />
      </div>

      <!-- Quick Actions Grid -->
      <div class="actions-grid">
        <div class="action-card" @click="navigateTo('/produtores-rurais/novo')">
          <div class="action-icon bg-green-500">
            <i class="pi pi-user-plus"></i>
          </div>
          <div class="action-content">
            <h4 class="action-title">Novo Produtor</h4>
            <p class="action-description">Cadastrar novo produtor rural</p>
          </div>
          <button class="action-button">
            <i class="pi pi-arrow-right"></i>
          </button>
        </div>

        <div class="action-card" @click="navigateTo('/propriedades/novo')">
          <div class="action-icon bg-blue-500">
            <i class="pi pi-home"></i>
          </div>
          <div class="action-content">
            <h4 class="action-title">Nova Propriedade</h4>
            <p class="action-description">Registrar nova propriedade</p>
          </div>
          <button class="action-button">
            <i class="pi pi-arrow-right"></i>
          </button>
        </div>

        <div class="action-card" @click="navigateTo('/unidades-producao/novo')">
          <div class="action-icon bg-amber-500">
            <i class="pi pi-file-edit"></i>
          </div>
          <div class="action-content">
            <h4 class="action-title">Nova Unidade de Produção</h4>
            <p class="action-description">Criar nova Unidade de Produção</p>
          </div>
          <button class="action-button">
            <i class="pi pi-arrow-right"></i>
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { onMounted } from 'vue'
import { useRouter } from 'vue-router'
import { useAuthStore } from "@/stores/auth";
import { useDashboardStore } from '@/stores/dashboard';
import PropriedadesPorMunicipioChart from '@/components/charts/PropriedadesPorMunicipioChart.vue'
import AnimaisPorEspecieChart from '@/components/charts/AnimaisPorEspecieChart.vue'
import HectaresPorCulturaChart from '@/components/charts/HectaresPorCulturaChart.vue'

const router = useRouter()
const authStore = useAuthStore();
const dashboardStore = useDashboardStore();

onMounted(() => {
  dashboardStore.fetchDashboardData();
});

function navigateTo(path: string) {
  router.push(path)
}
</script>
