<template>
  <form @submit.prevent="submit">
    <div class="p-fluid">
      <div class="p-field">
        <label for="nome_cultura">Cultura</label>
        <InputText id="nome_cultura" v-model="v$.nome_cultura.$model" :class="{ 'p-invalid': v$.nome_cultura.$error }" />
        <small v-if="v$.nome_cultura.$error" class="p-error">{{ v$.nome_cultura.$errors[0].$message }}</small>
      </div>
      <div class="p-field">
        <label for="area_total_ha">Área (ha)</label>
        <InputNumber id="area_total_ha" v-model="v$.area_total_ha.$model" :class="{ 'p-invalid': v$.area_total_ha.$error }" />
        <small v-if="v$.area_total_ha.$error" class="p-error">{{ v$.area_total_ha.$errors[0].$message }}</small>
      </div>
      <div class="p-field">
        <label for="coordenadas_geograficas">Coordenadas Geográficas</label>
        <InputText id="coordenadas_geograficas" v-model="form.coordenadas_geograficas" />
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
import type { UnidadeProducao } from '@/services/unidadeProducaoService';
import { useVuelidate } from '@vuelidate/core';
import { required, numeric } from '@vuelidate/validators';
import { usePropriedadeStore } from '@/stores/propriedade';

const props = defineProps<{ unidadeProducao?: UnidadeProducao | null }>();
const emit = defineEmits(['submit']);

const propriedadeStore = usePropriedadeStore();

onMounted(() => {
  propriedadeStore.fetchPropriedades();
});

const form = ref<Partial<UnidadeProducao>>({ ...props.unidadeProducao });

const rules = computed(() => ({
  nome_cultura: { required },
  area_total_ha: { required, numeric },
  propriedade_id: { required },
}));

const v$ = useVuelidate(rules, form);

watch(() => props.unidadeProducao, (newVal) => {
  form.value = { ...newVal };
});

const submit = () => {
  v$.value.$touch();
  if (!v$.value.$invalid) {
    emit('submit', form.value);
  }
};
</script>
