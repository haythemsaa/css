import React, { useEffect } from 'react';
import { NavigationContainer } from '@react-navigation/native';
import { createStackNavigator } from '@react-navigation/stack';
import { createBottomTabNavigator } from '@react-navigation/bottom-tabs';
import { ActivityIndicator, View, TouchableOpacity, Text } from 'react-native';

import useAuthStore from '../stores/authStore';
import { COLORS } from '../constants/theme';

// Auth Screens
import LoginScreen from '../screens/auth/LoginScreen';
import RegisterScreen from '../screens/auth/RegisterScreen';

// Main Screens
import HomeScreen from '../screens/main/HomeScreen';
import PartnersScreen from '../screens/partners/PartnersScreen';
import PartnerDetailScreen from '../screens/partners/PartnerDetailScreen';
import MapScreen from '../screens/partners/MapScreen';
import ContentScreen from '../screens/content/ContentScreen';
import ContentDetailScreen from '../screens/content/ContentDetailScreen';
import ProfileScreen from '../screens/profile/ProfileScreen';

// Codes Screens
import MyCodesScreen from '../screens/codes/MyCodesScreen';
import QRScannerScreen from '../screens/codes/QRScannerScreen';

const Stack = createStackNavigator();
const Tab = createBottomTabNavigator();

// Auth Stack Navigator
const AuthNavigator = () => {
  return (
    <Stack.Navigator
      screenOptions={{
        headerStyle: {
          backgroundColor: COLORS.black,
        },
        headerTintColor: COLORS.white,
        headerTitleStyle: {
          fontWeight: 'bold',
        },
      }}
    >
      <Stack.Screen
        name="Login"
        component={LoginScreen}
        options={{ title: 'Connexion' }}
      />
      <Stack.Screen
        name="Register"
        component={RegisterScreen}
        options={{ title: 'Inscription' }}
      />
    </Stack.Navigator>
  );
};

// Partners Stack Navigator
const PartnersStack = () => {
  return (
    <Stack.Navigator
      screenOptions={{
        headerStyle: {
          backgroundColor: COLORS.black,
        },
        headerTintColor: COLORS.white,
        headerTitleStyle: {
          fontWeight: 'bold',
        },
      }}
    >
      <Stack.Screen
        name="PartnersList"
        component={PartnersScreen}
        options={({ navigation }) => ({
          title: 'CSS Privilèges Partners',
          headerRight: () => (
            <TouchableOpacity
              onPress={() => navigation.navigate('Map')}
              style={{ marginRight: 15 }}
            >
              <Text style={{ color: COLORS.gold, fontSize: 24 }}>🗺️</Text>
            </TouchableOpacity>
          ),
        })}
      />
      <Stack.Screen
        name="Map"
        component={MapScreen}
        options={{ title: 'Carte des partenaires' }}
      />
      <Stack.Screen
        name="PartnerDetail"
        component={PartnerDetailScreen}
        options={{ title: 'Détails partenaire' }}
      />
    </Stack.Navigator>
  );
};

// Codes Stack Navigator
const CodesStack = () => {
  return (
    <Stack.Navigator
      screenOptions={{
        headerStyle: {
          backgroundColor: COLORS.black,
        },
        headerTintColor: COLORS.white,
        headerTitleStyle: {
          fontWeight: 'bold',
        },
      }}
    >
      <Stack.Screen
        name="MyCodesList"
        component={MyCodesScreen}
        options={({ navigation }) => ({
          title: 'Mes Codes',
          headerRight: () => (
            <TouchableOpacity
              onPress={() => navigation.navigate('QRScanner')}
              style={{ marginRight: 16 }}
            >
              <Text style={{ color: COLORS.gold, fontSize: 24 }}>📷</Text>
            </TouchableOpacity>
          ),
        })}
      />
      <Stack.Screen
        name="QRScanner"
        component={QRScannerScreen}
        options={{
          title: 'Scanner QR Code',
          headerShown: false,
        }}
      />
    </Stack.Navigator>
  );
};

// Content Stack Navigator
const ContentStack = () => {
  return (
    <Stack.Navigator
      screenOptions={{
        headerStyle: {
          backgroundColor: COLORS.black,
        },
        headerTintColor: COLORS.white,
        headerTitleStyle: {
          fontWeight: 'bold',
        },
      }}
    >
      <Stack.Screen
        name="ContentList"
        component={ContentScreen}
        options={{ title: 'Actualités' }}
      />
      <Stack.Screen
        name="ContentDetail"
        component={ContentDetailScreen}
        options={{ title: 'Détails' }}
      />
    </Stack.Navigator>
  );
};

// Main Tab Navigator
const MainNavigator = () => {
  return (
    <Tab.Navigator
      screenOptions={{
        tabBarActiveTintColor: COLORS.gold,
        tabBarInactiveTintColor: COLORS.gray400,
        tabBarStyle: {
          backgroundColor: COLORS.black,
          borderTopColor: COLORS.gray800,
        },
        headerShown: false,
      }}
    >
      <Tab.Screen
        name="Home"
        component={HomeScreen}
        options={{
          title: 'Accueil',
          tabBarIcon: ({ color }) => <View>🏠</View>,
          headerShown: true,
          headerStyle: {
            backgroundColor: COLORS.black,
          },
          headerTintColor: COLORS.white,
          headerTitleStyle: {
            fontWeight: 'bold',
          },
        }}
      />
      <Tab.Screen
        name="Partners"
        component={PartnersStack}
        options={{
          title: 'CSS Privilèges',
          tabBarIcon: ({ color }) => <View>🏪</View>,
        }}
      />
      <Tab.Screen
        name="MyCodes"
        component={CodesStack}
        options={{
          title: 'Mes Codes',
          tabBarIcon: ({ color }) => <View>🎫</View>,
        }}
      />
      <Tab.Screen
        name="Content"
        component={ContentStack}
        options={{
          title: 'Actualités',
          tabBarIcon: ({ color }) => <View>📰</View>,
        }}
      />
      <Tab.Screen
        name="Profile"
        component={ProfileScreen}
        options={{
          title: 'Profil',
          tabBarIcon: ({ color }) => <View>👤</View>,
          headerShown: true,
          headerStyle: {
            backgroundColor: COLORS.black,
          },
          headerTintColor: COLORS.white,
          headerTitleStyle: {
            fontWeight: 'bold',
          },
        }}
      />
    </Tab.Navigator>
  );
};

// Root App Navigator
const AppNavigator = () => {
  const { isAuthenticated, isLoading, initialize } = useAuthStore();

  useEffect(() => {
    initialize();
  }, []);

  if (isLoading) {
    return (
      <View style={{ flex: 1, justifyContent: 'center', alignItems: 'center' }}>
        <ActivityIndicator size="large" color={COLORS.gold} />
      </View>
    );
  }

  return (
    <NavigationContainer>
      {isAuthenticated ? <MainNavigator /> : <AuthNavigator />}
    </NavigationContainer>
  );
};

export default AppNavigator;
