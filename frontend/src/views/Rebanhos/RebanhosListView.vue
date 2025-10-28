<template>
  <div>
    <div class="flex justify-between items-center mb-4">
      <h1 class="text-2xl font-bold">Rebanhos</h1>
      <Button label="Novo Rebanho" icon="pi pi-plus" @click="showCreateModal = true" />
    </div>

    <DataTable :value="rebanhoStore.rebanhos" :loading="rebanhoStore.loading" responsiveLayout="scroll">
      <Column field="id" header="ID"></Column>
      <Column field="especie" header="Espécie"></Column>
      <Column field="quantidade" header="Quantidade"></Column>
      <Column header="Ações">
        <template #body="slotProps">
          <Button icon="pi pi-pencil" class="p-button-rounded p-button-success mr-2" @click="editRebanho(slotProps.data)" />
          <Button icon="pi pi-trash" class="p-button-rounded p-button-danger" @click="confirmDelete(slotProps.data)" />
        </template>
      </Column>
    </DataTable>

    <Dialog header="Novo Rebanho" v-model:visible="showCreateModal" :modal="true" :style="{ width: '50vw' }">
      <RebanhoForm @submit="create" />
    </Dialog>

    <Dialog header="Editar Rebanho" v-model:visible="showEditModal" :modal="true" :style="{ width: '50vw' }">
      <RebanhoForm :rebanho="selectedRebanho" @submit="update" />
    </Dialog>

    <ConfirmDialog></ConfirmDialog>
  </div>
</template>

<script setup lang="ts">
import { ref, onMounted } from 'vue';
import { useRebanhoStore } from '@/stores/rebanho';
import DataTable from 'primevue/datatable';
import Column from 'primevue/column';
import Button from 'primevue/button';
import Dialog from 'primevue/dialog';
import ConfirmDialog from 'primevue/confirmdialog';
import { useConfirm } from 'primevue/useconfirm';
import RebanhoForm from './RebanhoForm.vue';
import type { Rebanho } from '@/services/rebanhoService';

const rebanhoStore = useRebanhoStore();
const confirm = useConfirm();

const showCreateModal = ref(false);
const showEditModal = ref(false);
const selectedRebanho = ref<Rebanho | null>(null);

onMounted(() => {
  rebanhoStore.fetchRebanhos();
});

const create = async (rebanho: Omit<Rebanho, 'id'>) => {
  await rebanhoStore.addRebanho(rebanho);
  showCreateModal.value = false;
};

const update = async (rebanho: Partial<Rebanho>) => {
  if (selectedRebanho.value) {
    await rebanhoStore.editRebanho(selectedRebanho.value.id, rebanho);
    showEditModal.value = false;
  }
};

const editRebanho = (rebanho: Rebanho) => {
  selectedRebanho.value = rebanho;
  showEditModal.value = true;
};

const confirmDelete = (rebanho: Rebanho) => {
  confirm.require({
    message: `Tem certeza que deseja excluir o rebanho de ${rebanho.especie}?`,
    header: 'Confirmação',
    icon: 'pi pi-exclamation-triangle',
    accept: async () => {
      await rebanhoStore.removeRebanho(rebanho.id);
    },
  });
};
</script>
