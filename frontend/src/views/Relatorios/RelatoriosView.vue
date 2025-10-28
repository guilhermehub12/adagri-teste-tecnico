<template>
  <div>
    <h1 class="text-2xl font-bold mb-4">Relatórios</h1>

    <TabView>
      <TabPanel header="Propriedades por Município">
        <PropriedadesPorMunicipioChart />
      </TabPanel>
      <TabPanel header="Animais por Espécie">
        <AnimaisPorEspecieChart />
      </TabPanel>
      <TabPanel header="Hectares por Cultura">
        <HectaresPorCulturaChart />
      </TabPanel>
    </TabView>
  </div>
</template>

<script setup lang="ts">
import TabView from 'primevue/tabview';
import TabPanel from 'primevue/tabpanel';
import PropriedadesPorMunicipioChart from '@/components/charts/PropriedadesPorMunicipioChart.vue';
import AnimaisPorEspecieChart from '@/components/charts/AnimaisPorEspecieChart.vue';
import HectaresPorCulturaChart from '@/components/charts/HectaresPorCulturaChart.vue';
</script>
