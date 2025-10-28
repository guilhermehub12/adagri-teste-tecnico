<template>
  <form @submit.prevent="submit">
    <div class="p-fluid">
      <div class="p-field">
        <label for="especie">Espécie</label>
        <InputText id="especie" v-model="v$.especie.$model" :class="{ 'p-invalid': v$.especie.$error }" />
        <small v-if="v$.especie.$error" class="p-error">{{ v$.especie.$errors[0].$message }}</small>
      </div>
      <div class="p-field">
        <label for="quantidade">Quantidade</label>
        <InputNumber id="quantidade" v-model="v$.quantidade.$model" :class="{ 'p-invalid': v$.quantidade.$error }" />
        <small v-if="v$.quantidade.$error" class="p-error">{{ v$.quantidade.$errors[0].$message }}</small>
      </div>
      <div class="p-field">
        <label for="finalidade">Finalidade</label>
        <InputText id="finalidade" v-model="form.finalidade" />
      </div>
      <div class="p-field">
        <label for="data_atualizacao">Data de Atualização</label>
        <Calendar id="data_atualizacao" v-model="form.data_atualizacao" />
      </div>
      <div class="p-field">
        <label for="propriedade">Propriedade</label>
        <Dropdown id="propriedade" v-model="v$.propriedade_id.$model" :options="propriedadeStore.propriedades" optionLabel="nome" optionValue="id" placeholder="Selecione a propriedade" :class="{ 'p-invalid': v$.propriedade_id.$error }" />
        <small v-if="v$.propriedade_id.$error" class="p-error">{{ v$.propriedade_id.$errors[0].$message }}</small>
      </div>
    </div>
    <Button type="submit" label="Salvar" class="mt-4" :disabled="v$.$invalid" />
  </form>
</template>

<script setup lang="ts">
import { ref, watch, computed, onMounted } from 'vue';
import InputText from 'primevue/inputtext';
import InputNumber from 'primevue/inputnumber';
import Button from 'primevue/button';
import Dropdown from 'primevue/dropdown';
import Calendar from 'primevue/calendar';
import type { Rebanho } from '@/services/rebanhoService';
import { useVuelidate } from '@vuelidate/core';
import { required, numeric } from '@vuelidate/validators';
import { usePropriedadeStore } from '@/stores/propriedade';

const props = defineProps<{ rebanho?: Rebanho | null }>();
const emit = defineEmits(['submit']);

const propriedadeStore = usePropriedadeStore();

onMounted(() => {
  propriedadeStore.fetchPropriedades();
});

const form = ref<Partial<Rebanho>>({ ...props.rebanho });

const rules = computed(() => ({
  especie: { required },
  quantidade: { required, numeric },
  propriedade_id: { required },
}));

const v$ = useVuelidate(rules, form);

watch(() => props.rebanho, (newVal) => {
  form.value = { ...newVal };
});

const submit = () => {
  v$.value.$touch();
  if (!v$.value.$invalid) {
    emit('submit', form.value);
  }
};
</script>
