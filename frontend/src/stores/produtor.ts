import { defineStore } from 'pinia';
import { ref } from 'vue';
import { getProdutores, getProdutor, createProdutor, updateProdutor, deleteProdutor, type ProdutorRural } from '@/services/produtorService';

export const useProdutorStore = defineStore('produtor', () => {
  const produtores = ref<ProdutorRural[]>([]);
  const produtor = ref<ProdutorRural | null>(null);
  const loading = ref(false);

  async function fetchProdutores() {
    loading.value = true;
    try {
      const response = await getProdutores();
      produtores.value = response.data;
    } catch (error) {
      console.error('Error fetching produtores:', error);
    } finally {
      loading.value = false;
    }
  }

  async function fetchProdutor(id: number) {
    loading.value = true;
    try {
      const response = await getProdutor(id);
      produtor.value = response.data;
    } catch (error) {
      console.error(`Error fetching produtor with id ${id}:`, error);
    } finally {
      loading.value = false;
    }
  }

  async function addProdutor(newProdutor: Omit<ProdutorRural, 'id'>) {
    loading.value = true;
    try {
      await createProdutor(newProdutor);
      await fetchProdutores();
    } catch (error) {
      console.error('Error creating produtor:', error);
    } finally {
      loading.value = false;
    }
  }

  async function editProdutor(id: number, updatedProdutor: Partial<ProdutorRural>) {
    loading.value = true;
    try {
      await updateProdutor(id, updatedProdutor);
      await fetchProdutores();
    } catch (error) {
      console.error(`Error updating produtor with id ${id}:`, error);
    } finally {
      loading.value = false;
    }
  }

  async function removeProdutor(id: number) {
    loading.value = true;
    try {
      await deleteProdutor(id);
      await fetchProdutores();
    } catch (error) {
      console.error(`Error deleting produtor with id ${id}:`, error);
    } finally {
      loading.value = false;
    }
  }

  return {
    produtores,
    produtor,
    loading,
    fetchProdutores,
    fetchProdutor,
    addProdutor,
    editProdutor,
    removeProdutor,
  };
});
