<template>
  <div class="login-container">
    <!-- Background decorativo -->
    <div class="login-background">
      <div class="bg-circle circle-1"></div>
      <div class="bg-circle circle-2"></div>
      <div class="bg-circle circle-3"></div>
      <div class="bg-line line-1"></div>
      <div class="bg-line line-2"></div>
    </div>

    <!-- Card de Login -->
    <div class="login-card">
      <!-- Logo/Ícone -->
      <div class="login-logo">
        <div class="logo-icon">
          <i class="pi pi-sitemap"></i>
        </div>
      </div>

      <!-- Título -->
      <div class="login-header">
        <h1 class="login-title">Sistema Agropecuário</h1>
        <p class="login-subtitle">Faça login para continuar</p>
      </div>

      <!-- Formulário -->
      <form @submit.prevent="handleSubmit" class="login-form">
        <!-- Email -->
        <div class="form-group">
          <label for="email-address" class="form-label">
            Email ou usuário
          </label>
          <InputText
            id="email-address"
            v-model="formData.email"
            type="email"
            placeholder="email@exemplo.comm"
            class="form-input"
            :class="{ 'p-invalid': errors.email }"
          />
          <small v-if="errors.email" class="form-error">
            {{ errors.email }}
          </small>
        </div>

        <!-- Senha -->
        <div class="form-group">
          <label for="password" class="form-label">
            Senha
          </label>
          <Password
            id="password"
            v-model="formData.password"
            placeholder="senha123@"
            :feedback="false"
            toggleMask
            class="w-full"
            :class="{ 'p-invalid': errors.password }"
            inputClass="form-input"
          />
          <small v-if="errors.password" class="form-error">
            {{ errors.password }}
          </small>
        </div>

        <!-- Error Message -->
        <Message v-if="errorMessage" severity="error" :closable="false" class="login-error">
          {{ errorMessage }}
        </Message>

        <!-- Submit Button -->
        <Button
          type="submit"
          :loading="loading"
          label="Entrar"
          icon="pi pi-sign-in"
          class="login-submit"
          size="large"
        />
      </form>

      <!-- Footer -->
      <div class="login-footer">
        <p>Não tem uma conta? <RouterLink to="/register" class="text-green-400 hover:text-green-300">Crie uma agora</RouterLink></p>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, reactive } from 'vue'
import { useRouter, RouterLink } from 'vue-router'
import InputText from 'primevue/inputtext'
import Password from 'primevue/password'
import Button from 'primevue/button'
import Message from 'primevue/message'
import { useAuthStore } from '@/stores/auth'

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
    await authStore.login(formData)
    router.push('/')
  } catch (error: any) {
    errorMessage.value = error.response?.data?.message || 'Erro ao fazer login'
  } finally {
    loading.value = false
  }
}
</script>
