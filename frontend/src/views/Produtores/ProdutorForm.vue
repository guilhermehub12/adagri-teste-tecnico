<template>
  <form @submit.prevent="submit">
    <div class="p-fluid">
      <div class="p-field">
        <label for="nome">Nome</label>
        <InputText id="nome" v-model="v$.nome.$model" :class="{ 'p-invalid': v$.nome.$error }" />
        <small v-if="v$.nome.$error" class="p-error">{{ v$.nome.$errors[0].$message }}</small>
      </div>
      <div class="p-field">
        <label for="cpf_cnpj">CPF/CNPJ</label>
        <InputText id="cpf_cnpj" v-model="v$.cpf_cnpj.$model" :class="{ 'p-invalid': v$.cpf_cnpj.$error }" />
        <small v-if="v$.cpf_cnpj.$error" class="p-error">{{ v$.cpf_cnpj.$errors[0].$message }}</small>
      </div>
      <div class="p-field">
        <label for="telefone">Telefone</label>
        <InputText id="telefone" v-model="form.telefone" />
      </div>
      <div class="p-field">
        <label for="email">Email</label>
        <InputText id="email" v-model="v$.email.$model" :class="{ 'p-invalid': v$.email.$error }" />
        <small v-if="v$.email.$error" class="p-error">{{ v$.email.$errors[0].$message }}</small>
      </div>
      <div class="p-field">
        <label for="endereco">Endereço</label>
        <InputText id="endereco" v-model="form.endereco" />
      </div>
    </div>
    <Button type="submit" label="Salvar" class="mt-4" :disabled="v$.$invalid" />
  </form>
</template>

<script setup lang="ts">
import { ref, watch, computed } from 'vue';
import InputText from 'primevue/inputtext';
import Button from 'primevue/button';
import type { ProdutorRural } from '@/services/produtorService';
import { useVuelidate } from '@vuelidate/core';
import { required, email } from '@vuelidate/validators';

const props = defineProps<{ produtor?: ProdutorRural | null }>();
const emit = defineEmits(['submit']);

const form = ref<Partial<ProdutorRural>>({ ...props.produtor });

const rules = computed(() => ({
  nome: { required },
  cpf_cnpj: { required },
  email: { required, email },
}));

const v$ = useVuelidate(rules, form);

watch(() => props.produtor, (newVal) => {
  form.value = { ...newVal };
});

const submit = () => {
  v$.value.$touch();
  if (!v$.value.$invalid) {
    emit('submit', form.value);
  }
};
</script>
