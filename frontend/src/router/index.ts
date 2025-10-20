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
        path: 'produtores-rurais/novo',
        name: 'produtores-rurais-create',
        component: () => import('@/views/ProdutoresRurais/ProdutorRuralCreateView.vue')
      },
      {
        path: 'propriedades/novo',
        name: 'propriedades-create',
        component: () => import('@/views/Propriedades/PropriedadeCreateView.vue')
      },
      {
        path: 'unidades-producao/novo',
        name: 'unidades-producao-create',
        component: () => import('@/views/UnidadesProducao/UnidadeProducaoCreateView.vue')
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
