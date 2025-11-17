import * as Notifications from 'expo-notifications';
import * as Device from 'expo-device';
import { Platform } from 'react-native';

// Configuration du comportement des notifications
Notifications.setNotificationHandler({
  handleNotification: async () => ({
    shouldShowAlert: true,
    shouldPlaySound: true,
    shouldSetBadge: true,
  }),
});

class NotificationService {
  constructor() {
    this.expoPushToken = null;
    this.notificationListener = null;
    this.responseListener = null;
  }

  /**
   * Demander les permissions et obtenir le token push
   */
  async registerForPushNotifications() {
    let token;

    if (Device.isDevice) {
      const { status: existingStatus } = await Notifications.getPermissionsAsync();
      let finalStatus = existingStatus;

      if (existingStatus !== 'granted') {
        const { status } = await Notifications.requestPermissionsAsync();
        finalStatus = status;
      }

      if (finalStatus !== 'granted') {
        console.log('Permission de notification refusée');
        return null;
      }

      // Obtenir le token Expo Push
      token = (await Notifications.getExpoPushTokenAsync()).data;
      console.log('Expo Push Token:', token);
    } else {
      console.log('Les notifications ne fonctionnent pas sur simulateur');
    }

    // Configuration Android
    if (Platform.OS === 'android') {
      Notifications.setNotificationChannelAsync('default', {
        name: 'default',
        importance: Notifications.AndroidImportance.MAX,
        vibrationPattern: [0, 250, 250, 250],
        lightColor: '#D4AF37', // Or CSS
      });
    }

    this.expoPushToken = token;
    return token;
  }

  /**
   * Écouter les notifications reçues
   */
  setupNotificationListeners(onNotificationReceived, onNotificationResponse) {
    // Écouter quand une notification est reçue pendant que l'app est au premier plan
    this.notificationListener = Notifications.addNotificationReceivedListener((notification) => {
      console.log('Notification reçue:', notification);
      if (onNotificationReceived) {
        onNotificationReceived(notification);
      }
    });

    // Écouter quand l'utilisateur tape sur une notification
    this.responseListener = Notifications.addNotificationResponseReceivedListener((response) => {
      console.log('Notification tapée:', response);
      if (onNotificationResponse) {
        onNotificationResponse(response);
      }
    });
  }

  /**
   * Nettoyer les listeners
   */
  removeNotificationListeners() {
    if (this.notificationListener) {
      Notifications.removeNotificationSubscription(this.notificationListener);
    }
    if (this.responseListener) {
      Notifications.removeNotificationSubscription(this.responseListener);
    }
  }

  /**
   * Planifier une notification locale
   */
  async scheduleLocalNotification(title, body, data = {}, trigger = null) {
    try {
      const id = await Notifications.scheduleNotificationAsync({
        content: {
          title,
          body,
          data,
          sound: true,
          priority: Notifications.AndroidNotificationPriority.HIGH,
        },
        trigger: trigger || null, // null = immédiat
      });
      return id;
    } catch (error) {
      console.error('Erreur lors de la planification de notification:', error);
      return null;
    }
  }

  /**
   * Planifier une notification de rappel pour expiration de code
   */
  async scheduleCodeExpirationReminder(code, expiresAt) {
    const expirationDate = new Date(expiresAt);
    const reminderDate = new Date(expirationDate.getTime() - 24 * 60 * 60 * 1000); // 24h avant

    if (reminderDate > new Date()) {
      return await this.scheduleLocalNotification(
        'Code CSS Privilèges bientôt expiré',
        `Votre code ${code} expire demain. N'oubliez pas de l'utiliser !`,
        { type: 'code_expiration', code },
        { date: reminderDate }
      );
    }
    return null;
  }

  /**
   * Planifier une notification pour un match à venir
   */
  async scheduleMatchReminder(match) {
    const matchDate = new Date(match.match_date);
    const reminderDate = new Date(matchDate.getTime() - 2 * 60 * 60 * 1000); // 2h avant

    if (reminderDate > new Date()) {
      return await this.scheduleLocalNotification(
        `CSS vs ${match.opponent}`,
        `Le match commence dans 2 heures ! ${match.competition}`,
        { type: 'match', matchId: match.id },
        { date: reminderDate }
      );
    }
    return null;
  }

  /**
   * Envoyer une notification de nouvelle offre CSS Privilèges
   */
  async notifyNewOffer(offer, partner) {
    return await this.scheduleLocalNotification(
      `Nouvelle offre ${partner.name} !`,
      `${offer.title} - Réduction jusqu'à ${offer.reduction_socios}%`,
      { type: 'new_offer', offerId: offer.id, partnerId: partner.id }
    );
  }

  /**
   * Envoyer une notification de nouveau contenu
   */
  async notifyNewContent(content) {
    const typeEmojis = {
      article: '📰',
      video: '🎥',
      gallery: '📷',
      podcast: '🎙️',
    };

    return await this.scheduleLocalNotification(
      `${typeEmojis[content.type] || '📱'} Nouveau ${content.type}`,
      content.title,
      { type: 'new_content', contentId: content.id }
    );
  }

  /**
   * Annuler une notification planifiée
   */
  async cancelNotification(notificationId) {
    try {
      await Notifications.cancelScheduledNotificationAsync(notificationId);
      return true;
    } catch (error) {
      console.error('Erreur lors de l\'annulation de notification:', error);
      return false;
    }
  }

  /**
   * Annuler toutes les notifications
   */
  async cancelAllNotifications() {
    try {
      await Notifications.cancelAllScheduledNotificationsAsync();
      return true;
    } catch (error) {
      console.error('Erreur lors de l\'annulation de toutes les notifications:', error);
      return false;
    }
  }

  /**
   * Obtenir toutes les notifications planifiées
   */
  async getAllScheduledNotifications() {
    try {
      return await Notifications.getAllScheduledNotificationsAsync();
    } catch (error) {
      console.error('Erreur lors de la récupération des notifications:', error);
      return [];
    }
  }

  /**
   * Obtenir le badge count
   */
  async getBadgeCount() {
    try {
      return await Notifications.getBadgeCountAsync();
    } catch (error) {
      console.error('Erreur lors de la récupération du badge:', error);
      return 0;
    }
  }

  /**
   * Définir le badge count
   */
  async setBadgeCount(count) {
    try {
      await Notifications.setBadgeCountAsync(count);
      return true;
    } catch (error) {
      console.error('Erreur lors de la définition du badge:', error);
      return false;
    }
  }
}

export default new NotificationService();
