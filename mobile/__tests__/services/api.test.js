import axios from 'axios';
import AsyncStorage from '@react-native-async-storage/async-storage';
import {
  authService,
  partnersService,
  offersService,
  codesService,
  contentService,
  playersService,
  matchesService,
} from '../../src/services/api';

// Mock axios
jest.mock('axios');

describe('API Services', () => {
  let mockApi;

  beforeEach(() => {
    // Reset mocks
    jest.clearAllMocks();

    // Setup mock axios instance
    mockApi = {
      post: jest.fn(() => Promise.resolve({ data: {} })),
      get: jest.fn(() => Promise.resolve({ data: {} })),
      put: jest.fn(() => Promise.resolve({ data: {} })),
      delete: jest.fn(() => Promise.resolve({ data: {} })),
      interceptors: {
        request: { use: jest.fn() },
        response: { use: jest.fn() },
      },
    };

    axios.create.mockReturnValue(mockApi);
  });

  describe('authService', () => {
    it('should login with credentials', async () => {
      const credentials = { email: 'test@example.com', password: 'password123' };
      mockApi.post.mockResolvedValueOnce({ data: { token: 'test-token' } });

      const result = await authService.login(credentials);

      expect(mockApi.post).toHaveBeenCalledWith('/auth/login', credentials);
      expect(result.data).toEqual({ token: 'test-token' });
    });

    it('should register new user', async () => {
      const userData = {
        name: 'Test User',
        email: 'test@example.com',
        password: 'password123',
      };
      mockApi.post.mockResolvedValueOnce({ data: { user: userData } });

      const result = await authService.register(userData);

      expect(mockApi.post).toHaveBeenCalledWith('/auth/register', userData);
      expect(result.data.user).toEqual(userData);
    });

    it('should logout user', async () => {
      await authService.logout();
      expect(mockApi.post).toHaveBeenCalledWith('/auth/logout');
    });

    it('should get user profile', async () => {
      const profile = { id: 1, name: 'Test User', email: 'test@example.com' };
      mockApi.get.mockResolvedValueOnce({ data: profile });

      const result = await authService.getProfile();

      expect(mockApi.get).toHaveBeenCalledWith('/auth/profile');
      expect(result.data).toEqual(profile);
    });

    it('should update user profile', async () => {
      const updates = { name: 'Updated Name' };
      mockApi.put.mockResolvedValueOnce({ data: { success: true } });

      await authService.updateProfile(updates);

      expect(mockApi.put).toHaveBeenCalledWith('/auth/profile', updates);
    });

    it('should change password', async () => {
      const passwords = { current_password: 'old', new_password: 'new' };
      mockApi.put.mockResolvedValueOnce({ data: { success: true } });

      await authService.changePassword(passwords);

      expect(mockApi.put).toHaveBeenCalledWith('/auth/password', passwords);
    });
  });

  describe('partnersService', () => {
    it('should get partner categories', async () => {
      const categories = [{ id: 1, name: 'Restaurant' }];
      mockApi.get.mockResolvedValueOnce({ data: categories });

      const result = await partnersService.getCategories();

      expect(mockApi.get).toHaveBeenCalledWith('/partners/categories');
      expect(result.data).toEqual(categories);
    });

    it('should get partners list', async () => {
      const params = { category: 'restaurant' };
      mockApi.get.mockResolvedValueOnce({ data: [] });

      await partnersService.getPartners(params);

      expect(mockApi.get).toHaveBeenCalledWith('/partners', { params });
    });

    it('should get single partner', async () => {
      const slug = 'partner-slug';
      mockApi.get.mockResolvedValueOnce({ data: { slug } });

      await partnersService.getPartner(slug);

      expect(mockApi.get).toHaveBeenCalledWith(`/partners/${slug}`);
    });

    it('should get nearby partners', async () => {
      const lat = 34.74;
      const lng = 10.76;
      mockApi.get.mockResolvedValueOnce({ data: [] });

      await partnersService.getNearby(lat, lng);

      expect(mockApi.get).toHaveBeenCalledWith('/partners/nearby', {
        params: { latitude: lat, longitude: lng, radius: 10 },
      });
    });
  });

  describe('offersService', () => {
    it('should get offers for a partner', async () => {
      const partnerSlug = 'partner-slug';
      mockApi.get.mockResolvedValueOnce({ data: [] });

      await offersService.getOffers(partnerSlug);

      expect(mockApi.get).toHaveBeenCalledWith(`/partners/${partnerSlug}/offers`, { params: {} });
    });

    it('should get single offer', async () => {
      const slug = 'offer-slug';
      mockApi.get.mockResolvedValueOnce({ data: { slug } });

      await offersService.getOffer(slug);

      expect(mockApi.get).toHaveBeenCalledWith(`/offers/${slug}`);
    });
  });

  describe('codesService', () => {
    it('should generate code', async () => {
      const offerSlug = 'offer-slug';
      mockApi.post.mockResolvedValueOnce({ data: { code: 'QR-12345' } });

      const result = await codesService.generateCode(offerSlug);

      expect(mockApi.post).toHaveBeenCalledWith(`/codes/generate/${offerSlug}`, { type: 'qr' });
      expect(result.data.code).toBe('QR-12345');
    });

    it('should get user codes', async () => {
      mockApi.get.mockResolvedValueOnce({ data: [] });

      await codesService.getMyCodes();

      expect(mockApi.get).toHaveBeenCalledWith('/codes/my-codes', { params: {} });
    });

    it('should validate code', async () => {
      const code = 'QR-12345';
      mockApi.post.mockResolvedValueOnce({ data: { valid: true } });

      const result = await codesService.validateCode(code);

      expect(mockApi.post).toHaveBeenCalledWith('/codes/validate', { code });
      expect(result.data.valid).toBe(true);
    });

    it('should use code', async () => {
      const code = 'QR-12345';
      const amount = 100;
      mockApi.post.mockResolvedValueOnce({ data: { success: true } });

      await codesService.useCode(code, amount);

      expect(mockApi.post).toHaveBeenCalledWith(`/codes/${code}/use`, { amount });
    });
  });

  describe('contentService', () => {
    it('should get content list', async () => {
      mockApi.get.mockResolvedValueOnce({ data: [] });

      await contentService.getContent();

      expect(mockApi.get).toHaveBeenCalledWith('/content', { params: {} });
    });

    it('should get content detail', async () => {
      const slug = 'content-slug';
      mockApi.get.mockResolvedValueOnce({ data: { slug } });

      await contentService.getContentDetail(slug);

      expect(mockApi.get).toHaveBeenCalledWith(`/content/${slug}`);
    });

    it('should like content', async () => {
      const id = 1;
      mockApi.post.mockResolvedValueOnce({ data: { success: true } });

      await contentService.likeContent(id);

      expect(mockApi.post).toHaveBeenCalledWith(`/content/${id}/like`);
    });

    it('should unlike content', async () => {
      const id = 1;
      mockApi.delete.mockResolvedValueOnce({ data: { success: true } });

      await contentService.unlikeContent(id);

      expect(mockApi.delete).toHaveBeenCalledWith(`/content/${id}/like`);
    });
  });

  describe('playersService', () => {
    it('should get players list', async () => {
      mockApi.get.mockResolvedValueOnce({ data: [] });

      await playersService.getPlayers();

      expect(mockApi.get).toHaveBeenCalledWith('/players', { params: {} });
    });

    it('should get single player', async () => {
      const id = 1;
      mockApi.get.mockResolvedValueOnce({ data: { id } });

      await playersService.getPlayer(id);

      expect(mockApi.get).toHaveBeenCalledWith(`/players/${id}`);
    });
  });

  describe('matchesService', () => {
    it('should get matches list', async () => {
      mockApi.get.mockResolvedValueOnce({ data: [] });

      await matchesService.getMatches();

      expect(mockApi.get).toHaveBeenCalledWith('/matches', { params: {} });
    });

    it('should get upcoming matches', async () => {
      mockApi.get.mockResolvedValueOnce({ data: [] });

      await matchesService.getUpcoming();

      expect(mockApi.get).toHaveBeenCalledWith('/matches/upcoming', { params: {} });
    });

    it('should get match results', async () => {
      mockApi.get.mockResolvedValueOnce({ data: [] });

      await matchesService.getResults();

      expect(mockApi.get).toHaveBeenCalledWith('/matches/results', { params: {} });
    });

    it('should get single match', async () => {
      const id = 1;
      mockApi.get.mockResolvedValueOnce({ data: { id } });

      await matchesService.getMatch(id);

      expect(mockApi.get).toHaveBeenCalledWith(`/matches/${id}`);
    });
  });
});
