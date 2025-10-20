<template>
  <form @submit.prevent="submit">
    <div class="p-fluid">
      <div class="p-field">
        <label for="name">Nome</label>
        <InputText id="name" v-model="v$.name.$model" :class="{ 'p-invalid': v$.name.$error }" />
        <small v-if="v$.name.$error" class="p-error">{{ v$.name.$errors[0].$message }}</small>
      </div>
      <div class="p-field">
        <label for="email">Email</label>
        <InputText id="email" v-model="v$.email.$model" :class="{ 'p-invalid': v$.email.$error }" />
        <small v-if="v$.email.$error" class="p-error">{{ v$.email.$errors[0].$message }}</small>
      </div>
      <div class="p-field">
        <label for="password">Senha</label>
        <Password id="password" v-model="form.password" :class="{ 'p-invalid': v$.password.$error }" />
        <small v-if="v$.password.$error" class="p-error">{{ v$.password.$errors[0].$message }}</small>
      </div>
      <div class="p-field">
        <label for="password_confirmation">Confirmar Senha</label>
        <Password id="password_confirmation" v-model="form.password_confirmation" :class="{ 'p-invalid': v$.password_confirmation.$error }" />
        <small v-if="v$.password_confirmation.$error" class="p-error">{{ v$.password_confirmation.$errors[0].$message }}</small>
      </div>
      <div class="p-field">
        <label for="role">Perfil</label>
        <Dropdown id="role" v-model="v$.role.$model" :options="roles" placeholder="Selecione o perfil" :class="{ 'p-invalid': v$.role.$error }" />
        <small v-if="v$.role.$error" class="p-error">{{ v$.role.$errors[0].$message }}</small>
      </div>
    </div>
    <Button type="submit" label="Salvar" class="mt-4" :disabled="v$.$invalid" />
  </form>
</template>

<script setup lang="ts">
import { ref, watch, computed } from 'vue';
import InputText from 'primevue/inputtext';
import Password from 'primevue/password';
import Button from 'primevue/button';
import Dropdown from 'primevue/dropdown';
import type { User } from '@/services/usuarioService';
import { useVuelidate } from '@vuelidate/core';
import { required, email, minLength, sameAs } from '@vuelidate/validators';

const props = defineProps<{ user?: User | null }>();
const emit = defineEmits(['submit']);

const form = ref<Partial<User>>({ ...props.user });

const roles = ref(['admin', 'gestor', 'tecnico', 'extensionista']);

const rules = computed(() => ({
  name: { required },
  email: { required, email },
  password: { required, minLength: minLength(8) },
  password_confirmation: { required, sameAs: sameAs(form.value.password) },
  role: { required },
}));

const v$ = useVuelidate(rules, form);

watch(() => props.user, (newVal) => {
  form.value = { ...newVal };
});

const submit = () => {
  v$.value.$touch();
  if (!v$.value.$invalid) {
    emit('submit', form.value);
  }
};
</script>
