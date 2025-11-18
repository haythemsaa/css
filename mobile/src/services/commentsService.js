import axios from 'axios';
import AsyncStorage from '@react-native-async-storage/async-storage';
import { API_BASE_URL } from '../constants/config';

/**
 * Comments Service
 * Gère les commentaires sur le contenu
 */

class CommentsService {
  /**
   * Récupérer les commentaires d'un contenu
   */
  async getComments(contentSlug) {
    try {
      const token = await AsyncStorage.getItem('auth_token');

      const response = await axios.get(
        `${API_BASE_URL}/content/${contentSlug}/comments`,
        {
          headers: {
            Authorization: `Bearer ${token}`,
          },
        }
      );

      if (response.data.success) {
        return response.data.data.comments;
      }

      // Fallback: commentaires mockés
      return this.generateMockComments();
    } catch (error) {
      console.error('Error fetching comments:', error);
      return this.generateMockComments();
    }
  }

  /**
   * Ajouter un commentaire
   */
  async addComment(contentSlug, text) {
    try {
      const token = await AsyncStorage.getItem('auth_token');
      const user = await AsyncStorage.getItem('user');
      const userData = JSON.parse(user);

      const response = await axios.post(
        `${API_BASE_URL}/content/${contentSlug}/comments`,
        { text },
        {
          headers: {
            Authorization: `Bearer ${token}`,
          },
        }
      );

      if (response.data.success) {
        return response.data.data.comment;
      }

      // Fallback: créer un commentaire local
      return {
        id: `temp-${Date.now()}`,
        text,
        user: {
          id: userData.id,
          name: userData.name,
          avatar: null,
        },
        likes_count: 0,
        is_liked: false,
        created_at: new Date().toISOString(),
      };
    } catch (error) {
      console.error('Error adding comment:', error);
      throw error;
    }
  }

  /**
   * Supprimer un commentaire
   */
  async deleteComment(commentId) {
    try {
      const token = await AsyncStorage.getItem('auth_token');

      const response = await axios.delete(
        `${API_BASE_URL}/comments/${commentId}`,
        {
          headers: {
            Authorization: `Bearer ${token}`,
          },
        }
      );

      return response.data.success;
    } catch (error) {
      console.error('Error deleting comment:', error);
      throw error;
    }
  }

  /**
   * Liker un commentaire
   */
  async likeComment(commentId) {
    try {
      const token = await AsyncStorage.getItem('auth_token');

      const response = await axios.post(
        `${API_BASE_URL}/comments/${commentId}/like`,
        {},
        {
          headers: {
            Authorization: `Bearer ${token}`,
          },
        }
      );

      return response.data.success;
    } catch (error) {
      console.error('Error liking comment:', error);
      throw error;
    }
  }

  /**
   * Unliker un commentaire
   */
  async unlikeComment(commentId) {
    try {
      const token = await AsyncStorage.getItem('auth_token');

      const response = await axios.post(
        `${API_BASE_URL}/comments/${commentId}/unlike`,
        {},
        {
          headers: {
            Authorization: `Bearer ${token}`,
          },
        }
      );

      return response.data.success;
    } catch (error) {
      console.error('Error unliking comment:', error);
      throw error;
    }
  }

  /**
   * Signaler un commentaire
   */
  async reportComment(commentId, reason) {
    try {
      const token = await AsyncStorage.getItem('auth_token');

      const response = await axios.post(
        `${API_BASE_URL}/comments/${commentId}/report`,
        { reason },
        {
          headers: {
            Authorization: `Bearer ${token}`,
          },
        }
      );

      return response.data.success;
    } catch (error) {
      console.error('Error reporting comment:', error);
      throw error;
    }
  }

  // =============== MOCK DATA ===============

  generateMockComments() {
    return [
      {
        id: 1,
        text: 'Magnifique match ! Quelle remontée au score 🔥⚽',
        user: {
          id: 101,
          name: 'Ahmed Ben Ali',
          avatar: null,
        },
        likes_count: 24,
        is_liked: false,
        created_at: new Date(Date.now() - 2 * 60 * 60 * 1000).toISOString(), // Il y a 2h
      },
      {
        id: 2,
        text: 'Le gardien CSS était exceptionnel aujourd\'hui 🧤👏',
        user: {
          id: 102,
          name: 'Fatma Gharbi',
          avatar: null,
        },
        likes_count: 18,
        is_liked: true,
        created_at: new Date(Date.now() - 4 * 60 * 60 * 1000).toISOString(), // Il y a 4h
      },
      {
        id: 3,
        text: 'Victoire méritée ! Allez CSS 🖤⚪',
        user: {
          id: 103,
          name: 'Mohamed Trabelsi',
          avatar: null,
        },
        likes_count: 12,
        is_liked: false,
        created_at: new Date(Date.now() - 6 * 60 * 60 * 1000).toISOString(), // Il y a 6h
      },
      {
        id: 4,
        text: 'Belle ambiance au stade ! Les supporters étaient au top 📣',
        user: {
          id: 104,
          name: 'Sarra Mansour',
          avatar: null,
        },
        likes_count: 8,
        is_liked: false,
        created_at: new Date(Date.now() - 12 * 60 * 60 * 1000).toISOString(), // Il y a 12h
      },
      {
        id: 5,
        text: 'On continue comme ça ! Direction le championnat 🏆',
        user: {
          id: 105,
          name: 'Karim Jebali',
          avatar: null,
        },
        likes_count: 15,
        is_liked: false,
        created_at: new Date(Date.now() - 18 * 60 * 60 * 1000).toISOString(), // Il y a 18h
      },
    ];
  }
}

export default new CommentsService();
