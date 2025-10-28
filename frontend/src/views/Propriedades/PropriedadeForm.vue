<template>
  <form @submit.prevent="submit">
    <div class="p-fluid">
      <div class="p-field">
        <label for="nome">Nome</label>
        <InputText id="nome" v-model="v$.nome.$model" :class="{ 'p-invalid': v$.nome.$error }" />
        <small v-if="v$.nome.$error" class="p-error">{{ v$.nome.$errors[0].$message }}</small>
      </div>
      <div class="p-field">
        <label for="municipio">Município</label>
        <InputText id="municipio" v-model="v$.municipio.$model" :class="{ 'p-invalid': v$.municipio.$error }" />
        <small v-if="v$.municipio.$error" class="p-error">{{ v$.municipio.$errors[0].$message }}</small>
      </div>
      <div class="p-field">
        <label for="uf">UF</label>
        <InputText id="uf" v-model="v$.uf.$model" :class="{ 'p-invalid': v$.uf.$error }" />
        <small v-if="v$.uf.$error" class="p-error">{{ v$.uf.$errors[0].$message }}</small>
      </div>
      <div class="p-field">
        <label for="inscricao_estadual">Inscrição Estadual</label>
        <InputText id="inscricao_estadual" v-model="form.inscricao_estadual" />
      </div>
      <div class="p-field">
        <label for="area_total">Área Total (ha)</label>
        <InputNumber id="area_total" v-model="form.area_total" />
      </div>
      <div class="p-field">
        <label for="produtor">Produtor</label>
        <Dropdown id="produtor" v-model="v$.produtor_id.$model" :options="produtorStore.produtores" optionLabel="nome" optionValue="id" placeholder="Selecione o produtor" :class="{ 'p-invalid': v$.produtor_id.$error }" />
        <small v-if="v$.produtor_id.$error" class="p-error">{{ v$.produtor_id.$errors[0].$message }}</small>
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
import type { Propriedade } from '@/services/propriedadeService';
import { useVuelidate } from '@vuelidate/core';
import { required } from '@vuelidate/validators';
import { useProdutorStore } from '@/stores/produtor';

const props = defineProps<{ propriedade?: Propriedade | null }>();
const emit = defineEmits(['submit']);

const produtorStore = useProdutorStore();

onMounted(() => {
  produtorStore.fetchProdutores();
});

const form = ref<Partial<Propriedade>>({ ...props.propriedade });

const rules = computed(() => ({
  nome: { required },
  municipio: { required },
  uf: { required },
  produtor_id: { required },
}));

const v$ = useVuelidate(rules, form);

watch(() => props.propriedade, (newVal) => {
  form.value = { ...newVal };
});

const submit = () => {
  v$.value.$touch();
  if (!v$.value.$invalid) {
    emit('submit', form.value);
  }
};
</script>
