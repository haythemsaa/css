import AsyncStorage from '@react-native-async-storage/async-storage';
import NetInfo from '@react-native-community/netinfo';

class CacheService {
  constructor() {
    this.isOnline = true;
    this.syncQueue = [];
    this.CACHE_EXPIRATION = 30 * 60 * 1000; // 30 minutes par défaut
  }

  /**
   * Initialiser le service et écouter les changements de connexion
   */
  async initialize() {
    // Vérifier l'état initial de la connexion
    const state = await NetInfo.fetch();
    this.isOnline = state.isConnected;

    // Écouter les changements de connexion
    NetInfo.addEventListener((state) => {
      const wasOffline = !this.isOnline;
      this.isOnline = state.isConnected;

      // Si on vient de se reconnecter, synchroniser
      if (wasOffline && this.isOnline) {
        console.log('Connexion restaurée, synchronisation...');
        this.syncPendingActions();
      }
    });

    // Charger la file d'attente de sync depuis le storage
    await this.loadSyncQueue();
  }

  /**
   * Vérifier si on est en ligne
   */
  async checkConnection() {
    const state = await NetInfo.fetch();
    this.isOnline = state.isConnected;
    return this.isOnline;
  }

  /**
   * Sauvegarder des données dans le cache
   */
  async set(key, data, expirationMs = this.CACHE_EXPIRATION) {
    try {
      const cacheData = {
        data,
        timestamp: Date.now(),
        expiration: expirationMs,
      };
      await AsyncStorage.setItem(`cache_${key}`, JSON.stringify(cacheData));
      return true;
    } catch (error) {
      console.error('Erreur lors de la sauvegarde dans le cache:', error);
      return false;
    }
  }

  /**
   * Récupérer des données du cache
   */
  async get(key, allowExpired = false) {
    try {
      const cached = await AsyncStorage.getItem(`cache_${key}`);
      if (!cached) return null;

      const { data, timestamp, expiration } = JSON.parse(cached);
      const age = Date.now() - timestamp;

      // Vérifier si le cache est expiré
      if (!allowExpired && age > expiration) {
        console.log(`Cache expiré pour ${key}`);
        await this.remove(key);
        return null;
      }

      return data;
    } catch (error) {
      console.error('Erreur lors de la récupération du cache:', error);
      return null;
    }
  }

  /**
   * Supprimer une entrée du cache
   */
  async remove(key) {
    try {
      await AsyncStorage.removeItem(`cache_${key}`);
      return true;
    } catch (error) {
      console.error('Erreur lors de la suppression du cache:', error);
      return false;
    }
  }

  /**
   * Nettoyer tout le cache
   */
  async clearAll() {
    try {
      const keys = await AsyncStorage.getAllKeys();
      const cacheKeys = keys.filter((key) => key.startsWith('cache_'));
      await AsyncStorage.multiRemove(cacheKeys);
      return true;
    } catch (error) {
      console.error('Erreur lors du nettoyage du cache:', error);
      return false;
    }
  }

  /**
   * Obtenir la taille du cache
   */
  async getCacheSize() {
    try {
      const keys = await AsyncStorage.getAllKeys();
      const cacheKeys = keys.filter((key) => key.startsWith('cache_'));
      return cacheKeys.length;
    } catch (error) {
      console.error('Erreur lors du calcul de la taille du cache:', error);
      return 0;
    }
  }

  /**
   * Ajouter une action à la file d'attente de synchronisation
   */
  async addToSyncQueue(action) {
    try {
      this.syncQueue.push({
        ...action,
        timestamp: Date.now(),
        id: `${Date.now()}_${Math.random().toString(36).substr(2, 9)}`,
      });
      await this.saveSyncQueue();
      return true;
    } catch (error) {
      console.error('Erreur lors de l\'ajout à la file de sync:', error);
      return false;
    }
  }

  /**
   * Sauvegarder la file d'attente de sync
   */
  async saveSyncQueue() {
    try {
      await AsyncStorage.setItem('sync_queue', JSON.stringify(this.syncQueue));
      return true;
    } catch (error) {
      console.error('Erreur lors de la sauvegarde de la file de sync:', error);
      return false;
    }
  }

  /**
   * Charger la file d'attente de sync
   */
  async loadSyncQueue() {
    try {
      const queue = await AsyncStorage.getItem('sync_queue');
      this.syncQueue = queue ? JSON.parse(queue) : [];
      return this.syncQueue;
    } catch (error) {
      console.error('Erreur lors du chargement de la file de sync:', error);
      this.syncQueue = [];
      return [];
    }
  }

  /**
   * Synchroniser les actions en attente
   */
  async syncPendingActions() {
    if (!this.isOnline || this.syncQueue.length === 0) {
      return { success: true, synced: 0, failed: 0 };
    }

    console.log(`Synchronisation de ${this.syncQueue.length} actions...`);

    let synced = 0;
    let failed = 0;
    const failedActions = [];

    for (const action of this.syncQueue) {
      try {
        // Exécuter l'action en fonction de son type
        await this.executeAction(action);
        synced++;
      } catch (error) {
        console.error('Erreur lors de la synchronisation de l\'action:', error);
        failed++;
        failedActions.push(action);
      }
    }

    // Garder uniquement les actions qui ont échoué
    this.syncQueue = failedActions;
    await this.saveSyncQueue();

    console.log(`Synchronisation terminée: ${synced} succès, ${failed} échecs`);

    return { success: true, synced, failed };
  }

  /**
   * Exécuter une action de la file d'attente
   */
  async executeAction(action) {
    // Cette méthode sera appelée avec l'API service injecté
    // Pour l'instant, on log juste l'action
    console.log('Exécution de l\'action:', action.type, action.data);

    // Dans une implémentation réelle, on ferait :
    // switch (action.type) {
    //   case 'like_content':
    //     await apiService.likeContent(action.data.contentId);
    //     break;
    //   case 'update_profile':
    //     await apiService.updateProfile(action.data);
    //     break;
    //   // etc.
    // }
  }

  /**
   * Obtenir le statut de la file de sync
   */
  getSyncQueueStatus() {
    return {
      isOnline: this.isOnline,
      queueLength: this.syncQueue.length,
      queue: this.syncQueue,
    };
  }

  /**
   * Vider la file de sync
   */
  async clearSyncQueue() {
    this.syncQueue = [];
    await this.saveSyncQueue();
  }

  /**
   * Méthodes de cache spécifiques pour les entités courantes
   */

  async cachePartners(partners) {
    return await this.set('partners', partners, 60 * 60 * 1000); // 1 heure
  }

  async getCachedPartners() {
    return await this.get('partners');
  }

  async cacheOffers(offers) {
    return await this.set('offers', offers, 30 * 60 * 1000); // 30 minutes
  }

  async getCachedOffers() {
    return await this.get('offers');
  }

  async cacheContent(content) {
    return await this.set('content', content, 15 * 60 * 1000); // 15 minutes
  }

  async getCachedContent() {
    return await this.get('content');
  }

  async cacheMyCodes(codes) {
    return await this.set('my_codes', codes, 5 * 60 * 1000); // 5 minutes
  }

  async getCachedMyCodes() {
    return await this.get('my_codes');
  }

  async cacheMatches(matches) {
    return await this.set('matches', matches, 60 * 60 * 1000); // 1 heure
  }

  async getCachedMatches() {
    return await this.get('matches');
  }

  async cachePlayers(players) {
    return await this.set('players', players, 24 * 60 * 60 * 1000); // 24 heures
  }

  async getCachedPlayers() {
    return await this.get('players');
  }
}

export default new CacheService();
