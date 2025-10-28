import api from './api';

export interface ProdutorRural {
  id: number;
  nome: string;
  cpf_cnpj: string;
  telefone: string;
  email: string;
  endereco: string;
}

export const getProdutores = async () => {
  const response = await api.get('/produtores-rurais');
  return response.data;
};

export const getProdutor = async (id: number) => {
  const response = await api.get(`/produtores-rurais/${id}`);
  return response.data;
};

export const createProdutor = async (produtor: Omit<ProdutorRural, 'id'>) => {
  const response = await api.post('/produtores-rurais', produtor);
  return response.data;
};

export const updateProdutor = async (id: number, produtor: Partial<ProdutorRural>) => {
  const response = await api.put(`/produtores-rurais/${id}`, produtor);
  return response.data;
};

export const deleteProdutor = async (id: number) => {
  const response = await api.delete(`/produtores-rurais/${id}`);
  return response.data;
};
