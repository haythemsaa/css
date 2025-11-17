import { describe, it, expect, vi, beforeEach } from 'vitest';
import { renderHook, act, waitFor } from '@testing-library/react';
import useAuthStore from '../authStore';
import { authService } from '../../services/api';

// Mock the auth service
vi.mock('../../services/api', () => ({
  authService: {
    login: vi.fn(),
    register: vi.fn(),
    logout: vi.fn(),
    updateProfile: vi.fn(),
    getProfile: vi.fn(),
  },
}));

// Mock console.error to suppress expected errors in tests
const consoleErrorSpy = vi.spyOn(console, 'error').mockImplementation(() => {});

describe('Auth Store', () => {
  beforeEach(() => {
    localStorage.clear();
    vi.clearAllMocks();
    act(() => {
      useAuthStore.setState({
        user: null,
        token: null,
        isAuthenticated: false,
        isLoading: false,
        error: null,
      });
    });
  });

  describe('Initial State', () => {
    it('has correct initial state', () => {
      const { result } = renderHook(() => useAuthStore());

      expect(result.current.user).toBeNull();
      expect(result.current.token).toBeNull();
      expect(result.current.isAuthenticated).toBe(false);
      expect(result.current.isLoading).toBe(false);
      expect(result.current.error).toBeNull();
    });
  });

  describe('Login', () => {
    it('successfully logs in a user', async () => {
      const mockUser = {
        id: 1,
        name: 'John Doe',
        email: 'john@example.com',
        user_type: 'premium',
      };
      const mockToken = 'mock-token-123';

      authService.login.mockResolvedValue({
        success: true,
        data: { user: mockUser, token: mockToken },
      });

      const { result } = renderHook(() => useAuthStore());

      let response;
      await act(async () => {
        response = await result.current.login({
          email: 'john@example.com',
          password: 'password123',
        });
      });

      expect(response.success).toBe(true);
      expect(result.current.user).toEqual(mockUser);
      expect(result.current.token).toBe(mockToken);
      expect(result.current.isAuthenticated).toBe(true);
      expect(localStorage.getItem('auth_token')).toBe(mockToken);
    });

    it('handles login error', async () => {
      authService.login.mockRejectedValue({
        message: 'Invalid credentials',
      });

      const { result } = renderHook(() => useAuthStore());

      let response;
      await act(async () => {
        response = await result.current.login({
          email: 'wrong@example.com',
          password: 'wrongpassword',
        });
      });

      expect(response.success).toBe(false);
      expect(response.error).toBe('Invalid credentials');
      expect(result.current.isAuthenticated).toBe(false);
    });
  });

  describe('Register', () => {
    it('successfully registers a user', async () => {
      const mockUser = {
        id: 1,
        name: 'Jane Doe',
        email: 'jane@example.com',
        user_type: 'free',
      };
      const mockToken = 'mock-token-456';

      authService.register.mockResolvedValue({
        success: true,
        data: { user: mockUser, token: mockToken },
      });

      const { result } = renderHook(() => useAuthStore());

      let response;
      await act(async () => {
        response = await result.current.register({
          name: 'Jane Doe',
          email: 'jane@example.com',
          password: 'password123',
        });
      });

      expect(response.success).toBe(true);
      expect(result.current.user).toEqual(mockUser);
      expect(result.current.isAuthenticated).toBe(true);
    });
  });

  describe('Logout', () => {
    it('successfully logs out a user', async () => {
      act(() => {
        useAuthStore.setState({
          user: { id: 1, name: 'John' },
          token: 'token123',
          isAuthenticated: true,
        });
      });
      localStorage.setItem('auth_token', 'token123');

      authService.logout.mockResolvedValue({ success: true });

      const { result } = renderHook(() => useAuthStore());

      await act(async () => {
        await result.current.logout();
      });

      expect(result.current.user).toBeNull();
      expect(result.current.token).toBeNull();
      expect(result.current.isAuthenticated).toBe(false);
      expect(localStorage.getItem('auth_token')).toBeNull();
    });
  });

  describe('Helper Methods', () => {
    it('checks if user is premium', () => {
      const { result, rerender } = renderHook(() => useAuthStore());

      act(() => useAuthStore.setState({ user: { user_type: 'free' } }));
      rerender();
      expect(result.current.isPremium()).toBe(false);

      act(() => useAuthStore.setState({ user: { user_type: 'premium' } }));
      rerender();
      expect(result.current.isPremium()).toBe(true);

      act(() => useAuthStore.setState({ user: { user_type: 'socios' } }));
      rerender();
      expect(result.current.isPremium()).toBe(true);
    });

    it('checks if user is socios', () => {
      const { result, rerender } = renderHook(() => useAuthStore());

      act(() => useAuthStore.setState({ user: { user_type: 'free' } }));
      rerender();
      expect(result.current.isSocios()).toBe(false);

      act(() => useAuthStore.setState({ user: { user_type: 'socios' } }));
      rerender();
      expect(result.current.isSocios()).toBe(true);
    });

    it('gets correct discount level', () => {
      const { result, rerender } = renderHook(() => useAuthStore());

      act(() => useAuthStore.setState({ user: { user_type: 'free' } }));
      rerender();
      expect(result.current.getDiscountLevel()).toBe('free');

      act(() => useAuthStore.setState({ user: { user_type: 'premium' } }));
      rerender();
      expect(result.current.getDiscountLevel()).toBe('premium');

      act(() => useAuthStore.setState({ user: { user_type: 'socios' } }));
      rerender();
      expect(result.current.getDiscountLevel()).toBe('socios');
    });

    it('clears error', () => {
      const { result, rerender } = renderHook(() => useAuthStore());

      act(() => useAuthStore.setState({ error: 'Some error' }));
      rerender();
      expect(result.current.error).toBe('Some error');

      act(() => result.current.clearError());
      expect(result.current.error).toBeNull();
    });
  });
});
