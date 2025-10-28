<template>
  <div>
    <h1 class="text-2xl font-bold mb-4">Configurações</h1>

    <div v-if="configuracaoStore.loading">Carregando...</div>

    <div v-if="configuracaoStore.profile">
      <div class="p-fluid">
        <div class="p-field">
          <label for="name">Nome</label>
          <InputText id="name" :value="configuracaoStore.profile.name" readonly class="readonly-input" />
        </div>
        <div class="p-field">
          <label for="email">Email</label>
          <InputText id="email" :value="configuracaoStore.profile.email" readonly class="readonly-input" />
        </div>
        <div class="p-field">
          <label for="role">Perfil</label>
          <InputText id="role" :value="configuracaoStore.profile.role" readonly class="readonly-input" />
        </div>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { onMounted } from 'vue';
import { useConfiguracaoStore } from '@/stores/configuracao';
import InputText from 'primevue/inputtext';

const configuracaoStore = useConfiguracaoStore();

onMounted(() => {
  configuracaoStore.fetchProfile();
});
</script>
