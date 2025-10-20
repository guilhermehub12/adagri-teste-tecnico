import { defineStore } from 'pinia';
import { ref } from 'vue';
import { getPropriedades, getPropriedade, createPropriedade, updatePropriedade, deletePropriedade, type Propriedade } from '@/services/propriedadeService';

export const usePropriedadeStore = defineStore('propriedade', () => {
  const propriedades = ref<Propriedade[]>([]);
  const propriedade = ref<Propriedade | null>(null);
  const loading = ref(false);

  async function fetchPropriedades() {
    loading.value = true;
    try {
      const response = await getPropriedades();
      propriedades.value = response.data;
    } catch (error) {
      console.error('Error fetching propriedades:', error);
    } finally {
      loading.value = false;
    }
  }

  async function fetchPropriedade(id: number) {
    loading.value = true;
    try {
      const response = await getPropriedade(id);
      propriedade.value = response.data;
    } catch (error) {
      console.error(`Error fetching propriedade with id ${id}:`, error);
    } finally {
      loading.value = false;
    }
  }

  async function addPropriedade(newPropriedade: Omit<Propriedade, 'id'>) {
    loading.value = true;
    try {
      await createPropriedade(newPropriedade);
      await fetchPropriedades();
    } catch (error) {
      console.error('Error creating propriedade:', error);
    } finally {
      loading.value = false;
    }
  }

  async function editPropriedade(id: number, updatedPropriedade: Partial<Propriedade>) {
    loading.value = true;
    try {
      await updatePropriedade(id, updatedPropriedade);
      await fetchPropriedades();
    } catch (error) {
      console.error(`Error updating propriedade with id ${id}:`, error);
    } finally {
      loading.value = false;
    }
  }

  async function removePropriedade(id: number) {
    loading.value = true;
    try {
      await deletePropriedade(id);
      await fetchPropriedades();
    } catch (error) {
      console.error(`Error deleting propriedade with id ${id}:`, error);
    } finally {
      loading.value = false;
    }
  }

  return {
    propriedades,
    propriedade,
    loading,
    fetchPropriedades,
    fetchPropriedade,
    addPropriedade,
    editPropriedade,
    removePropriedade,
  };
});
