import api from './api';

export interface UnidadeProducao {
  id: number;
  nome_cultura: string;
  area_total_ha: number;
  // Add other fields as needed
}

export const getUnidadesProducao = async () => {
  const response = await api.get('/unidades-producao');
  return response.data;
};

export const getUnidadeProducao = async (id: number) => {
  const response = await api.get(`/unidades-producao/${id}`);
  return response.data;
};

export const createUnidadeProducao = async (unidadeProducao: Omit<UnidadeProducao, 'id'>) => {
  const response = await api.post('/unidades-producao', unidadeProducao);
  return response.data;
};

export const updateUnidadeProducao = async (id: number, unidadeProducao: Partial<UnidadeProducao>) => {
  const response = await api.put(`/unidades-producao/${id}`, unidadeProducao);
  return response.data;
};

export const deleteUnidadeProducao = async (id: number) => {
  const response = await api.delete(`/unidades-producao/${id}`);
  return response.data;
};
