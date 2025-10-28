<template>
  <div>
    <div class="flex justify-between items-center mb-4">
      <h1 class="text-2xl font-bold">Unidades de Produção</h1>
      <Button label="Nova Unidade" icon="pi pi-plus" @click="showCreateModal = true" />
    </div>

    <DataTable :value="unidadeProducaoStore.unidadesProducao" :loading="unidadeProducaoStore.loading" responsiveLayout="scroll">
      <Column field="id" header="ID"></Column>
      <Column field="nome_cultura" header="Cultura"></Column>
      <Column field="area_total_ha" header="Área (ha)"></Column>
      <Column header="Ações">
        <template #body="slotProps">
          <Button icon="pi pi-pencil" class="p-button-rounded p-button-success mr-2" @click="editUnidadeProducao(slotProps.data)" />
          <Button icon="pi pi-trash" class="p-button-rounded p-button-danger" @click="confirmDelete(slotProps.data)" />
        </template>
      </Column>
    </DataTable>

    <Dialog header="Nova Unidade de Produção" v-model:visible="showCreateModal" :modal="true" :style="{ width: '50vw' }">
      <UnidadeProducaoForm @submit="create" />
    </Dialog>

    <Dialog header="Editar Unidade de Produção" v-model:visible="showEditModal" :modal="true" :style="{ width: '50vw' }">
      <UnidadeProducaoForm :unidadeProducao="selectedUnidadeProducao" @submit="update" />
    </Dialog>

    <ConfirmDialog></ConfirmDialog>
  </div>
</template>

<script setup lang="ts">
import { ref, onMounted } from 'vue';
import { useUnidadeProducaoStore } from '@/stores/unidadeProducao';
import DataTable from 'primevue/datatable';
import Column from 'primevue/column';
import Button from 'primevue/button';
import Dialog from 'primevue/dialog';
import ConfirmDialog from 'primevue/confirmdialog';
import { useConfirm } from 'primevue/useconfirm';
import UnidadeProducaoForm from './UnidadeProducaoForm.vue';
import type { UnidadeProducao } from '@/services/unidadeProducaoService';

const unidadeProducaoStore = useUnidadeProducaoStore();
const confirm = useConfirm();

const showCreateModal = ref(false);
const showEditModal = ref(false);
const selectedUnidadeProducao = ref<UnidadeProducao | null>(null);

onMounted(() => {
  unidadeProducaoStore.fetchUnidadesProducao();
});

const create = async (unidadeProducao: Omit<UnidadeProducao, 'id'>) => {
  await unidadeProducaoStore.addUnidadeProducao(unidadeProducao);
  showCreateModal.value = false;
};

const update = async (unidadeProducao: Partial<UnidadeProducao>) => {
  if (selectedUnidadeProducao.value) {
    await unidadeProducaoStore.editUnidadeProducao(selectedUnidadeProducao.value.id, unidadeProducao);
    showEditModal.value = false;
  }
};

const editUnidadeProducao = (unidadeProducao: UnidadeProducao) => {
  selectedUnidadeProducao.value = unidadeProducao;
  showEditModal.value = true;
};

const confirmDelete = (unidadeProducao: UnidadeProducao) => {
  confirm.require({
    message: `Tem certeza que deseja excluir a unidade de produção de ${unidadeProducao.nome_cultura}?`,
    header: 'Confirmação',
    icon: 'pi pi-exclamation-triangle',
    accept: async () => {
      await unidadeProducaoStore.removeUnidadeProducao(unidadeProducao.id);
    },
  });
};
</script>
