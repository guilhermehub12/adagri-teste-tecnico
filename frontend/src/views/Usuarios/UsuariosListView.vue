<template>
  <div>
    <div class="flex justify-between items-center mb-4">
      <h1 class="text-2xl font-bold">Usuários</h1>
      <Button label="Novo Usuário" icon="pi pi-plus" @click="showCreateModal = true" />
    </div>

    <DataTable :value="usuarioStore.users" :loading="usuarioStore.loading" responsiveLayout="scroll">
      <Column field="id" header="ID"></Column>
      <Column field="name" header="Nome"></Column>
      <Column field="email" header="Email"></Column>
      <Column field="role" header="Perfil"></Column>
      <Column header="Ações">
        <template #body="slotProps">
          <Button icon="pi pi-pencil" class="p-button-rounded p-button-success mr-2" @click="editUser(slotProps.data)" />
          <Button icon="pi pi-trash" class="p-button-rounded p-button-danger" @click="confirmDelete(slotProps.data)" />
        </template>
      </Column>
    </DataTable>

    <Dialog header="Novo Usuário" v-model:visible="showCreateModal" :modal="true" :style="{ width: '50vw' }">
      <UsuarioForm @submit="create" />
    </Dialog>

    <Dialog header="Editar Usuário" v-model:visible="showEditModal" :modal="true" :style="{ width: '50vw' }">
      <UsuarioForm :user="selectedUser" @submit="update" />
    </Dialog>

    <ConfirmDialog></ConfirmDialog>
  </div>
</template>

<script setup lang="ts">
import { ref, onMounted } from 'vue';
import { useUsuarioStore } from '@/stores/usuario';
import DataTable from 'primevue/datatable';
import Column from 'primevue/column';
import Button from 'primevue/button';
import Dialog from 'primevue/dialog';
import ConfirmDialog from 'primevue/confirmdialog';
import { useConfirm } from 'primevue/useconfirm';
import UsuarioForm from './UsuarioForm.vue';
import type { User } from '@/services/usuarioService';

const usuarioStore = useUsuarioStore();
const confirm = useConfirm();

const showCreateModal = ref(false);
const showEditModal = ref(false);
const selectedUser = ref<User | null>(null);

onMounted(() => {
  usuarioStore.fetchUsers();
});

const create = async (user: Omit<User, 'id'>) => {
  await usuarioStore.addUser(user);
  showCreateModal.value = false;
};

const update = async (user: Partial<User>) => {
  if (selectedUser.value) {
    await usuarioStore.editUser(selectedUser.value.id, user);
    showEditModal.value = false;
  }
};

const editUser = (user: User) => {
  selectedUser.value = user;
  showEditModal.value = true;
};

const confirmDelete = (user: User) => {
  confirm.require({
    message: `Tem certeza que deseja excluir o usuário ${user.name}?`,
    header: 'Confirmação',
    icon: 'pi pi-exclamation-triangle',
    accept: async () => {
      await usuarioStore.removeUser(user.id);
    },
  });
};
</script>
