<template>
  <header class="app-header">
    <div class="header-container">
      <!-- Left Side: Menu Toggle + Navigation -->
      <div class="header-left">
        <!-- Mobile Menu Toggle -->
        <button
          @click="$emit('toggle-sidebar')"
          class="header-menu-toggle lg:hidden"
          aria-label="Toggle menu"
        >
          <i class="pi pi-bars"></i>
        </button>

        <!-- Navigation Breadcrumb -->
        <Breadcrumb :home="home" :model="breadcrumbItems" class="header-breadcrumb">
          <template #item="{ item }">
            <router-link v-if="item.route" :to="item.route" class="breadcrumb-link">
              <i v-if="item.icon" :class="item.icon" class="breadcrumb-icon"></i>
              <span>{{ item.label }}</span>
            </router-link>
            <span v-else class="breadcrumb-current">
              <i v-if="item.icon" :class="item.icon" class="breadcrumb-icon"></i>
              <span>{{ item.label }}</span>
            </span>
          </template>
        </Breadcrumb>
      </div>

      <!-- Right Side: Actions -->
      <div class="header-right">
        <!-- Theme Toggle -->
        <button @click="themeStore.toggle" class="header-icon-button" aria-label="Alternar tema">
          <i :class="themeStore.isDark ? 'pi pi-moon' : 'pi pi-sun'"></i>
        </button>

        <!-- User Menu -->
        <div class="header-user">
          <Avatar
            :label="getInitials(authStore.user?.name)"
            class="user-avatar"
            shape="circle"
            size="large"
          />
          <div class="user-info hidden md:block">
            <p class="user-name">{{ authStore.user?.name }}</p>
            <p class="user-role">{{ authStore.user?.role }}</p>
          </div>
          <button
            @click="handleLogout"
            class="logout-button"
            aria-label="Sair do sistema"
          >
            <i class="pi pi-sign-out"></i>
          </button>
        </div>
      </div>
    </div>
  </header>
</template>

<script setup lang="ts">
import { computed } from 'vue'
import { useRouter, useRoute } from 'vue-router'
import Avatar from 'primevue/avatar'
import Breadcrumb from 'primevue/breadcrumb'
import { useAuthStore } from '@/stores/auth'
import { useThemeStore } from '@/stores/theme'

defineEmits(['toggle-sidebar'])

const router = useRouter()
const route = useRoute()
const authStore = useAuthStore()
const themeStore = useThemeStore()

// Breadcrumb home item
const home = {
  icon: 'pi pi-home',
  route: '/dashboard',
  label: 'Dashboard'
}

// Mapeamento de rotas para breadcrumbs
const routeBreadcrumbMap: Record<string, { label: string; icon?: string }[]> = {
  '/dashboard': [],
  '/produtores-rurais/novo': [
    { label: 'Produtores Rurais', icon: 'pi pi-users' },
    { label: 'Novo Produtor' }
  ],
  '/propriedades/novo': [
    { label: 'Propriedades', icon: 'pi pi-home' },
    { label: 'Nova Propriedade' }
  ],
  '/unidades-producao/novo': [
    { label: 'Unidades de Produção', icon: 'pi pi-file-edit' },
    { label: 'Nova Unidade de Produção' }
  ]
}

// Breadcrumb items dinâmicos baseados na rota atual
const breadcrumbItems = computed(() => {
  const currentPath = route.path
  const items = routeBreadcrumbMap[currentPath] || []

  return items.map((item, index) => ({
    label: item.label,
    icon: item.icon,
    // Apenas o último item não tem rota (é o item atual)
    route: index < items.length - 1 ? undefined : undefined
  }))
})

function getInitials(name: string | undefined): string {
  if (!name) return '?'
  return name
    .split(' ')
    .map(word => word[0])
    .join('')
    .toUpperCase()
    .substring(0, 2)
}

function handleLogout() {
  authStore.logout()
  router.push('/login')
}
</script>
