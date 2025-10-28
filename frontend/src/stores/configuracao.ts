import { defineStore } from 'pinia';
import { ref } from 'vue';
import { getMyProfile } from '@/services/configuracaoService';
import type { User } from '@/services/usuarioService';

export const useConfiguracaoStore = defineStore('configuracao', () => {
  const profile = ref<User | null>(null);
  const loading = ref(false);

  async function fetchProfile() {
    loading.value = true;
    try {
      const response = await getMyProfile();
      profile.value = response;
    } catch (error) {
      console.error('Error fetching profile:', error);
    } finally {
      loading.value = false;
    }
  }

  return {
    profile,
    loading,
    fetchProfile,
  };
});
