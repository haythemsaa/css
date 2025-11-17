import axios from 'axios';
import AsyncStorage from '@react-native-async-storage/async-storage';
import { API_BASE_URL, API_TIMEOUT, STORAGE_KEYS } from '../constants/config';

// Create axios instance
const api = axios.create({
  baseURL: API_BASE_URL,
  timeout: API_TIMEOUT,
  headers: {
    'Content-Type': 'application/json',
    'Accept': 'application/json',
  },
});

// Request interceptor to add auth token
api.interceptors.request.use(
  async (config) => {
    const token = await AsyncStorage.getItem(STORAGE_KEYS.AUTH_TOKEN);
    if (token) {
      config.headers.Authorization = `Bearer ${token}`;
    }
    return config;
  },
  (error) => {
    return Promise.reject(error);
  }
);

// Response interceptor to handle errors
api.interceptors.response.use(
  (response) => response,
  async (error) => {
    if (error.response?.status === 401) {
      // Unauthorized - clear auth data
      await AsyncStorage.removeItem(STORAGE_KEYS.AUTH_TOKEN);
      await AsyncStorage.removeItem(STORAGE_KEYS.USER_DATA);
    }
    return Promise.reject(error);
  }
);

// Auth Service
export const authService = {
  login: (credentials) => api.post('/auth/login', credentials),
  register: (userData) => api.post('/auth/register', userData),
  logout: () => api.post('/auth/logout'),
  getProfile: () => api.get('/auth/profile'),
  updateProfile: (data) => api.put('/auth/profile', data),
  changePassword: (data) => api.put('/auth/password', data),
};

// Partners Service
export const partnersService = {
  getCategories: () => api.get('/partners/categories'),
  getPartners: (params = {}) => api.get('/partners', { params }),
  getPartner: (slug) => api.get(`/partners/${slug}`),
  getNearby: (latitude, longitude, radius = 10) =>
    api.get('/partners/nearby', { params: { latitude, longitude, radius } }),
};

// Offers Service
export const offersService = {
  getOffers: (partnerSlug, params = {}) =>
    api.get(`/partners/${partnerSlug}/offers`, { params }),
  getOffer: (slug) => api.get(`/offers/${slug}`),
};

// Codes Service
export const codesService = {
  generateCode: (offerSlug, type = 'qr') =>
    api.post(`/codes/generate/${offerSlug}`, { type }),
  getMyCodes: (params = {}) => api.get('/codes/my-codes', { params }),
  validateCode: (code) => api.post('/codes/validate', { code }),
  useCode: (code, amount) => api.post(`/codes/${code}/use`, { amount }),
};

// Content Service
export const contentService = {
  getContent: (params = {}) => api.get('/content', { params }),
  getContentDetail: (slug) => api.get(`/content/${slug}`),
  likeContent: (id) => api.post(`/content/${id}/like`),
  unlikeContent: (id) => api.delete(`/content/${id}/like`),
};

// Players Service
export const playersService = {
  getPlayers: (params = {}) => api.get('/players', { params }),
  getPlayer: (id) => api.get(`/players/${id}`),
};

// Matches Service
export const matchesService = {
  getMatches: (params = {}) => api.get('/matches', { params }),
  getUpcoming: (params = {}) => api.get('/matches/upcoming', { params }),
  getResults: (params = {}) => api.get('/matches/results', { params }),
  getMatch: (id) => api.get(`/matches/${id}`),
};

export default api;
