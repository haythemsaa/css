import { create } from 'zustand';
import { persist } from 'zustand/middleware';
import { authService } from '../services/api';

const useAuthStore = create(
  persist(
    (set, get) => ({
      // État
      user: null,
      token: null,
      isAuthenticated: false,
      isLoading: false,
      error: null,

      // Actions
      login: async (credentials) => {
        set({ isLoading: true, error: null });
        try {
          const response = await authService.login(credentials);

          if (response.success) {
            const { user, token } = response.data;

            // Sauvegarder le token
            localStorage.setItem('auth_token', token);

            set({
              user,
              token,
              isAuthenticated: true,
              isLoading: false,
              error: null,
            });

            return { success: true, user };
          }
        } catch (error) {
          set({
            error: error.message || 'Erreur de connexion',
            isLoading: false,
          });
          return { success: false, error: error.message };
        }
      },

      register: async (data) => {
        set({ isLoading: true, error: null });
        try {
          const response = await authService.register(data);

          if (response.success) {
            const { user, token } = response.data;

            // Sauvegarder le token
            localStorage.setItem('auth_token', token);

            set({
              user,
              token,
              isAuthenticated: true,
              isLoading: false,
              error: null,
            });

            return { success: true, user };
          }
        } catch (error) {
          set({
            error: error.message || "Erreur d'inscription",
            isLoading: false,
          });
          return { success: false, error: error.message };
        }
      },

      logout: async () => {
        try {
          await authService.logout();
        } catch (error) {
          console.error('Erreur lors de la déconnexion:', error);
        } finally {
          // Nettoyer le localStorage
          localStorage.removeItem('auth_token');
          localStorage.removeItem('user');

          // Réinitialiser l'état
          set({
            user: null,
            token: null,
            isAuthenticated: false,
            error: null,
          });
        }
      },

      updateProfile: async (data) => {
        set({ isLoading: true, error: null });
        try {
          const response = await authService.updateProfile(data);

          if (response.success) {
            set({
              user: response.data,
              isLoading: false,
              error: null,
            });

            return { success: true, user: response.data };
          }
        } catch (error) {
          set({
            error: error.message || 'Erreur de mise à jour',
            isLoading: false,
          });
          return { success: false, error: error.message };
        }
      },

      refreshProfile: async () => {
        if (!get().isAuthenticated) return;

        try {
          const response = await authService.getProfile();

          if (response.success) {
            set({ user: response.data });
          }
        } catch (error) {
          console.error('Erreur lors du rafraîchissement du profil:', error);
        }
      },

      // Vérifier si l'utilisateur est Premium ou Socios
      isPremium: () => {
        const { user } = get();
        return user?.user_type === 'premium' || user?.user_type === 'socios';
      },

      isSocios: () => {
        const { user } = get();
        return user?.user_type === 'socios';
      },

      // Obtenir le niveau de réduction
      getDiscountLevel: () => {
        const { user } = get();
        if (!user) return null;

        switch (user.user_type) {
          case 'socios':
            return 'socios';
          case 'premium':
            return 'premium';
          default:
            return 'free';
        }
      },

      // Clear error
      clearError: () => set({ error: null }),
    }),
    {
      name: 'auth-storage',
      partialize: (state) => ({
        user: state.user,
        token: state.token,
        isAuthenticated: state.isAuthenticated,
      }),
    }
  )
);

export default useAuthStore;
