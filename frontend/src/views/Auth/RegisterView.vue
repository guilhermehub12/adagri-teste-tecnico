<template>
  <div class="login-container">
    <div class="login-background">
      <div class="bg-circle circle-1"></div>
      <div class="bg-circle circle-2"></div>
      <div class="bg-circle circle-3"></div>
      <div class="bg-line line-1"></div>
      <div class="bg-line line-2"></div>
    </div>

    <div class="login-card">
      <div class="login-logo">
        <div class="logo-icon">
          <i class="pi pi-user-plus"></i>
        </div>
      </div>

      <div class="login-header">
        <h1 class="login-title">Criar Nova Conta</h1>
        <p class="login-subtitle">Junte-se à nossa plataforma</p>
      </div>

      <form @submit.prevent="handleSubmit" class="login-form">
        <div class="form-group">
          <label for="name" class="form-label">Nome</label>
          <InputText id="name" v-model="formData.name" class="form-input" :class="{ 'p-invalid': errors.name }" placeholder="Seu nome completo" />
          <small v-if="errors.name" class="form-error">{{ errors.name }}</small>
        </div>

        <div class="form-group">
          <label for="email" class="form-label">Email</label>
          <InputText id="email" v-model="formData.email" class="form-input" :class="{ 'p-invalid': errors.email }" placeholder="seu.email@exemplo.com" />
          <small v-if="errors.email" class="form-error">{{ errors.email }}</small>
        </div>

        <div class="form-group">
          <label for="password" class="form-label">Senha</label>
          <Password id="password" v-model="formData.password" class="w-full" :class="{ 'p-invalid': errors.password }" inputClass="form-input" placeholder="Senha (mín. 8 caracteres)" :feedback="false" toggleMask />
          <small v-if="errors.password" class="form-error">{{ errors.password }}</small>
        </div>

        <div class="form-group">
          <label for="password_confirmation" class="form-label">Confirmar Senha</label>
          <Password id="password_confirmation" v-model="formData.password_confirmation" class="w-full" :class="{ 'p-invalid': errors.password_confirmation }" inputClass="form-input" placeholder="Confirme sua senha" :feedback="false" toggleMask />
          <small v-if="errors.password_confirmation" class="form-error">{{ errors.password_confirmation }}</small>
        </div>

        <Message v-if="errorMessage" severity="error" :closable="false" class="login-error">
          {{ errorMessage }}
        </Message>

        <Button type="submit" :loading="loading" label="Criar Conta" class="login-submit p-button-success" />
      </form>

      <div class="login-footer">
        <p>Já tem uma conta? <RouterLink to="/login" class="text-green-400 hover:text-green-300">Faça login</RouterLink></p>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, reactive } from 'vue'
import { useRouter, RouterLink } from 'vue-router'
import { useAuthStore } from '@/stores/auth'
import InputText from 'primevue/inputtext'
import Password from 'primevue/password'
import Button from 'primevue/button'
import Message from 'primevue/message'

const formData = reactive({
  name: '',
  email: '',
  password: '',
  password_confirmation: ''
})

const errors = reactive({
  name: '',
  email: '',
  password: '',
  password_confirmation: ''
})

const loading = ref(false)
const errorMessage = ref('')

const router = useRouter()
const authStore = useAuthStore()

function validateForm(): boolean {
  errors.name = ''
  errors.email = ''
  errors.password = ''
  errors.password_confirmation = ''
  errorMessage.value = ''

  let isValid = true

  if (!formData.name) {
    errors.name = 'Nome é obrigatório'
    isValid = false
  }

  if (!formData.email) {
    errors.email = 'Email é obrigatório'
    isValid = false
  } else if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(formData.email)) {
    errors.email = 'Email inválido'
    isValid = false
  }

  if (!formData.password) {
    errors.password = 'Senha é obrigatória'
    isValid = false
  } else if (formData.password.length < 8) {
    errors.password = 'A senha deve ter no mínimo 8 caracteres'
    isValid = false
  }

  if (formData.password !== formData.password_confirmation) {
    errors.password_confirmation = 'As senhas não conferem'
    isValid = false
  }

  return isValid
}

async function handleSubmit() {
  if (!validateForm()) {
    return
  }

  loading.value = true
  errorMessage.value = ''

  try {
    await authStore.register(formData)
    router.push('/')
  } catch (error: any) {
    errorMessage.value = error.response?.data?.message || 'Erro ao criar conta'
  } finally {
    loading.value = false
  }
}
</script>
