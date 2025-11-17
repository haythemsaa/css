import { create } from 'zustand';
import AsyncStorage from '@react-native-async-storage/async-storage';
import { authService } from '../services/api';
import { STORAGE_KEYS } from '../constants/config';

const useAuthStore = create((set, get) => ({
  user: null,
  token: null,
  isAuthenticated: false,
  isLoading: true,

  // Initialize auth state from storage
  initialize: async () => {
    try {
      const token = await AsyncStorage.getItem(STORAGE_KEYS.AUTH_TOKEN);
      const userData = await AsyncStorage.getItem(STORAGE_KEYS.USER_DATA);

      if (token && userData) {
        set({
          token,
          user: JSON.parse(userData),
          isAuthenticated: true,
          isLoading: false,
        });

        // Refresh profile
        get().refreshProfile();
      } else {
        set({ isLoading: false });
      }
    } catch (error) {
      console.error('Error initializing auth:', error);
      set({ isLoading: false });
    }
  },

  // Login
  login: async (credentials) => {
    try {
      const response = await authService.login(credentials);

      if (response.data.success) {
        const { token, user } = response.data.data;

        await AsyncStorage.setItem(STORAGE_KEYS.AUTH_TOKEN, token);
        await AsyncStorage.setItem(STORAGE_KEYS.USER_DATA, JSON.stringify(user));

        set({
          token,
          user,
          isAuthenticated: true,
        });

        return { success: true, data: user };
      }

      return { success: false, message: 'Login failed' };
    } catch (error) {
      console.error('Login error:', error);
      return {
        success: false,
        message: error.response?.data?.message || 'Network error',
      };
    }
  },

  // Register
  register: async (userData) => {
    try {
      const response = await authService.register(userData);

      if (response.data.success) {
        const { token, user } = response.data.data;

        await AsyncStorage.setItem(STORAGE_KEYS.AUTH_TOKEN, token);
        await AsyncStorage.setItem(STORAGE_KEYS.USER_DATA, JSON.stringify(user));

        set({
          token,
          user,
          isAuthenticated: true,
        });

        return { success: true, data: user };
      }

      return { success: false, message: 'Registration failed' };
    } catch (error) {
      console.error('Registration error:', error);
      return {
        success: false,
        message: error.response?.data?.message || 'Network error',
      };
    }
  },

  // Logout
  logout: async () => {
    try {
      await authService.logout();
    } catch (error) {
      console.error('Logout error:', error);
    } finally {
      await AsyncStorage.removeItem(STORAGE_KEYS.AUTH_TOKEN);
      await AsyncStorage.removeItem(STORAGE_KEYS.USER_DATA);

      set({
        user: null,
        token: null,
        isAuthenticated: false,
      });
    }
  },

  // Refresh profile
  refreshProfile: async () => {
    try {
      const response = await authService.getProfile();

      if (response.data.success) {
        const user = response.data.data;
        await AsyncStorage.setItem(STORAGE_KEYS.USER_DATA, JSON.stringify(user));

        set({ user });
      }
    } catch (error) {
      console.error('Refresh profile error:', error);

      // If unauthorized, logout
      if (error.response?.status === 401) {
        get().logout();
      }
    }
  },

  // Update profile
  updateProfile: async (data) => {
    try {
      const response = await authService.updateProfile(data);

      if (response.data.success) {
        const user = response.data.data;
        await AsyncStorage.setItem(STORAGE_KEYS.USER_DATA, JSON.stringify(user));

        set({ user });

        return { success: true, data: user };
      }

      return { success: false, message: 'Update failed' };
    } catch (error) {
      console.error('Update profile error:', error);
      return {
        success: false,
        message: error.response?.data?.message || 'Network error',
      };
    }
  },

  // Helper: Check if user is premium
  isPremium: () => {
    const { user } = get();
    return user?.user_type === 'premium' || user?.user_type === 'socios';
  },

  // Helper: Check if user is socios
  isSocios: () => {
    const { user } = get();
    return user?.user_type === 'socios';
  },

  // Helper: Get user type info
  getUserTypeInfo: () => {
    const { user } = get();
    const types = {
      free: { name: 'Free', icon: '👤', color: '#6B7280' },
      premium: { name: 'Premium', icon: '⭐', color: '#F59E0B' },
      socios: { name: 'Socios', icon: '👑', color: '#D4AF37' },
    };
    return types[user?.user_type] || types.free;
  },
}));

export default useAuthStore;
