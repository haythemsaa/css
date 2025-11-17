import React, { useEffect, useState } from 'react';
import { StatusBar } from 'expo-status-bar';
import { View, Text, ActivityIndicator, StyleSheet } from 'react-native';
import AppNavigator from './src/navigation/AppNavigator';
import notificationService from './src/services/notificationService';
import cacheService from './src/services/cacheService';

export default function App() {
  const [isReady, setIsReady] = useState(false);

  useEffect(() => {
    initializeApp();

    return () => {
      // Cleanup
      notificationService.removeNotificationListeners();
    };
  }, []);

  const initializeApp = async () => {
    try {
      console.log('Initialisation de l\'application...');

      // Initialiser le service de cache et vérifier la connexion
      await cacheService.initialize();
      console.log('Service de cache initialisé');

      // Initialiser les notifications
      const token = await notificationService.registerForPushNotifications();
      if (token) {
        console.log('Notifications activées, token:', token);
      }

      // Configurer les listeners de notifications
      notificationService.setupNotificationListeners(
        (notification) => {
          console.log('Notification reçue:', notification);
        },
        (response) => {
          console.log('Notification tappée:', response);
          // TODO: Gérer la navigation selon le type de notification
        }
      );

      setIsReady(true);
    } catch (error) {
      console.error('Erreur lors de l\'initialisation:', error);
      setIsReady(true); // Continuer même en cas d'erreur
    }
  };

  if (!isReady) {
    return (
      <View style={styles.loadingContainer}>
        <ActivityIndicator size="large" color="#D4AF37" />
        <Text style={styles.loadingText}>Initialisation...</Text>
      </View>
    );
  }

  return (
    <>
      <AppNavigator />
      <StatusBar style="light" />
    </>
  );
}

const styles = StyleSheet.create({
  loadingContainer: {
    flex: 1,
    justifyContent: 'center',
    alignItems: 'center',
    backgroundColor: '#F5F5F5',
  },
  loadingText: {
    marginTop: 10,
    fontSize: 14,
    color: '#666',
  },
});
