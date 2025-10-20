import api from './api';

export const getPropriedadesPorMunicipio = async (uf?: string) => {
  const response = await api.get('/relatorios/propriedades-por-municipio', { params: { uf } });
  return response.data;
};

export const getAnimaisPorEspecie = async (especie?: string) => {
  const response = await api.get('/relatorios/animais-por-especie', { params: { especie } });
  return response.data;
};

export const getHectaresPorCultura = async (cultura?: string) => {
  const response = await api.get('/relatorios/hectares-por-cultura', { params: { cultura } });
  return response.data;
};
