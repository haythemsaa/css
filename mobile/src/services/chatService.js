import axios from 'axios';
import AsyncStorage from '@react-native-async-storage/async-storage';
import { API_BASE_URL } from '../constants/config';

/**
 * Chat Service
 * Gère le chat support en temps réel avec l'équipe CSS
 */

class ChatService {
  constructor() {
    this.messages = [];
    this.listeners = [];
  }

  /**
   * Récupérer l'historique des messages
   */
  async getMessages() {
    try {
      const token = await AsyncStorage.getItem('auth_token');

      const response = await axios.get(`${API_BASE_URL}/support/messages`, {
        headers: {
          Authorization: `Bearer ${token}`,
        },
      });

      if (response.data.success) {
        this.messages = response.data.data.messages;
        this.notifyListeners();
        return this.messages;
      }

      return [];
    } catch (error) {
      console.error('Error fetching messages:', error);

      // Retourner messages en cache si erreur
      const cachedMessages = await AsyncStorage.getItem('chat_messages');
      if (cachedMessages) {
        this.messages = JSON.parse(cachedMessages);
        return this.messages;
      }

      return [];
    }
  }

  /**
   * Envoyer un message
   */
  async sendMessage(text) {
    try {
      const token = await AsyncStorage.getItem('auth_token');
      const user = await AsyncStorage.getItem('user');
      const userData = JSON.parse(user);

      // Créer le message localement d'abord (optimistic update)
      const tempMessage = {
        id: `temp-${Date.now()}`,
        text,
        sender: 'user',
        user: {
          id: userData.id,
          name: userData.name,
        },
        created_at: new Date().toISOString(),
        pending: true,
      };

      this.messages.push(tempMessage);
      this.notifyListeners();

      // Envoyer au serveur
      const response = await axios.post(
        `${API_BASE_URL}/support/messages`,
        { text },
        {
          headers: {
            Authorization: `Bearer ${token}`,
          },
        }
      );

      if (response.data.success) {
        // Remplacer le message temporaire par le message réel
        const index = this.messages.findIndex(m => m.id === tempMessage.id);
        if (index !== -1) {
          this.messages[index] = response.data.data.message;
          this.notifyListeners();
        }

        // Sauvegarder en cache
        await AsyncStorage.setItem('chat_messages', JSON.stringify(this.messages));

        return response.data.data.message;
      }

      return tempMessage;
    } catch (error) {
      console.error('Error sending message:', error);
      throw error;
    }
  }

  /**
   * Marquer les messages comme lus
   */
  async markAsRead() {
    try {
      const token = await AsyncStorage.getItem('auth_token');

      await axios.post(
        `${API_BASE_URL}/support/messages/read`,
        {},
        {
          headers: {
            Authorization: `Bearer ${token}`,
          },
        }
      );
    } catch (error) {
      console.error('Error marking messages as read:', error);
    }
  }

  /**
   * Simuler une réponse de support (pour démo)
   */
  simulateAdminResponse() {
    setTimeout(() => {
      const responses = [
        "Bonjour ! Comment puis-je vous aider aujourd'hui ?",
        "Je vais vérifier cela pour vous.",
        "Merci de votre patience. Un agent va vous répondre sous peu.",
        "Votre demande a été enregistrée. Nous vous reviendrons dans les plus brefs délais.",
        "Pour toute urgence, vous pouvez nous contacter au +216 74 123 456",
      ];

      const randomResponse = responses[Math.floor(Math.random() * responses.length)];

      const adminMessage = {
        id: `admin-${Date.now()}`,
        text: randomResponse,
        sender: 'admin',
        user: {
          id: 'admin',
          name: 'Support CSS',
          avatar: '👨‍💼',
        },
        created_at: new Date().toISOString(),
      };

      this.messages.push(adminMessage);
      this.notifyListeners();

      // Sauvegarder en cache
      AsyncStorage.setItem('chat_messages', JSON.stringify(this.messages));
    }, 2000 + Math.random() * 3000); // Réponse entre 2-5 secondes
  }

  /**
   * Polling pour nouveaux messages (simule temps réel)
   */
  startPolling() {
    this.pollingInterval = setInterval(async () => {
      try {
        const newMessages = await this.getMessages();
        if (newMessages.length > this.messages.length) {
          this.notifyListeners();
        }
      } catch (error) {
        console.error('Polling error:', error);
      }
    }, 5000); // Poll toutes les 5 secondes
  }

  stopPolling() {
    if (this.pollingInterval) {
      clearInterval(this.pollingInterval);
      this.pollingInterval = null;
    }
  }

  /**
   * S'abonner aux changements de messages
   */
  subscribe(listener) {
    this.listeners.push(listener);
    return () => {
      this.listeners = this.listeners.filter(l => l !== listener);
    };
  }

  /**
   * Notifier tous les listeners
   */
  notifyListeners() {
    this.listeners.forEach(listener => listener(this.messages));
  }

  /**
   * Obtenir le nombre de messages non lus
   */
  getUnreadCount() {
    return this.messages.filter(m => m.sender === 'admin' && !m.read).length;
  }

  /**
   * Effacer l'historique local
   */
  async clearHistory() {
    this.messages = [];
    await AsyncStorage.removeItem('chat_messages');
    this.notifyListeners();
  }
}

export default new ChatService();
