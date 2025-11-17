# 📱 Tests Mobile - CSS Platform

## 📊 Vue d'ensemble

Suite de tests complète pour l'application mobile React Native du Club Sportif Sfaxien.

### 🎯 Couverture des tests

| Catégorie | Tests créés | Description |
|-----------|-------------|-------------|
| **Services API** | ✅ api.test.js | Tests complets pour tous les services API (auth, partners, offers, codes, content, players, matches) |
| **Composants** | ✅ Button.test.js, Input.test.js | Tests pour les composants communs réutilisables |
| **Stores** | ✅ authStore.test.js | Tests pour le store d'authentification Zustand |
| **Écrans** | ✅ LoginScreen.test.js | Tests pour l'écran de connexion |

**Total: 5 fichiers de tests couvrant les fonctionnalités critiques** 🎉

## 🛠️ Configuration

### Frameworks & Outils

- **Jest** (v29.7.0) - Framework de testing
- **React Native Testing Library** (v12.4.3) - Utilitaires de test pour React Native
- **jest-expo** (v51.0.4) - Preset Jest pour Expo
- **react-test-renderer** (v19.1.0) - Renderer pour tests React

### Fichiers de configuration

- `jest.setup.js` - Configuration globale des mocks et setup
- `package.json` - Configuration Jest et scripts de test

## 📝 Tests détaillés

### 1. Services API (`__tests__/services/api.test.js`)

Tests pour tous les services de l'API backend:

#### authService (6 tests)
- ✅ Login avec credentials
- ✅ Inscription nouvel utilisateur
- ✅ Déconnexion
- ✅ Récupération du profil
- ✅ Mise à jour du profil
- ✅ Changement de mot de passe

#### partnersService (4 tests)
- ✅ Récupération des catégories
- ✅ Liste des partenaires
- ✅ Détail d'un partenaire
- ✅ Partenaires à proximité

#### offersService (2 tests)
- ✅ Offres d'un partenaire
- ✅ Détail d'une offre

#### codesService (4 tests)
- ✅ Génération de code de réduction
- ✅ Liste des codes utilisateur
- ✅ Validation d'un code
- ✅ Utilisation d'un code

#### contentService (4 tests)
- ✅ Liste du contenu
- ✅ Détail du contenu
- ✅ Like d'un contenu
- ✅ Unlike d'un contenu

#### playersService (2 tests)
- ✅ Liste des joueurs
- ✅ Détail d'un joueur

#### matchesService (4 tests)
- ✅ Liste des matchs
- ✅ Matchs à venir
- ✅ Résultats des matchs
- ✅ Détail d'un match

**Total: 26 tests pour les services API**

### 2. Composants (`__tests__/components/`)

#### Button.test.js (10 tests)
- ✅ Rendu avec titre
- ✅ Appel de onPress au clic
- ✅ Désactivation quand disabled
- ✅ Affichage du loader en loading
- ✅ Pas d'appel onPress en loading
- ✅ Variantes (primary, secondary, outline, ghost, danger)
- ✅ Tailles (sm, md, lg)
- ✅ Style fullWidth
- ✅ Styles personnalisés

#### Input.test.js (10 tests)
- ✅ Rendu avec placeholder
- ✅ Rendu avec label
- ✅ Appel onChangeText au changement
- ✅ Affichage de la valeur actuelle
- ✅ Affichage des erreurs
- ✅ Désactivation quand disabled
- ✅ Mode secureTextEntry pour passwords
- ✅ Support multiline
- ✅ Types de clavier différents
- ✅ Styles personnalisés

**Total: 20 tests pour les composants**

### 3. Stores (`__tests__/stores/authStore.test.js`)

Tests pour le store d'authentification Zustand:

#### Initialisation (2 tests)
- ✅ Chargement des données depuis AsyncStorage
- ✅ Gestion de l'absence de données

#### Login (2 tests)
- ✅ Login réussi
- ✅ Gestion des erreurs de login

#### Register (2 tests)
- ✅ Inscription réussie
- ✅ Gestion des erreurs d'inscription

#### Logout (1 test)
- ✅ Déconnexion et nettoyage des données

#### Update Profile (1 test)
- ✅ Mise à jour du profil

#### Helper Methods (3 tests)
- ✅ Vérification utilisateur premium
- ✅ Vérification utilisateur socios
- ✅ Récupération des infos de type d'utilisateur

**Total: 11 tests pour authStore**

### 4. Écrans (`__tests__/screens/LoginScreen.test.js`)

Tests pour l'écran de connexion:

- ✅ Rendu correct du formulaire
- ✅ Validation des champs vides
- ✅ Validation email invalide
- ✅ Validation mot de passe trop court
- ✅ Appel login avec credentials valides
- ✅ Affichage alerte en cas d'échec
- ✅ Effacement des erreurs lors de la saisie
- ✅ Navigation vers l'écran d'inscription

**Total: 8 tests pour LoginScreen**

## 🎯 Résumé total

| Catégorie | Nombre de tests |
|-----------|-----------------|
| Services API | 26 |
| Composants | 20 |
| Stores | 11 |
| Écrans | 8 |
| **TOTAL** | **65 tests** ✅ |

## 🚀 Scripts de test

```bash
# Exécuter tous les tests
npm test

# Exécuter les tests en mode watch
npm run test:watch

# Générer un rapport de couverture
npm run test:coverage
```

## 🔧 Mocks configurés

Le fichier `jest.setup.js` configure automatiquement les mocks pour:

- **@react-native-async-storage/async-storage** - Stockage local
- **@react-native-community/netinfo** - Informations réseau
- **react-native-maps** - Cartes
- **expo-location** - Géolocalisation
- **expo-barcode-scanner** - Scanner de codes-barres/QR
- **expo-notifications** - Notifications
- **expo-camera** - Caméra
- **@react-navigation/native** - Navigation

## 📋 Prochaines étapes

Pour étendre la couverture de tests:

1. **Écrans additionnels**
   - RegisterScreen
   - HomeScreen
   - PartnersScreen
   - PartnerDetailScreen
   - MyCodesScreen
   - ProfileScreen

2. **Hooks personnalisés**
   - Tests pour les hooks métier custom

3. **Services additionnels**
   - cacheService
   - locationService
   - notificationService

4. **Tests E2E**
   - Tests bout en bout avec Detox ou Appium

5. **Tests de performance**
   - Benchmarks de rendu
   - Tests de charge

## 📚 Ressources

- [React Native Testing Library](https://callstack.github.io/react-native-testing-library/)
- [Jest Documentation](https://jestjs.io/)
- [Testing Expo Apps](https://docs.expo.dev/develop/unit-testing/)

---

**Note**: Cette suite de tests assure la qualité et la fiabilité de l'application mobile CSS Platform. Elle couvre les fonctionnalités critiques et peut être étendue au fur et à mesure de l'évolution de l'application.
