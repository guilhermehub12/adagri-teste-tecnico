import { defineStore } from 'pinia';
import { ref } from 'vue';
import { getRebanhos, getRebanho, createRebanho, updateRebanho, deleteRebanho, type Rebanho } from '@/services/rebanhoService';

export const useRebanhoStore = defineStore('rebanho', () => {
  const rebanhos = ref<Rebanho[]>([]);
  const rebanho = ref<Rebanho | null>(null);
  const loading = ref(false);

  async function fetchRebanhos() {
    loading.value = true;
    try {
      const response = await getRebanhos();
      rebanhos.value = response.data;
    } catch (error) {
      console.error('Error fetching rebanhos:', error);
    } finally {
      loading.value = false;
    }
  }

  async function fetchRebanho(id: number) {
    loading.value = true;
    try {
      const response = await getRebanho(id);
      rebanho.value = response.data;
    } catch (error) {
      console.error(`Error fetching rebanho with id ${id}:`, error);
    } finally {
      loading.value = false;
    }
  }

  async function addRebanho(newRebanho: Omit<Rebanho, 'id'>) {
    loading.value = true;
    try {
      await createRebanho(newRebanho);
      await fetchRebanhos();
    } catch (error) {
      console.error('Error creating rebanho:', error);
    } finally {
      loading.value = false;
    }
  }

  async function editRebanho(id: number, updatedRebanho: Partial<Rebanho>) {
    loading.value = true;
    try {
      await updateRebanho(id, updatedRebanho);
      await fetchRebanhos();
    } catch (error) {
      console.error(`Error updating rebanho with id ${id}:`, error);
    } finally {
      loading.value = false;
    }
  }

  async function removeRebanho(id: number) {
    loading.value = true;
    try {
      await deleteRebanho(id);
      await fetchRebanhos();
    } catch (error) {
      console.error(`Error deleting rebanho with id ${id}:`, error);
    } finally {
      loading.value = false;
    }
  }

  return {
    rebanhos,
    rebanho,
    loading,
    fetchRebanhos,
    fetchRebanho,
    addRebanho,
    editRebanho,
    removeRebanho,
  };
});
