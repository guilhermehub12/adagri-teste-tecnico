import { createRouter, createWebHistory, type RouteRecordRaw } from 'vue-router'
import { useAuthStore } from '@/stores/auth'
import LoginView from '@/views/Auth/LoginView.vue'
import RegisterView from '@/views/Auth/RegisterView.vue'
import DefaultLayout from '@/layouts/DefaultLayout.vue'
import DashboardView from '@/views/DashboardView.vue'

const routes: RouteRecordRaw[] = [
  {
    path: '/login',
    name: 'login',
    component: LoginView,
    meta: { requiresAuth: false }
  },
  {
    path: '/register',
    name: 'register',
    component: RegisterView,
    meta: { requiresAuth: false }
  },
  {
    path: '/',
    component: DefaultLayout,
    meta: { requiresAuth: true },
    children: [
      {
        path: '',
        redirect: '/dashboard'
      },
      {
        path: 'dashboard',
        name: 'dashboard',
        component: DashboardView
      },
      {
        path: 'produtores',
        name: 'produtores',
        component: () => import('@/views/Produtores/ProdutoresListView.vue')
      },
      {
        path: 'produtores/novo',
        name: 'produtores-create',
        component: () => import('@/views/Produtores/ProdutorForm.vue')
      },
      {
        path: 'produtores/:id/editar',
        name: 'produtores-edit',
        component: () => import('@/views/Produtores/ProdutorForm.vue'),
        props: true
      },
      {
        path: 'propriedades',
        name: 'propriedades',
        component: () => import('@/views/Propriedades/PropriedadesListView.vue')
      },
      {
        path: 'propriedades/novo',
        name: 'propriedades-create',
        component: () => import('@/views/Propriedades/PropriedadeForm.vue')
      },
      {
        path: 'propriedades/:id/editar',
        name: 'propriedades-edit',
        component: () => import('@/views/Propriedades/PropriedadeForm.vue'),
        props: true
      },
      {
        path: 'rebanhos',
        name: 'rebanhos',
        component: () => import('@/views/Rebanhos/RebanhosListView.vue')
      },
      {
        path: 'rebanhos/novo',
        name: 'rebanhos-create',
        component: () => import('@/views/Rebanhos/RebanhoForm.vue')
      },
      {
        path: 'rebanhos/:id/editar',
        name: 'rebanhos-edit',
        component: () => import('@/views/Rebanhos/RebanhoForm.vue'),
        props: true
      },
      {
        path: 'unidades-producao',
        name: 'unidades-producao',
        component: () => import('@/views/UnidadesProducao/UnidadesProducaoListView.vue')
      },
      {
        path: 'unidades-producao/novo',
        name: 'unidades-producao-create',
        component: () => import('@/views/UnidadesProducao/UnidadeProducaoForm.vue')
      },
      {
        path: 'unidades-producao/:id/editar',
        name: 'unidades-producao-edit',
        component: () => import('@/views/UnidadesProducao/UnidadeProducaoForm.vue'),
        props: true
      },
      {
        path: 'relatorios',
        name: 'relatorios',
        component: () => import('@/views/Relatorios/RelatoriosView.vue')
      },
      {
        path: 'usuarios',
        name: 'usuarios',
        component: () => import('@/views/Usuarios/UsuariosListView.vue')
      },
      {
        path: 'usuarios/novo',
        name: 'usuarios-create',
        component: () => import('@/views/Usuarios/UsuarioForm.vue')
      },
      {
        path: 'usuarios/:id/editar',
        name: 'usuarios-edit',
        component: () => import('@/views/Usuarios/UsuarioForm.vue'),
        props: true
      },
      {
        path: 'configuracoes',
        name: 'configuracoes',
        component: () => import('@/views/Configuracoes/ConfiguracoesView.vue')
      }
    ]
  }
]

const router = createRouter({
  history: createWebHistory(import.meta.env.BASE_URL),
  routes
})

// Navigation Guard
router.beforeEach(async (to, _from, next) => {
  const authStore = useAuthStore()

  // Check if auth status is resolved
  if (!authStore.isAuthResolved) {
    await authStore.checkAuth()
  }

  const requiresAuth = to.matched.some(record => record.meta.requiresAuth)

  if (requiresAuth && !authStore.isAuthenticated) {
    // Redirect to login if not authenticated
    next({ name: 'login' })
  } else if ((to.name === 'login' || to.name === 'register') && authStore.isAuthenticated) {
    // Redirect to dashboard if already authenticated and trying to access login/register
    next({ name: 'dashboard' })
  } else {
    next()
  }
})

export default router
