import { defineStore } from 'pinia';
import { ref } from 'vue';
import { getUsers, getUser, createUser, updateUser, deleteUser, type User } from '@/services/usuarioService';

export const useUsuarioStore = defineStore('usuario', () => {
  const users = ref<User[]>([]);
  const user = ref<User | null>(null);
  const loading = ref(false);

  async function fetchUsers() {
    loading.value = true;
    try {
      const response = await getUsers();
      users.value = response.data;
    } catch (error) {
      console.error('Error fetching users:', error);
    } finally {
      loading.value = false;
    }
  }

  async function fetchUser(id: number) {
    loading.value = true;
    try {
      const response = await getUser(id);
      user.value = response.data;
    } catch (error) {
      console.error(`Error fetching user with id ${id}:`, error);
    } finally {
      loading.value = false;
    }
  }

  async function addUser(newUser: Omit<User, 'id'>) {
    loading.value = true;
    try {
      await createUser(newUser);
      await fetchUsers();
    } catch (error) {
      console.error('Error creating user:', error);
    } finally {
      loading.value = false;
    }
  }

  async function editUser(id: number, updatedUser: Partial<User>) {
    loading.value = true;
    try {
      await updateUser(id, updatedUser);
      await fetchUsers();
    } catch (error) {
      console.error(`Error updating user with id ${id}:`, error);
    } finally {
      loading.value = false;
    }
  }

  async function removeUser(id: number) {
    loading.value = true;
    try {
      await deleteUser(id);
      await fetchUsers();
    } catch (error) {
      console.error(`Error deleting user with id ${id}:`, error);
    } finally {
      loading.value = false;
    }
  }

  return {
    users,
    user,
    loading,
    fetchUsers,
    fetchUser,
    addUser,
    editUser,
    removeUser,
  };
});
