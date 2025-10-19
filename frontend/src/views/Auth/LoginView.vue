<template>
  <div class="min-h-screen flex items-center justify-center bg-gray-50 py-12 px-4 sm:px-6 lg:px-8">
    <div class="w-full max-w-2xl space-y-8 bg-white p-12 rounded-lg shadow-md">
      <div>
        <h2 class="mt-6 text-center text-4xl font-extrabold text-gray-900">
          Sistema ADAGRI
        </h2>
        <p class="mt-3 text-center text-lg text-gray-600">
          Faça login para continuar
        </p>
      </div>

      <form class="mt-8 space-y-6" @submit.prevent="handleSubmit">
        <div class="rounded-md shadow-sm -space-y-px">
          <div>
            <label for="email-address" class="sr-only">Email</label>
            <input
              id="email-address"
              v-model="formData.email"
              type="email"
              required
              class="appearance-none rounded-none relative block w-full px-5 py-4 border border-gray-300 placeholder-gray-500 text-gray-900 rounded-t-md focus:outline-none focus:ring-green-500 focus:border-green-500 focus:z-10 text-lg"
              placeholder="Email"
            />
            <div v-if="errors.email" class="text-red-600 text-base mt-2 px-1">
              {{ errors.email }}
            </div>
          </div>

          <div>
            <label for="password" class="sr-only">Senha</label>
            <input
              id="password"
              v-model="formData.password"
              type="password"
              required
              class="appearance-none rounded-none relative block w-full px-5 py-4 border border-gray-300 placeholder-gray-500 text-gray-900 rounded-b-md focus:outline-none focus:ring-green-500 focus:border-green-500 focus:z-10 text-lg"
              placeholder="Senha"
            />
            <div v-if="errors.password" class="text-red-600 text-base mt-2 px-1">
              {{ errors.password }}
            </div>
          </div>
        </div>

        <div v-if="errorMessage" class="text-red-600 text-base text-center font-medium">
          {{ errorMessage }}
        </div>

        <div>
          <button
            type="submit"
            :disabled="loading"
            class="group relative w-full flex justify-center py-4 px-6 border border-transparent text-lg font-semibold rounded-md text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 disabled:opacity-50 disabled:cursor-not-allowed transition-colors"
          >
            {{ loading ? 'Carregando...' : 'Entrar' }}
          </button>
        </div>
      </form>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, reactive } from 'vue'
import { useRouter } from 'vue-router'
import { useAuthStore } from '@/stores/auth'
import { login } from '@/services/api'

const router = useRouter()
const authStore = useAuthStore()

const formData = reactive({
  email: '',
  password: ''
})

const errors = reactive({
  email: '',
  password: ''
})

const loading = ref(false)
const errorMessage = ref('')

function validateForm(): boolean {
  errors.email = ''
  errors.password = ''
  errorMessage.value = ''

  let isValid = true

  if (!formData.email) {
    errors.email = 'Email é obrigatório'
    isValid = false
  } else if (!isValidEmail(formData.email)) {
    errors.email = 'Email deve ser válido'
    isValid = false
  }

  if (!formData.password) {
    errors.password = 'Senha é obrigatória'
    isValid = false
  }

  return isValid
}

function isValidEmail(email: string): boolean {
  const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/
  return emailRegex.test(email)
}

async function handleSubmit() {
  if (!validateForm()) {
    return
  }

  loading.value = true
  errorMessage.value = ''

  try {
    const response = await login(formData)
    authStore.login(response.token, response.user)
    router.push('/')
  } catch (error: any) {
    errorMessage.value = error.response?.data?.message || 'Erro ao fazer login'
  } finally {
    loading.value = false
  }
}
</script>
