import api from './api';

export interface Rebanho {
  id: number;
  especie: string;
  quantidade: number;
  // Add other fields as needed
}

export const getRebanhos = async () => {
  const response = await api.get('/rebanhos');
  return response.data;
};

export const getRebanho = async (id: number) => {
  const response = await api.get(`/rebanhos/${id}`);
  return response.data;
};

export const createRebanho = async (rebanho: Omit<Rebanho, 'id'>) => {
  const response = await api.post('/rebanhos', rebanho);
  return response.data;
};

export const updateRebanho = async (id: number, rebanho: Partial<Rebanho>) => {
  const response = await api.put(`/rebanhos/${id}`, rebanho);
  return response.data;
};

export const deleteRebanho = async (id: number) => {
  const response = await api.delete(`/rebanhos/${id}`);
  return response.data;
};
