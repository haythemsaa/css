import axios from 'axios';
import AsyncStorage from '@react-native-async-storage/async-storage';
import statsService from '../../src/services/statsService';

// Mock axios
jest.mock('axios');

// Mock AsyncStorage
jest.mock('@react-native-async-storage/async-storage', () => ({
  getItem: jest.fn(),
}));

describe('StatsService', () => {
  const mockToken = 'mock-token-123';

  beforeEach(() => {
    jest.clearAllMocks();
    AsyncStorage.getItem.mockResolvedValue(mockToken);
  });

  describe('getGlobalStats', () => {
    it('should fetch global statistics from API', async () => {
      const mockStats = {
        totalCodes: 25,
        usedCodes: 18,
        totalSavings: 450.5,
        loyaltyPoints: 1250,
        currentLevel: 'Gold',
      };

      axios.get.mockResolvedValueOnce({
        data: {
          success: true,
          data: { stats: mockStats },
        },
      });

      const stats = await statsService.getGlobalStats();

      expect(axios.get).toHaveBeenCalledWith(
        expect.stringContaining('/stats/global'),
        expect.objectContaining({
          headers: { Authorization: `Bearer ${mockToken}` },
        })
      );
      expect(stats).toEqual(mockStats);
    });

    it('should return mock stats on API error', async () => {
      axios.get.mockRejectedValueOnce(new Error('Network error'));

      const stats = await statsService.getGlobalStats();

      expect(stats).toHaveProperty('totalCodes');
      expect(stats).toHaveProperty('totalSavings');
      expect(stats).toHaveProperty('loyaltyPoints');
      expect(stats.totalCodes).toBeGreaterThan(0);
    });
  });

  describe('getCodesByType', () => {
    it('should fetch codes breakdown by type', async () => {
      const mockCodesByType = [
        { type: 'qr', count: 15 },
        { type: 'barcode', count: 10 },
      ];

      axios.get.mockResolvedValueOnce({
        data: {
          success: true,
          data: { codesByType: mockCodesByType },
        },
      });

      const result = await statsService.getCodesByType();

      expect(axios.get).toHaveBeenCalledWith(
        expect.stringContaining('/stats/codes/by-type'),
        expect.objectContaining({
          headers: { Authorization: `Bearer ${mockToken}` },
        })
      );
      expect(result).toEqual(mockCodesByType);
    });

    it('should return mock data on API error', async () => {
      axios.get.mockRejectedValueOnce(new Error('Network error'));

      const result = await statsService.getCodesByType();

      expect(Array.isArray(result)).toBe(true);
      expect(result.length).toBeGreaterThan(0);
      expect(result[0]).toHaveProperty('type');
      expect(result[0]).toHaveProperty('count');
    });
  });

  describe('getCodesByStatus', () => {
    it('should fetch codes breakdown by status', async () => {
      const mockCodesByStatus = [
        { status: 'active', count: 12 },
        { status: 'used', count: 8 },
        { status: 'expired', count: 5 },
      ];

      axios.get.mockResolvedValueOnce({
        data: {
          success: true,
          data: { codesByStatus: mockCodesByStatus },
        },
      });

      const result = await statsService.getCodesByStatus();

      expect(axios.get).toHaveBeenCalledWith(
        expect.stringContaining('/stats/codes/by-status'),
        expect.objectContaining({
          headers: { Authorization: `Bearer ${mockToken}` },
        })
      );
      expect(result).toEqual(mockCodesByStatus);
    });
  });

  describe('getCodesByCategory', () => {
    it('should fetch codes breakdown by category', async () => {
      const mockCodesByCategory = [
        { category: 'Restaurant', count: 10 },
        { category: 'Mode', count: 8 },
      ];

      axios.get.mockResolvedValueOnce({
        data: {
          success: true,
          data: { codesByCategory: mockCodesByCategory },
        },
      });

      const result = await statsService.getCodesByCategory();

      expect(result).toEqual(mockCodesByCategory);
    });
  });

  describe('getSavingsHistory', () => {
    it('should fetch savings history for specified period', async () => {
      const mockHistory = {
        labels: ['Lun', 'Mar', 'Mer', 'Jeu', 'Ven', 'Sam', 'Dim'],
        data: [15.5, 22.0, 0, 18.75, 30.25, 12.0, 25.5],
      };

      axios.get.mockResolvedValueOnce({
        data: {
          success: true,
          data: { history: mockHistory },
        },
      });

      const result = await statsService.getSavingsHistory('week');

      expect(axios.get).toHaveBeenCalledWith(
        expect.stringContaining('/stats/savings/week'),
        expect.objectContaining({
          headers: { Authorization: `Bearer ${mockToken}` },
        })
      );
      expect(result).toEqual(mockHistory);
      expect(result.labels).toHaveLength(7);
      expect(result.data).toHaveLength(7);
    });

    it('should generate mock data for month period', async () => {
      axios.get.mockRejectedValueOnce(new Error('Network error'));

      const result = await statsService.getSavingsHistory('month');

      expect(result.labels).toHaveLength(4);
      expect(result.data).toHaveLength(4);
      expect(result.labels).toContain('Semaine 1');
    });

    it('should generate mock data for year period', async () => {
      axios.get.mockRejectedValueOnce(new Error('Network error'));

      const result = await statsService.getSavingsHistory('year');

      expect(result.labels).toHaveLength(12);
      expect(result.data).toHaveLength(12);
      expect(result.labels[0]).toBe('Jan');
    });

    it('should default to month period if invalid period provided', async () => {
      axios.get.mockRejectedValueOnce(new Error('Network error'));

      const result = await statsService.getSavingsHistory('invalid');

      expect(result.labels).toHaveLength(4);
    });
  });

  describe('getTopPartners', () => {
    it('should fetch top partners by usage', async () => {
      const mockPartners = [
        {
          partner: { name: 'Restaurant A', logo: 'logo1.png' },
          usageCount: 15,
          totalSavings: 250.5,
        },
        {
          partner: { name: 'Shop B', logo: 'logo2.png' },
          usageCount: 12,
          totalSavings: 180.0,
        },
      ];

      axios.get.mockResolvedValueOnce({
        data: {
          success: true,
          data: { topPartners: mockPartners },
        },
      });

      const result = await statsService.getTopPartners();

      expect(axios.get).toHaveBeenCalledWith(
        expect.stringContaining('/stats/top-partners'),
        expect.objectContaining({
          headers: { Authorization: `Bearer ${mockToken}` },
        })
      );
      expect(result).toEqual(mockPartners);
    });

    it('should return mock partners on API error', async () => {
      axios.get.mockRejectedValueOnce(new Error('Network error'));

      const result = await statsService.getTopPartners();

      expect(Array.isArray(result)).toBe(true);
      expect(result.length).toBeGreaterThan(0);
      expect(result[0]).toHaveProperty('partner');
      expect(result[0]).toHaveProperty('usageCount');
      expect(result[0]).toHaveProperty('totalSavings');
    });

    it('should limit to 5 partners', async () => {
      axios.get.mockRejectedValueOnce(new Error('Network error'));

      const result = await statsService.getTopPartners();

      expect(result.length).toBeLessThanOrEqual(5);
    });
  });

  describe('getRecentActivities', () => {
    it('should fetch recent user activities', async () => {
      const mockActivities = [
        {
          type: 'code_generated',
          description: 'Code généré pour Restaurant A',
          date: '2025-01-18T10:00:00Z',
          icon: '🎫',
        },
        {
          type: 'code_used',
          description: 'Code utilisé chez Shop B',
          date: '2025-01-17T15:30:00Z',
          icon: '✅',
        },
      ];

      axios.get.mockResolvedValueOnce({
        data: {
          success: true,
          data: { activities: mockActivities },
        },
      });

      const result = await statsService.getRecentActivities();

      expect(axios.get).toHaveBeenCalledWith(
        expect.stringContaining('/stats/activities'),
        expect.objectContaining({
          headers: { Authorization: `Bearer ${mockToken}` },
        })
      );
      expect(result).toEqual(mockActivities);
    });

    it('should return mock activities on API error', async () => {
      axios.get.mockRejectedValueOnce(new Error('Network error'));

      const result = await statsService.getRecentActivities();

      expect(Array.isArray(result)).toBe(true);
      expect(result.length).toBeGreaterThan(0);
      expect(result[0]).toHaveProperty('type');
      expect(result[0]).toHaveProperty('description');
      expect(result[0]).toHaveProperty('date');
      expect(result[0]).toHaveProperty('icon');
    });

    it('should limit to 10 activities', async () => {
      axios.get.mockRejectedValueOnce(new Error('Network error'));

      const result = await statsService.getRecentActivities();

      expect(result.length).toBeLessThanOrEqual(10);
    });
  });

  describe('mock data generators', () => {
    it('should generate consistent mock global stats', () => {
      const stats1 = statsService.generateMockGlobalStats();
      const stats2 = statsService.generateMockGlobalStats();

      expect(stats1).toEqual(stats2);
      expect(stats1.totalCodes).toBe(25);
    });

    it('should generate valid savings history for week', () => {
      const history = statsService.generateMockSavingsHistory('week');

      expect(history.labels.length).toBe(7);
      expect(history.data.length).toBe(7);
      expect(history.labels[0]).toBe('Lun');
    });

    it('should generate valid top partners', () => {
      const partners = statsService.generateMockTopPartners();

      expect(partners.length).toBe(5);
      partners.forEach((partner) => {
        expect(partner.partner.name).toBeTruthy();
        expect(partner.usageCount).toBeGreaterThan(0);
        expect(partner.totalSavings).toBeGreaterThan(0);
      });
    });

    it('should generate valid recent activities', () => {
      const activities = statsService.generateMockRecentActivities();

      expect(activities.length).toBe(10);
      activities.forEach((activity) => {
        expect(['code_generated', 'code_used', 'savings_earned', 'level_up']).toContain(
          activity.type
        );
        expect(activity.icon).toBeTruthy();
        expect(activity.description).toBeTruthy();
      });
    });
  });
});
