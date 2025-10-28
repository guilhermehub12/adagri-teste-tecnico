import { defineStore } from 'pinia';
import { ref } from 'vue';
import { getProdutores, getProdutor, createProdutor, updateProdutor, deleteProdutor, type ProdutorRural } from '@/services/produtorService';

export const useProdutorStore = defineStore('produtor', () => {
  const produtores = ref<ProdutorRural[]>([]);
  const produtor = ref<ProdutorRural | null>(null);
  const loading = ref(false);
  const pagination = ref({
    current_page: 1,
    per_page: 15,
    total: 0,
    last_page: 1,
  });

  async function fetchProdutores(page: number = 1, perPage: number = 15, filters: Record<string, any> = {}) {
    loading.value = true;
    try {
      console.log('Fetching produtores with:', { page, perPage, filters });
      const response = await getProdutores(page, perPage, filters);
      console.log('API Response:', response);
      produtores.value = response.data.data;
      pagination.value = {
        current_page: response.data.meta.current_page,
        per_page: response.data.meta.per_page,
        total: response.data.meta.total,
        last_page: response.data.meta.last_page,
      };
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
      await fetchProdutores(pagination.value.current_page, pagination.value.per_page);
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
      await fetchProdutores(pagination.value.current_page, pagination.value.per_page);
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
      await fetchProdutores(pagination.value.current_page, pagination.value.per_page);
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
    pagination,
    fetchProdutores,
    fetchProdutor,
    addProdutor,
    editProdutor,
    removeProdutor,
  };
});
