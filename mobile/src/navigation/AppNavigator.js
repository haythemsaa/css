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

// v1.4 - New Screens
import ChatScreen from '../screens/support/ChatScreen';
import StatsScreen from '../screens/stats/StatsScreen';

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
          title: 'CSS Privilèges',
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
        options={{ title: 'Mes Codes' }}
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

// Profile Stack Navigator (v1.4 - with Stats and Chat)
const ProfileStack = () => {
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
        name="ProfileMain"
        component={ProfileScreen}
        options={({ navigation }) => ({
          title: 'Profil',
          headerRight: () => (
            <View style={{ flexDirection: 'row', marginRight: 15 }}>
              <TouchableOpacity
                onPress={() => navigation.navigate('Stats')}
                style={{ marginRight: 15 }}
              >
                <Text style={{ color: COLORS.gold, fontSize: 24 }}>📊</Text>
              </TouchableOpacity>
              <TouchableOpacity
                onPress={() => navigation.navigate('Chat')}
              >
                <Text style={{ color: COLORS.gold, fontSize: 24 }}>💬</Text>
              </TouchableOpacity>
            </View>
          ),
        })}
      />
      <Stack.Screen
        name="Stats"
        component={StatsScreen}
        options={{ title: 'Mes Statistiques' }}
      />
      <Stack.Screen
        name="Chat"
        component={ChatScreen}
        options={{ title: 'Support CSS' }}
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
          tabBarIcon: ({ color }) => <View><Text style={{ fontSize: 24 }}>🏠</Text></View>,
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
          title: 'Partenaires',
          tabBarIcon: ({ color }) => <View><Text style={{ fontSize: 24 }}>🏪</Text></View>,
        }}
      />
      <Tab.Screen
        name="MyCodes"
        component={CodesStack}
        options={{
          title: 'Mes Codes',
          tabBarIcon: ({ color }) => <View><Text style={{ fontSize: 24 }}>🎫</Text></View>,
        }}
      />
      <Tab.Screen
        name="Content"
        component={ContentStack}
        options={{
          title: 'Actualités',
          tabBarIcon: ({ color }) => <View><Text style={{ fontSize: 24 }}>📰</Text></View>,
        }}
      />
      <Tab.Screen
        name="Profile"
        component={ProfileStack}
        options={{
          title: 'Profil',
          tabBarIcon: ({ color }) => <View><Text style={{ fontSize: 24 }}>👤</Text></View>,
        }}
      />
    </Tab.Navigator>
  );
};

// Root App Navigator
const AppNavigator = () => {
  const { isAuthenticated, isLoading } = useAuthStore();

  if (isLoading) {
    return (
      <View style={{ flex: 1, justifyContent: 'center', alignItems: 'center' }}>
        <ActivityIndicator size="large" color={COLORS.primary} />
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
