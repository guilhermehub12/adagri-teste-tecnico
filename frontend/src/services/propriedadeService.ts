import api from './api';

export interface Propriedade {
  id: number;
  nome: string;
  municipio: string;
  uf: string;
  // Add other fields as needed
}

export const getPropriedades = async () => {
  const response = await api.get('/propriedades');
  return response.data;
};

export const getPropriedade = async (id: number) => {
  const response = await api.get(`/propriedades/${id}`);
  return response.data;
};

export const createPropriedade = async (propriedade: Omit<Propriedade, 'id'>) => {
  const response = await api.post('/propriedades', propriedade);
  return response.data;
};

export const updatePropriedade = async (id: number, propriedade: Partial<Propriedade>) => {
  const response = await api.put(`/propriedades/${id}`, propriedade);
  return response.data;
};

export const deletePropriedade = async (id: number) => {
  const response = await api.delete(`/propriedades/${id}`);
  return response.data;
};
