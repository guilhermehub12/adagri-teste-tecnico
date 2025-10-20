import { defineStore } from 'pinia';
import { ref } from 'vue';
import { getUnidadesProducao, getUnidadeProducao, createUnidadeProducao, updateUnidadeProducao, deleteUnidadeProducao, type UnidadeProducao } from '@/services/unidadeProducaoService';

export const useUnidadeProducaoStore = defineStore('unidadeProducao', () => {
  const unidadesProducao = ref<UnidadeProducao[]>([]);
  const unidadeProducao = ref<UnidadeProducao | null>(null);
  const loading = ref(false);

  async function fetchUnidadesProducao() {
    loading.value = true;
    try {
      const response = await getUnidadesProducao();
      unidadesProducao.value = response.data;
    } catch (error) {
      console.error('Error fetching unidades de produção:', error);
    } finally {
      loading.value = false;
    }
  }

  async function fetchUnidadeProducao(id: number) {
    loading.value = true;
    try {
      const response = await getUnidadeProducao(id);
      unidadeProducao.value = response.data;
    } catch (error) {
      console.error(`Error fetching unidade de produção with id ${id}:`, error);
    } finally {
      loading.value = false;
    }
  }

  async function addUnidadeProducao(newUnidadeProducao: Omit<UnidadeProducao, 'id'>) {
    loading.value = true;
    try {
      await createUnidadeProducao(newUnidadeProducao);
      await fetchUnidadesProducao();
    } catch (error) {
      console.error('Error creating unidade de produção:', error);
    } finally {
      loading.value = false;
    }
  }

  async function editUnidadeProducao(id: number, updatedUnidadeProducao: Partial<UnidadeProducao>) {
    loading.value = true;
    try {
      await updateUnidadeProducao(id, updatedUnidadeProducao);
      await fetchUnidadesProducao();
    } catch (error) {
      console.error(`Error updating unidade de produção with id ${id}:`, error);
    } finally {
      loading.value = false;
    }
  }

  async function removeUnidadeProducao(id: number) {
    loading.value = true;
    try {
      await deleteUnidadeProducao(id);
      await fetchUnidadesProducao();
    } catch (error) {
      console.error(`Error deleting unidade de produção with id ${id}:`, error);
    } finally {
      loading.value = false;
    }
  }

  return {
    unidadesProducao,
    unidadeProducao,
    loading,
    fetchUnidadesProducao,
    fetchUnidadeProducao,
    addUnidadeProducao,
    editUnidadeProducao,
    removeUnidadeProducao,
  };
});
