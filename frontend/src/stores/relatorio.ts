import { defineStore } from 'pinia';
import { ref } from 'vue';
import { getPropriedadesPorMunicipio, getAnimaisPorEspecie, getHectaresPorCultura } from '@/services/relatorioService';

export const useRelatorioStore = defineStore('relatorio', () => {
  const propriedadesPorMunicipio = ref([]);
  const animaisPorEspecie = ref([]);
  const hectaresPorCultura = ref([]);
  const loading = ref(false);

  async function fetchPropriedadesPorMunicipio(uf?: string) {
    loading.value = true;
    try {
      const response = await getPropriedadesPorMunicipio(uf);
      propriedadesPorMunicipio.value = response.data;
    } catch (error) {
      console.error('Error fetching propriedades por município:', error);
    } finally {
      loading.value = false;
    }
  }

  async function fetchAnimaisPorEspecie(especie?: string) {
    loading.value = true;
    try {
      const response = await getAnimaisPorEspecie(especie);
      animaisPorEspecie.value = response.data;
    } catch (error) {
      console.error('Error fetching animais por espécie:', error);
    } finally {
      loading.value = false;
    }
  }

  async function fetchHectaresPorCultura(cultura?: string) {
    loading.value = true;
    try {
      const response = await getHectaresPorCultura(cultura);
      hectaresPorCultura.value = response.data;
    } catch (error) {
      console.error('Error fetching hectares por cultura:', error);
    } finally {
      loading.value = false;
    }
  }

  return {
    propriedadesPorMunicipio,
    animaisPorEspecie,
    hectaresPorCultura,
    loading,
    fetchPropriedadesPorMunicipio,
    fetchAnimaisPorEspecie,
    fetchHectaresPorCultura,
  };
});
