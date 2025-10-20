import api from './api';

export const getMyProfile = async () => {
  const response = await api.get('/auth/me');
  return response.data;
};
