import axios from 'axios';
import AsyncStorage from '@react-native-async-storage/async-storage';
import { API_BASE_URL } from '../constants/config';

/**
 * Stats Service
 * Gère les statistiques personnelles de l'utilisateur
 */

class StatsService {
  /**
   * Récupérer les statistiques globales
   */
  async getGlobalStats() {
    try {
      const token = await AsyncStorage.getItem('auth_token');

      const response = await axios.get(`${API_BASE_URL}/stats/global`, {
        headers: {
          Authorization: `Bearer ${token}`,
        },
      });

      if (response.data.success) {
        return response.data.data;
      }

      // Fallback: données simulées
      return this.generateMockGlobalStats();
    } catch (error) {
      console.error('Error fetching global stats:', error);
      return this.generateMockGlobalStats();
    }
  }

  /**
   * Récupérer les statistiques des codes
   */
  async getCodesStats() {
    try {
      const token = await AsyncStorage.getItem('auth_token');

      const response = await axios.get(`${API_BASE_URL}/stats/codes`, {
        headers: {
          Authorization: `Bearer ${token}`,
        },
      });

      if (response.data.success) {
        return response.data.data;
      }

      return this.generateMockCodesStats();
    } catch (error) {
      console.error('Error fetching codes stats:', error);
      return this.generateMockCodesStats();
    }
  }

  /**
   * Récupérer l'historique d'économies
   */
  async getSavingsHistory(period = 'month') {
    try {
      const token = await AsyncStorage.getItem('auth_token');

      const response = await axios.get(`${API_BASE_URL}/stats/savings`, {
        params: { period },
        headers: {
          Authorization: `Bearer ${token}`,
        },
      });

      if (response.data.success) {
        return response.data.data;
      }

      return this.generateMockSavingsHistory(period);
    } catch (error) {
      console.error('Error fetching savings history:', error);
      return this.generateMockSavingsHistory(period);
    }
  }

  /**
   * Récupérer les statistiques d'activité
   */
  async getActivityStats() {
    try {
      const token = await AsyncStorage.getItem('auth_token');

      const response = await axios.get(`${API_BASE_URL}/stats/activity`, {
        headers: {
          Authorization: `Bearer ${token}`,
        },
      });

      if (response.data.success) {
        return response.data.data;
      }

      return this.generateMockActivityStats();
    } catch (error) {
      console.error('Error fetching activity stats:', error);
      return this.generateMockActivityStats();
    }
  }

  /**
   * Récupérer les partenaires les plus utilisés
   */
  async getTopPartners() {
    try {
      const token = await AsyncStorage.getItem('auth_token');

      const response = await axios.get(`${API_BASE_URL}/stats/top-partners`, {
        headers: {
          Authorization: `Bearer ${token}`,
        },
      });

      if (response.data.success) {
        return response.data.data;
      }

      return this.generateMockTopPartners();
    } catch (error) {
      console.error('Error fetching top partners:', error);
      return this.generateMockTopPartners();
    }
  }

  // =============== MOCK DATA GENERATORS ===============

  generateMockGlobalStats() {
    return {
      totalCodes: 45,
      usedCodes: 32,
      totalSavings: 450.50,
      averageSaving: 14.08,
      loyaltyPoints: 1250,
      currentLevel: 'Gold',
      nextLevel: 'Platinum',
      pointsToNextLevel: 750,
    };
  }

  generateMockCodesStats() {
    return {
      byType: [
        { type: 'qr', count: 25, percentage: 55.6 },
        { type: 'promo', count: 15, percentage: 33.3 },
        { type: 'nfc', count: 5, percentage: 11.1 },
      ],
      byStatus: [
        { status: 'used', count: 32, percentage: 71.1 },
        { status: 'active', count: 8, percentage: 17.8 },
        { status: 'expired', count: 5, percentage: 11.1 },
      ],
      byCategory: [
        { category: 'Restauration', count: 18, savings: 180.25 },
        { category: 'Shopping', count: 10, savings: 150.00 },
        { category: 'Sport', count: 8, savings: 95.50 },
        { category: 'Santé', count: 5, savings: 24.75 },
        { category: 'Loisirs', count: 4, savings: 0 },
      ],
    };
  }

  generateMockSavingsHistory(period) {
    const months = ['Jan', 'Fév', 'Mar', 'Avr', 'Mai', 'Juin'];
    const weeks = ['S1', 'S2', 'S3', 'S4'];
    const days = ['Lun', 'Mar', 'Mer', 'Jeu', 'Ven', 'Sam', 'Dim'];

    let labels, data;

    switch (period) {
      case 'week':
        labels = days;
        data = [15.5, 22.0, 0, 18.75, 30.25, 12.0, 25.5];
        break;
      case 'year':
        labels = ['Jan', 'Fév', 'Mar', 'Avr', 'Mai', 'Juin', 'Juil', 'Aoû', 'Sep', 'Oct', 'Nov', 'Déc'];
        data = [45, 52, 38, 65, 58, 72, 55, 48, 62, 70, 45, 50];
        break;
      case 'month':
      default:
        labels = weeks;
        data = [85.5, 125.75, 110.25, 129.0];
        break;
    }

    return { labels, data };
  }

  generateMockActivityStats() {
    return {
      lastActivity: new Date(Date.now() - 2 * 24 * 60 * 60 * 1000).toISOString(), // Il y a 2 jours
      totalSessions: 156,
      averageSessionDuration: 8.5, // minutes
      mostActiveDay: 'Samedi',
      mostActiveHour: '18:00',
      favoriteFeature: 'CSS Privilèges',
      recentActivities: [
        {
          id: 1,
          type: 'code_used',
          description: 'Code utilisé - Restaurant Le Corail',
          amount: 15.5,
          date: new Date(Date.now() - 2 * 24 * 60 * 60 * 1000).toISOString(),
        },
        {
          id: 2,
          type: 'code_generated',
          description: 'Code généré - Pharmacie Ibn Sina',
          date: new Date(Date.now() - 5 * 24 * 60 * 60 * 1000).toISOString(),
        },
        {
          id: 3,
          type: 'content_viewed',
          description: 'Vidéo consultée - Résumé Match CSS vs EST',
          date: new Date(Date.now() - 7 * 24 * 60 * 60 * 1000).toISOString(),
        },
        {
          id: 4,
          type: 'points_earned',
          description: 'Points gagnés - Achat chez Decathlon',
          amount: 25,
          date: new Date(Date.now() - 10 * 24 * 60 * 60 * 1000).toISOString(),
        },
        {
          id: 5,
          type: 'level_up',
          description: 'Niveau atteint - Gold',
          date: new Date(Date.now() - 15 * 24 * 60 * 60 * 1000).toISOString(),
        },
      ],
    };
  }

  generateMockTopPartners() {
    return [
      {
        id: 1,
        name: 'Restaurant Le Corail',
        category: 'Restauration',
        uses: 12,
        savings: 145.50,
        logo: '🍽️',
      },
      {
        id: 2,
        name: 'Carrefour Sfax',
        category: 'Shopping',
        uses: 8,
        savings: 95.25,
        logo: '🛒',
      },
      {
        id: 3,
        name: 'Salle Fitness Pro',
        category: 'Sport',
        uses: 6,
        savings: 75.00,
        logo: '💪',
      },
      {
        id: 4,
        name: 'Pharmacie Ibn Sina',
        category: 'Santé',
        uses: 4,
        savings: 24.75,
        logo: '💊',
      },
      {
        id: 5,
        name: 'Pathé Cinéma',
        category: 'Loisirs',
        uses: 2,
        savings: 20.00,
        logo: '🎬',
      },
    ];
  }
}

export default new StatsService();
