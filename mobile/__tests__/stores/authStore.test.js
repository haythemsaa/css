import { renderHook, act, waitFor } from '@testing-library/react-native';
import AsyncStorage from '@react-native-async-storage/async-storage';
import useAuthStore from '../../src/stores/authStore';
import { authService } from '../../src/services/api';

// Mock the API service
jest.mock('../../src/services/api', () => ({
  authService: {
    login: jest.fn(),
    register: jest.fn(),
    logout: jest.fn(),
    getProfile: jest.fn(),
    updateProfile: jest.fn(),
  },
}));

describe('authStore', () => {
  beforeEach(() => {
    // Clear all mocks before each test
    jest.clearAllMocks();
    AsyncStorage.clear();

    // Reset the store
    const { result } = renderHook(() => useAuthStore());
    act(() => {
      result.current.user = null;
      result.current.token = null;
      result.current.isAuthenticated = false;
      result.current.isLoading = false;
    });
  });

  describe('initialize', () => {
    it('should load auth data from storage', async () => {
      const token = 'test-token';
      const user = { id: 1, name: 'Test User', user_type: 'free' };

      AsyncStorage.getItem.mockImplementation((key) => {
        if (key === 'css_auth_token') return Promise.resolve(token);
        if (key === 'css_user_data') return Promise.resolve(JSON.stringify(user));
        return Promise.resolve(null);
      });

      authService.getProfile.mockResolvedValue({
        data: { success: true, data: user },
      });

      const { result } = renderHook(() => useAuthStore());

      await act(async () => {
        await result.current.initialize();
      });

      await waitFor(() => {
        expect(result.current.isAuthenticated).toBe(true);
        expect(result.current.user).toEqual(user);
        expect(result.current.token).toBe(token);
        expect(result.current.isLoading).toBe(false);
      });
    });

    it('should handle no auth data in storage', async () => {
      AsyncStorage.getItem.mockResolvedValue(null);

      const { result } = renderHook(() => useAuthStore());

      await act(async () => {
        await result.current.initialize();
      });

      expect(result.current.isAuthenticated).toBe(false);
      expect(result.current.user).toBe(null);
      expect(result.current.isLoading).toBe(false);
    });
  });

  describe('login', () => {
    it('should login successfully', async () => {
      const credentials = { email: 'test@example.com', password: 'password123' };
      const token = 'test-token';
      const user = { id: 1, name: 'Test User', email: 'test@example.com' };

      authService.login.mockResolvedValue({
        data: { success: true, data: { token, user } },
      });

      const { result } = renderHook(() => useAuthStore());

      let response;
      await act(async () => {
        response = await result.current.login(credentials);
      });

      expect(response.success).toBe(true);
      expect(result.current.isAuthenticated).toBe(true);
      expect(result.current.user).toEqual(user);
      expect(result.current.token).toBe(token);
      expect(AsyncStorage.setItem).toHaveBeenCalledWith('css_auth_token', token);
      expect(AsyncStorage.setItem).toHaveBeenCalledWith(
        'css_user_data',
        JSON.stringify(user)
      );
    });

    it('should handle login failure', async () => {
      const credentials = { email: 'test@example.com', password: 'wrong' };

      authService.login.mockRejectedValue({
        response: { data: { message: 'Invalid credentials' } },
      });

      const { result } = renderHook(() => useAuthStore());

      let response;
      await act(async () => {
        response = await result.current.login(credentials);
      });

      expect(response.success).toBe(false);
      expect(response.message).toBe('Invalid credentials');
      expect(result.current.isAuthenticated).toBe(false);
    });
  });

  describe('register', () => {
    it('should register successfully', async () => {
      const userData = {
        name: 'New User',
        email: 'new@example.com',
        password: 'password123',
      };
      const token = 'test-token';
      const user = { id: 1, ...userData };

      authService.register.mockResolvedValue({
        data: { success: true, data: { token, user } },
      });

      const { result } = renderHook(() => useAuthStore());

      let response;
      await act(async () => {
        response = await result.current.register(userData);
      });

      expect(response.success).toBe(true);
      expect(result.current.isAuthenticated).toBe(true);
      expect(result.current.user).toEqual(user);
      expect(result.current.token).toBe(token);
    });

    it('should handle registration failure', async () => {
      const userData = { email: 'existing@example.com' };

      authService.register.mockRejectedValue({
        response: { data: { message: 'Email already exists' } },
      });

      const { result } = renderHook(() => useAuthStore());

      let response;
      await act(async () => {
        response = await result.current.register(userData);
      });

      expect(response.success).toBe(false);
      expect(response.message).toBe('Email already exists');
    });
  });

  describe('logout', () => {
    it('should logout and clear data', async () => {
      authService.logout.mockResolvedValue({});

      const { result } = renderHook(() => useAuthStore());

      // Set authenticated state first
      act(() => {
        result.current.user = { id: 1, name: 'Test' };
        result.current.token = 'test-token';
        result.current.isAuthenticated = true;
      });

      await act(async () => {
        await result.current.logout();
      });

      expect(result.current.user).toBe(null);
      expect(result.current.token).toBe(null);
      expect(result.current.isAuthenticated).toBe(false);
      expect(AsyncStorage.removeItem).toHaveBeenCalledWith('css_auth_token');
      expect(AsyncStorage.removeItem).toHaveBeenCalledWith('css_user_data');
    });
  });

  describe('updateProfile', () => {
    it('should update profile successfully', async () => {
      const updatedUser = { id: 1, name: 'Updated Name', email: 'test@example.com' };

      authService.updateProfile.mockResolvedValue({
        data: { success: true, data: updatedUser },
      });

      const { result } = renderHook(() => useAuthStore());

      let response;
      await act(async () => {
        response = await result.current.updateProfile({ name: 'Updated Name' });
      });

      expect(response.success).toBe(true);
      expect(result.current.user).toEqual(updatedUser);
      expect(AsyncStorage.setItem).toHaveBeenCalledWith(
        'css_user_data',
        JSON.stringify(updatedUser)
      );
    });
  });

  describe('Helper methods', () => {
    it('should check if user is premium', () => {
      const { result } = renderHook(() => useAuthStore());

      act(() => {
        result.current.user = { user_type: 'premium' };
      });

      expect(result.current.isPremium()).toBe(true);

      act(() => {
        result.current.user = { user_type: 'free' };
      });

      expect(result.current.isPremium()).toBe(false);
    });

    it('should check if user is socios', () => {
      const { result } = renderHook(() => useAuthStore());

      act(() => {
        result.current.user = { user_type: 'socios' };
      });

      expect(result.current.isSocios()).toBe(true);

      act(() => {
        result.current.user = { user_type: 'premium' };
      });

      expect(result.current.isSocios()).toBe(false);
    });

    it('should get user type info', () => {
      const { result } = renderHook(() => useAuthStore());

      act(() => {
        result.current.user = { user_type: 'premium' };
      });

      const info = result.current.getUserTypeInfo();
      expect(info.name).toBe('Premium');
      expect(info.icon).toBe('⭐');
    });
  });
});
