import axios from 'axios';

const API_BASE_URL = 'http://localhost:8001/api';

const api = axios.create({
  baseURL: API_BASE_URL,
  headers: {
    'Content-Type': 'application/json',
  },
});

// Add auth token to requests
api.interceptors.request.use((config) => {
  const token = localStorage.getItem('authToken');
  if (token) {
    config.headers.Authorization = `Bearer ${token}`;
  }
  return config;
});

export const authAPI = {
  login: async (email: string, password: string) => {
    const response = await api.post('/auth/login', { email, password });
    if (response.data.token) {
      localStorage.setItem('authToken', response.data.token);
      localStorage.setItem('user', JSON.stringify(response.data.user));
    }
    return response.data;
  },

  register: async (userData: any) => {
    const response = await api.post('/auth/register', userData);
    if (response.data.token) {
      localStorage.setItem('authToken', response.data.token);
      localStorage.setItem('user', JSON.stringify(response.data.user));
    }
    return response.data;
  },

  logout: async () => {
    await api.post('/auth/logout');
    localStorage.removeItem('authToken');
    localStorage.removeItem('user');
  },

  getMe: async () => {
    const response = await api.get('/auth/me');
    return response.data;
  },

  updateDeviceToken: async (deviceToken: string) => {
    const response = await api.put('/auth/device-token', { device_token: deviceToken });
    return response.data;
  },
};

export const bookingAPI = {
  getBookings: async () => {
    const response = await api.get('/bookings');
    return response.data;
  },

  createBooking: async (bookingData: any) => {
    const response = await api.post('/bookings', bookingData);
    return response.data;
  },

  getBooking: async (id: number) => {
    const response = await api.get(`/bookings/${id}`);
    return response.data;
  },

  acceptBooking: async (id: number) => {
    const response = await api.post(`/bookings/${id}/accept`);
    return response.data;
  },

  rejectBooking: async (id: number) => {
    const response = await api.post(`/bookings/${id}/reject`);
    return response.data;
  },

  completeBooking: async (id: number) => {
    const response = await api.post(`/bookings/${id}/complete`);
    return response.data;
  },

  cancelBooking: async (id: number) => {
    const response = await api.post(`/bookings/${id}/cancel`);
    return response.data;
  },
};

export const riderAPI = {
  getRiders: async () => {
    const response = await api.get('/riders');
    return response.data;
  },

  goOnline: async () => {
    const response = await api.post('/riders/go-online');
    return response.data;
  },

  goOffline: async () => {
    const response = await api.post('/riders/go-offline');
    return response.data;
  },

  updateCapacity: async (capacity: number) => {
    const response = await api.put('/riders/capacity', { capacity });
    return response.data;
  },

  getMyAssignments: async () => {
    const response = await api.get('/riders/my-assignments');
    return response.data;
  },

  getQueuePosition: async () => {
    const response = await api.get('/riders/queue-position');
    return response.data;
  },

  getStats: async () => {
    const response = await api.get('/riders/stats');
    return response.data;
  },
};

export default api;
