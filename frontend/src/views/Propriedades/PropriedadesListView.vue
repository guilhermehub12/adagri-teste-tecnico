<template>
  <div>
    <div class="flex justify-between items-center mb-4">
      <h1 class="text-2xl font-bold">Propriedades</h1>
      <Button label="Nova Propriedade" icon="pi pi-plus" @click="showCreateModal = true" />
    </div>

    <DataTable :value="propriedadeStore.propriedades" :loading="propriedadeStore.loading" responsiveLayout="scroll">
      <Column field="id" header="ID"></Column>
      <Column field="nome" header="Nome"></Column>
      <Column field="municipio" header="Município"></Column>
      <Column field="uf" header="UF"></Column>
      <Column header="Ações">
        <template #body="slotProps">
          <Button icon="pi pi-pencil" class="p-button-rounded p-button-success mr-2" @click="editPropriedade(slotProps.data)" />
          <Button icon="pi pi-trash" class="p-button-rounded p-button-danger" @click="confirmDelete(slotProps.data)" />
        </template>
      </Column>
    </DataTable>

    <Dialog header="Nova Propriedade" v-model:visible="showCreateModal" :modal="true" :style="{ width: '50vw' }">
      <PropriedadeForm @submit="create" />
    </Dialog>

    <Dialog header="Editar Propriedade" v-model:visible="showEditModal" :modal="true" :style="{ width: '50vw' }">
      <PropriedadeForm :propriedade="selectedPropriedade" @submit="update" />
    </Dialog>

    <ConfirmDialog></ConfirmDialog>
  </div>
</template>

<script setup lang="ts">
import { ref, onMounted } from 'vue';
import { usePropriedadeStore } from '@/stores/propriedade';
import DataTable from 'primevue/datatable';
import Column from 'primevue/column';
import Button from 'primevue/button';
import Dialog from 'primevue/dialog';
import ConfirmDialog from 'primevue/confirmdialog';
import { useConfirm } from 'primevue/useconfirm';
import PropriedadeForm from './PropriedadeForm.vue';
import type { Propriedade } from '@/services/propriedadeService';

const propriedadeStore = usePropriedadeStore();
const confirm = useConfirm();

const showCreateModal = ref(false);
const showEditModal = ref(false);
const selectedPropriedade = ref<Propriedade | null>(null);

onMounted(() => {
  propriedadeStore.fetchPropriedades();
});

const create = async (propriedade: Omit<Propriedade, 'id'>) => {
  await propriedadeStore.addPropriedade(propriedade);
  showCreateModal.value = false;
};

const update = async (propriedade: Partial<Propriedade>) => {
  if (selectedPropriedade.value) {
    await propriedadeStore.editPropriedade(selectedPropriedade.value.id, propriedade);
    showEditModal.value = false;
  }
};

const editPropriedade = (propriedade: Propriedade) => {
  selectedPropriedade.value = propriedade;
  showEditModal.value = true;
};

const confirmDelete = (propriedade: Propriedade) => {
  confirm.require({
    message: `Tem certeza que deseja excluir a propriedade ${propriedade.nome}?`,
    header: 'Confirmação',
    icon: 'pi pi-exclamation-triangle',
    accept: async () => {
      await propriedadeStore.removePropriedade(propriedade.id);
    },
  });
};
</script>
