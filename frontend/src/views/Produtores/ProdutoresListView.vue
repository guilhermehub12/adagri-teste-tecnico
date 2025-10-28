<template>
  <div>
    <div class="flex justify-between items-center mb-4">
      <h1 class="text-2xl font-bold">Produtores Rurais</h1>
      <Button label="Novo Produtor" icon="pi pi-plus" @click="showCreateModal = true" />
    </div>

    <div class="p-fluid p-formgrid grid mb-4">
      <div class="p-field col-12 md:col-4">
        <label for="filterNome">Nome</label>
        <InputText id="filterNome" v-model="filters.nome" @input="onFilterChange" />
      </div>
      <div class="p-field col-12 md:col-4">
        <label for="filterCpfCnpj">CPF/CNPJ</label>
        <InputText id="filterCpfCnpj" v-model="filters.cpf_cnpj" @input="onFilterChange" />
      </div>
      <div class="p-field col-12 md:col-4">
        <label for="filterEmail">Email</label>
        <InputText id="filterEmail" v-model="filters.email" @input="onFilterChange" />
      </div>
    </div>

    <DataTable :value="produtorStore.produtores" :loading="produtorStore.loading" responsiveLayout="scroll">
      <Column field="id" header="ID"></Column>
      <Column field="nome" header="Nome"></Column>
      <Column field="cpf_cnpj" header="CPF/CNPJ"></Column>
      <Column field="telefone" header="Telefone"></Column>
      <Column field="email" header="Email"></Column>
      <Column header="Ações">
        <template #body="slotProps">
          <Button icon="pi pi-pencil" class="p-button-rounded p-button-success mr-2" @click="editProdutor(slotProps.data)" />
          <Button icon="pi pi-trash" class="p-button-rounded p-button-danger" @click="confirmDelete(slotProps.data)" />
        </template>
      </Column>
    </DataTable>

    <Paginator
      v-if="produtorStore.pagination.total > 0"
      :rows="produtorStore.pagination.per_page"
      :totalRecords="produtorStore.pagination.total"
      :rowsPerPageOptions="[10, 15, 20, 30]"
      @page="onPageChange"
    ></Paginator>

    <Dialog header="Novo Produtor" v-model:visible="showCreateModal" :modal="true" :style="{ width: '50vw' }">
      <ProdutorForm @submit="create" />
    </Dialog>

    <Dialog header="Editar Produtor" v-model:visible="showEditModal" :modal="true" :style="{ width: '50vw' }">
      <ProdutorForm :produtor="selectedProdutor" @submit="update" />
    </Dialog>

    <ConfirmDialog></ConfirmDialog>
  </div>
</template>

<script setup lang="ts">
import { ref, onMounted, reactive } from 'vue';
import { useProdutorStore } from '@/stores/produtor';
import DataTable from 'primevue/datatable';
import Column from 'primevue/column';
import Button from 'primevue/button';
import Dialog from 'primevue/dialog';
import ConfirmDialog from 'primevue/confirmdialog';
import { useConfirm } from 'primevue/useconfirm';
import ProdutorForm from './ProdutorForm.vue';
import type { ProdutorRural } from '@/services/produtorService';
import InputText from 'primevue/inputtext';
import Paginator from 'primevue/paginator';

const produtorStore = useProdutorStore();
const confirm = useConfirm();

const showCreateModal = ref(false);
const showEditModal = ref(false);
const selectedProdutor = ref<ProdutorRural | null>(null);

const filters = reactive({
  nome: '',
  cpf_cnpj: '',
  email: '',
});

let filterTimeout: ReturnType<typeof setTimeout>;

onMounted(() => {
  produtorStore.fetchProdutores();
});

const onFilterChange = () => {
  clearTimeout(filterTimeout);
  filterTimeout = setTimeout(() => {
    console.log('Applying filters:', filters);
    produtorStore.fetchProdutores(1, produtorStore.pagination.per_page, filters);
  }, 500);
};

const onPageChange = (event: any) => {
  produtorStore.fetchProdutores(event.page + 1, event.rows, filters);
};

const create = async (produtor: Omit<ProdutorRural, 'id'>) => {
  await produtorStore.addProdutor(produtor);
  showCreateModal.value = false;
};

const update = async (produtor: Partial<ProdutorRural>) => {
  if (selectedProdutor.value) {
    await produtorStore.editProdutor(selectedProdutor.value.id, produtor);
    showEditModal.value = false;
  }
};

const editProdutor = (produtor: ProdutorRural) => {
  selectedProdutor.value = produtor;
  showEditModal.value = true;
};

const confirmDelete = (produtor: ProdutorRural) => {
  confirm.require({
    message: `Tem certeza que deseja excluir o produtor ${produtor.nome}?`,
    header: 'Confirmação',
    icon: 'pi pi-exclamation-triangle',
    accept: async () => {
      await produtorStore.removeProdutor(produtor.id);
    },
  });
};
</script>
