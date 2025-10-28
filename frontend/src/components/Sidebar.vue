<template>
  <!-- Overlay for mobile -->
  <div
    v-if="isOpen"
    @click="$emit('close')"
    class="fixed inset-0 bg-black bg-opacity-50 z-40 lg:hidden"
    aria-hidden="true"
  ></div>

  <!-- Sidebar -->
  <aside
    :class="[
      'app-sidebar',
      'fixed lg:static inset-y-0 left-0 z-50',
      'transform transition-transform duration-300 ease-in-out',
      isOpen ? 'translate-x-0' : '-translate-x-full lg:translate-x-0'
    ]"
  >
    <!-- Logo -->
    <div class="sidebar-header">
      <div class="sidebar-logo">
        <i class="pi pi-sitemap" style="font-size: 2rem"></i>
        <span class="logo-text">ADAGRI</span>
      </div>
      <button @click="$emit('close')" class="sidebar-close-button lg:hidden" aria-label="Fechar menu">
        <i class="pi pi-times"></i>
      </button>
    </div>

    <!-- Navigation -->
    <nav class="sidebar-nav">
      <!-- Menu Principal Section -->
      <div class="nav-section">
        <h3 class="nav-section-title">Menu Principal</h3>
        <ul class="nav-list">
          <li>
            <RouterLink
              to="/dashboard"
              @click="$emit('close')"
              class="nav-item"
              active-class="active"
            >
              <i class="pi pi-home nav-icon"></i>
              <span class="nav-label">Visão Geral</span>
            </RouterLink>
          </li>
          <li>
            <RouterLink
              to="/produtores"
              @click="$emit('close')"
              class="nav-item"
              :class="{ active: isRouteActive('/produtores') }"
            >
              <i class="pi pi-users nav-icon"></i>
              <span class="nav-label">Produtores</span>
            </RouterLink>
          </li>
          <li>
            <RouterLink
              to="/propriedades"
              @click="$emit('close')"
              class="nav-item"
              :class="{ active: isRouteActive('/propriedades') }"
            >
              <i class="pi pi-map-marker nav-icon"></i>
              <span class="nav-label">Propriedades</span>
            </RouterLink>
          </li>
          <li>
            <RouterLink
              to="/rebanhos"
              @click="$emit('close')"
              class="nav-item"
              :class="{ active: isRouteActive('/rebanhos') }"
            >
              <i class="pi pi-server nav-icon"></i>
              <span class="nav-label">Rebanhos</span>
            </RouterLink>
          </li>
          <li>
            <RouterLink
              to="/unidades-producao"
              @click="$emit('close')"
              class="nav-item"
              :class="{ active: isRouteActive('/unidades-producao') }"
            >
              <i class="pi pi-th-large nav-icon"></i>
              <span class="nav-label">Unidades de Produção</span>
            </RouterLink>
          </li>
        </ul>
      </div>

      <!-- Gestão Section -->
      <div class="nav-section">
        <h3 class="nav-section-title">Gestão</h3>
        <ul class="nav-list">
          <li>
            <RouterLink
              to="/relatorios"
              @click="$emit('close')"
              class="nav-item"
              :class="{ active: isRouteActive('/relatorios') }"
            >
              <i class="pi pi-chart-bar nav-icon"></i>
              <span class="nav-label">Relatórios</span>
            </RouterLink>
          </li>
        </ul>
      </div>

      <!-- Sistema Section -->
      <div class="nav-section">
        <h3 class="nav-section-title">Sistema</h3>
        <ul class="nav-list">
          <li>
            <RouterLink
              to="/usuarios"
              @click="$emit('close')"
              class="nav-item"
              :class="{ active: isRouteActive('/usuarios') }"
            >
              <i class="pi pi-users nav-icon"></i>
              <span class="nav-label">Usuários</span>
            </RouterLink>
          </li>
          <li>
            <RouterLink
              to="/configuracoes"
              @click="$emit('close')"
              class="nav-item"
              :class="{ active: isRouteActive('/configuracoes') }"
            >
              <i class="pi pi-cog nav-icon"></i>
              <span class="nav-label">Configurações</span>
            </RouterLink>
          </li>
        </ul>
      </div>
    </nav>

    <!-- Sidebar Footer -->
    <div class="sidebar-footer">
      <div class="footer-content">
        <p class="footer-text">&copy; 2025 ADAGRI</p>
        <p class="footer-version">v1.0.0</p>
      </div>
    </div>
  </aside>
</template>

<script setup lang="ts">
import { RouterLink, useRoute } from 'vue-router'

defineProps<{
  isOpen: boolean
}>()

defineEmits(['close'])

const route = useRoute()

/**
 * Verifica se a rota atual pertence ao módulo especificado
 * Exemplo: /produtores-rurais/novo deve marcar /produtores como ativo
 */
function isRouteActive(basePath: string): boolean {
  const currentPath = route.path

  // Mapeamento de rotas base para suas variações
  const routeMap: Record<string, string[]> = {
    '/dashboard': ['/dashboard'],
    '/produtores': ['/produtores', '/produtores-rurais'],
    '/propriedades': ['/propriedades'],
    '/rebanhos': ['/rebanhos'],
    '/unidades-producao': ['/unidades-producao'],
    '/relatorios': ['/relatorios'],
    '/usuarios': ['/usuarios'],
    '/configuracoes': ['/configuracoes']
  }

  const variations = routeMap[basePath] || [basePath]

  // Verifica se a rota atual começa com alguma variação
  return variations.some(variation => currentPath.startsWith(variation))
}
</script>
