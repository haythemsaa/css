# CSS Platform - Application Mobile

<div align="center">

**Application mobile React Native pour le Club Sportif Sfaxien**

[![React Native](https://img.shields.io/badge/React_Native-0.81-61DAFB?logo=react)](https://reactnative.dev)
[![Expo](https://img.shields.io/badge/Expo-~54.0-000020?logo=expo)](https://expo.dev)
[![React Navigation](https://img.shields.io/badge/React_Navigation-7.x-6A5ACD)](https://reactnavigation.org)

⚽ يا CSS يا نجوم السما ⚽

</div>

---

## 📱 À propos

L'application mobile CSS Platform permet aux supporters du Club Sportif Sfaxien de:
- Se connecter et gérer leur compte (Free/Premium/Socios)
- Découvrir et utiliser les réductions **CSS Privilèges** (29 partenaires)
- Consulter les actualités et contenus du club
- Accumuler et gérer leurs points de fidélité
- Accéder aux informations de l'équipe et des matchs

---

## 🎯 Fonctionnalités

### ✅ Implémentées (v1.1.0) **[NOUVEAU]**

#### Authentification
- ✓ Connexion avec email/mot de passe
- ✓ Inscription gratuite (compte Free)
- ✓ Déconnexion
- ✓ Persistance de session (AsyncStorage)

#### Écran d'accueil
- ✓ Carte de statut utilisateur (Type + Points)
- ✓ Actions rapides (navigation)
- ✓ Section CSS Privilèges avec statistiques
- ✓ Invitation à upgrade pour utilisateurs Free

#### Partenaires CSS Privilèges
- ✓ Liste des 29 partenaires
- ✓ Filtrage par catégorie (Restaurants, Shopping, Sport, Santé)
- ✓ Affichage des réductions selon le type d'utilisateur
- ✓ **Écran de détail partenaire** ✨
- ✓ **Liste des offres par partenaire** ✨
- ✓ **Validation des offres (stock, expiration)** ✨

#### Génération de codes CSS Privilèges ✨ **[NOUVEAU v1.1]**
- ✓ **Génération de codes QR/Promo/NFC** depuis les offres
- ✓ **Modal de sélection du type de code**
- ✓ **Validation Premium/Socios requise**
- ✓ **Confirmation avec détails du code généré**

#### Mes Codes ✨ **[NOUVEAU v1.1]**
- ✓ **Liste de tous les codes générés**
- ✓ **Filtrage par statut** (Actifs, Utilisés, Expirés, Tous)
- ✓ **Affichage du statut des codes**
- ✓ **Pull-to-refresh**
- ✓ **Navigation vers scanner QR**
- ✓ **Détails complets de chaque code**

#### Scanner QR Code ✨ **[NOUVEAU v1.1]**
- ✓ **Scanner de QR codes avec caméra**
- ✓ **Validation des codes CSS Privilèges en temps réel**
- ✓ **Demande de permission caméra**
- ✓ **Zone de scan avec coins animés**
- ✓ **Feedback visuel lors de la validation**

#### Actualités
- ✓ Liste du contenu (Articles, Vidéos, Galeries, Podcasts)
- ✓ Filtrage par type de contenu
- ✓ Badge Premium pour contenu exclusif
- ✓ Contrôle d'accès selon le type d'utilisateur

#### Profil
- ✓ Informations utilisateur
- ✓ Statistiques (Type de compte, Niveau de fidélité)
- ✓ Points de fidélité
- ✓ Menu de navigation
- ✓ Déconnexion

#### Navigation
- ✓ **5 onglets** (Home, Partners, Mes Codes ✨, Content, Profile)
- ✓ **Stack navigation** pour Partners et Codes
- ✓ **Bouton scanner** dans header de Mes Codes ✨

### ✅ Implémentées (v1.2.0) **[NOUVEAU]**

#### Notifications Push ✨ **[NOUVEAU v1.2]**
- ✓ **Configuration et permissions** notifications
- ✓ **Service de gestion** des notifications
- ✓ **Notifications planifiées** pour les matchs
- ✓ **Alertes nouvelles offres** CSS Privilèges
- ✓ **Rappels expiration** de codes (24h avant)
- ✓ **Notifications actualités** en temps réel
- ✓ **Badge count** et gestion des notifications

#### Géolocalisation & Carte ✨ **[NOUVEAU v1.2]**
- ✓ **Carte interactive** des partenaires (React Native Maps)
- ✓ **Marqueurs colorés** par catégorie
- ✓ **Calcul de distance** avec formule Haversine
- ✓ **Filtrage par proximité** (5 km)
- ✓ **Position utilisateur** en temps réel
- ✓ **Navigation vers partenaires** (Google Maps/Apple Maps)
- ✓ **Bouton carte** dans header Partners
- ✓ **Callouts interactifs** avec infos partenaire

#### Mode Offline ✨ **[NOUVEAU v1.2]**
- ✓ **Cache automatique** des données consultées
- ✓ **Détection de connexion** (NetInfo)
- ✓ **Synchronisation automatique** au retour en ligne
- ✓ **File d'attente** pour actions offline
- ✓ **Gestion intelligente** des expirations de cache
- ✓ **Cache par entité** (partners, offers, content, codes)

### ✅ Implémentées (v1.3.0) **[NOUVEAU]**

#### Contenu détaillé ✨ **[NOUVEAU v1.3]**
- ✓ **ContentDetailScreen complet** pour articles/vidéos/galeries/podcasts
- ✓ **Lecteur vidéo intégré** avec Expo AV (useNativeControls)
- ✓ **Galerie photos swipeable** avec react-native-image-viewing
- ✓ **Player podcast/audio** avec contrôles (play/pause, progression)
- ✓ **Système de likes actif** avec compteur en temps réel
- ✓ **Partage social** avec Expo Sharing
- ✓ **Navigation ContentStack** (ContentList → ContentDetail)

### 🚧 À venir (v1.4+)

#### Fonctionnalités avancées
- [ ] Chat support en temps réel
- [ ] Statistiques personnelles détaillées
- [ ] Commentaires sur contenu

---

## 🛠️ Technologies

| Technologie | Version | Utilisation |
|-------------|---------|-------------|
| **React Native** | 0.81 | Framework mobile |
| **Expo** | ~54.0 | Toolchain et SDK |
| **React** | 19.1 | UI library |
| **React Navigation** | 7.x | Navigation et routing |
| **Zustand** | 5.x | State management |
| **Axios** | 1.x | HTTP client |
| **AsyncStorage** | 2.x | Stockage local persistant |
| **Expo Camera** | 17.x | Accès caméra (QR scanner) |
| **Expo Location** | 19.x | Géolocalisation |
| **Expo Notifications** | 0.31 | Notifications push |
| **React Native Maps** | 1.22 | Carte interactive |
| **NetInfo** | 11.x | Détection connexion |
| **Expo AV** | ~15.0.3 | Lecteur vidéo/audio |
| **Expo Sharing** | ~13.0.2 | Partage de contenu |
| **react-native-image-viewing** | 0.2.2 | Galerie photos swipeable |

---

## 📁 Structure du projet

```
mobile/
├── src/
│   ├── components/
│   │   ├── common/              # Composants réutilisables
│   │   │   ├── Button.js        # Bouton avec variants
│   │   │   ├── Card.js          # Carte avec styles
│   │   │   └── Input.js         # Input avec validation
│   │   ├── layout/              # Composants de layout
│   │   ├── partners/            # Composants partenaires
│   │   └── content/             # Composants contenu
│   ├── screens/
│   │   ├── auth/                # Authentification
│   │   │   ├── LoginScreen.js   # Écran de connexion
│   │   │   └── RegisterScreen.js # Écran d'inscription
│   │   ├── main/
│   │   │   └── HomeScreen.js    # Écran d'accueil
│   │   ├── partners/
│   │   │   └── PartnersScreen.js # Liste partenaires
│   │   ├── content/
│   │   │   ├── ContentScreen.js     # Liste contenu
│   │   │   └── ContentDetailScreen.js # Détail contenu (v1.3)
│   │   └── profile/
│   │       └── ProfileScreen.js  # Profil utilisateur
│   ├── navigation/
│   │   └── AppNavigator.js      # Configuration navigation
│   ├── services/
│   │   └── api.js               # API client (Axios)
│   ├── stores/
│   │   └── authStore.js         # Store authentification (Zustand)
│   ├── constants/
│   │   ├── theme.js             # Thème CSS (couleurs, spacing)
│   │   └── config.js            # Configuration app
│   └── utils/                   # Fonctions utilitaires
├── assets/                      # Images, fonts, etc.
├── App.js                       # Point d'entrée
├── package.json
└── README.md                    # Ce fichier
```

---

## 🚀 Installation

### Prérequis

- **Node.js** 18+ & npm
- **Expo CLI**: `npm install -g expo-cli`
- **Expo Go** app sur votre téléphone (iOS/Android)
  - [iOS App Store](https://apps.apple.com/app/expo-go/id982107779)
  - [Google Play Store](https://play.google.com/store/apps/details?id=host.exp.exponent)

### Installation

```bash
# Naviguer vers le dossier mobile
cd css/mobile

# Installer les dépendances
npm install

# Lancer le serveur de développement
npm start
```

### Tester l'application

#### Sur un appareil physique (recommandé)

1. Installer **Expo Go** sur votre téléphone
2. Scanner le QR code affiché dans le terminal
3. L'app se chargera automatiquement

#### Sur un émulateur

**Android:**
```bash
npm run android
```

**iOS (Mac uniquement):**
```bash
npm run ios
```

#### Sur le web (preview)

```bash
npm run web
```

---

## 🔧 Configuration

### Backend API

Modifier `src/constants/config.js` pour pointer vers votre backend:

```javascript
// Développement local
export const API_BASE_URL = 'http://localhost:8000/api/v1';

// Appareil physique (même réseau WiFi)
export const API_BASE_URL = 'http://192.168.1.X:8000/api/v1';

// Production
export const API_BASE_URL = 'https://api.css.tn/api/v1';
```

⚠️ **Important**: `localhost` ne fonctionne PAS sur un appareil physique. Utilisez l'adresse IP locale de votre machine.

### Thème et couleurs

Le thème se trouve dans `src/constants/theme.js`:

```javascript
export const COLORS = {
  black: '#000000',      // Couleur principale CSS
  gold: '#D4AF37',       // Or CSS
  // ...
};
```

---

## 📱 Comptes de test

Une fois le backend démarré, vous pouvez vous connecter avec:

| Type | Email | Mot de passe | Avantages |
|------|-------|--------------|-----------|
| **Free** | free1@css.tn | password | Contenu public uniquement |
| **Premium** | premium1@css.tn | password | Contenu premium + CSS Privilèges 10-15% |
| **Socios** | admin@css.tn | password | Tous avantages + CSS Privilèges 25% |

---

## 🏗️ Build pour production

### Android (APK)

```bash
# Build Android
expo build:android

# Ou avec EAS (nouveau)
eas build --platform android
```

### iOS (IPA)

```bash
# Build iOS (nécessite compte développeur Apple)
expo build:ios

# Ou avec EAS
eas build --platform ios
```

### Publication sur Expo

```bash
# Publier sur Expo Go
expo publish
```

---

## 🐛 Dépannage

### Erreur "Network request failed"

1. Vérifier que le backend est démarré (`php artisan serve`)
2. Vérifier l'URL de l'API dans `src/constants/config.js`
3. Sur appareil physique, utiliser l'IP locale (pas `localhost`)

### "Unable to resolve module"

```bash
# Nettoyer le cache et réinstaller
rm -rf node_modules
npm install
npm start --reset-cache
```

### Problème de navigation

```bash
# Réinstaller les dépendances de navigation
npm install @react-navigation/native @react-navigation/stack @react-navigation/bottom-tabs
expo install react-native-screens react-native-safe-area-context
```

---

## 🎨 Design System

### Couleurs CSS

- **Noir (#000000)**: Couleur principale du club
- **Or (#D4AF37)**: Couleur secondaire (accents, badges)
- **Blanc (#FFFFFF)**: Texte sur fonds sombres

### Composants disponibles

#### Button
```jsx
import { Button } from '../../components/common';

<Button
  title="Se connecter"
  variant="primary"  // primary | secondary | outline | ghost | danger
  size="lg"          // sm | md | lg
  fullWidth
  onPress={() => {}}
/>
```

#### Card
```jsx
import { Card } from '../../components/common';

<Card variant="gold" padding="lg">
  <Text>Contenu de la carte</Text>
</Card>
```

#### Input
```jsx
import { Input } from '../../components/common';

<Input
  label="Email"
  value={email}
  onChangeText={setEmail}
  placeholder="votre@email.com"
  keyboardType="email-address"
  error={errors.email}
/>
```

---

## 📊 Performance

### Optimisations implémentées

- ✓ AsyncStorage pour persistence
- ✓ Lazy loading des écrans
- ✓ Optimisation des re-renders (Zustand)
- ✓ Images optimisées
- ✓ Navigation stack/tabs efficace

### Métriques cibles

- Temps de démarrage: < 3s
- Navigation: < 100ms
- Taille du bundle: < 50 MB

---

## 🔐 Sécurité

- ✓ Tokens JWT stockés de manière sécurisée (AsyncStorage)
- ✓ Validation des entrées utilisateur
- ✓ HTTPS pour les appels API (production)
- ✓ Gestion des erreurs réseau
- ✓ Déconnexion automatique sur erreur 401

---

## 🤝 Contribution

Ce projet est propriétaire du Club Sportif Sfaxien.

Pour contribuer:
1. Fork le projet
2. Créer une branche (`git checkout -b feature/AmazingFeature`)
3. Commit vos changements (`git commit -m 'Add: AmazingFeature'`)
4. Push vers la branche (`git push origin feature/AmazingFeature`)
5. Ouvrir une Pull Request

---

## 📝 Changelog

### v1.3.0 (Novembre 2025) **[NOUVEAU]**

**Fonctionnalités Contenu Détaillé:**
- ✨ **ContentDetailScreen** complet avec support multi-formats
- ✨ **Lecteur vidéo** intégré avec Expo AV et contrôles natifs
- ✨ **Player audio/podcast** avec contrôles personnalisés (play/pause, progression, timer)
- ✨ **Galerie photos swipeable** avec react-native-image-viewing
- ✨ **Système de likes** avec compteur en temps réel
- ✨ **Partage social** via Expo Sharing
- ✨ **Support 4 types de contenu**: Article, Vidéo, Galerie, Podcast
- ✨ **Affichage conditionnel** selon le type de contenu
- ✨ **Type badges** avec emojis et couleurs CSS
- ✨ **Statistiques du contenu** (vues, likes, date de publication)

**Navigation:**
- Ajout ContentStack navigator (ContentList → ContentDetail)
- Navigation depuis ContentScreen vers détail du contenu
- Header avec bouton retour personnalisé

**Technique:**
- Intégration Expo AV ~15.0.3 (Video & Audio)
- Intégration Expo Sharing ~13.0.2
- Intégration react-native-image-viewing 0.2.2
- Gestion du cycle de vie audio (cleanup dans useEffect)
- Audio status callback pour suivi de la lecture
- Configuration audio iOS (playsInSilentModeIOS)
- Modal full-screen pour galerie photos
- Progress bar personnalisée pour audio
- Formatage du temps (MM:SS)

### v1.2.0 (Novembre 2025)

**Fonctionnalités Notifications Push:**
- ✨ **Service de notifications** complet avec Expo Notifications
- ✨ **Notifications planifiées** pour matchs à venir (2h avant)
- ✨ **Alertes nouvelles offres** CSS Privilèges en temps réel
- ✨ **Rappels expiration codes** (24h avant expiration)
- ✨ **Notifications actualités** du club
- ✨ **Badge count** et gestion des notifications reçues
- ✨ **Permissions iOS/Android** gérées automatiquement

**Fonctionnalités Géolocalisation & Carte:**
- ✨ **Carte interactive** avec React Native Maps
- ✨ **29 partenaires** affichés avec marqueurs colorés par catégorie
- ✨ **Position utilisateur** en temps réel sur la carte
- ✨ **Calcul de distance** avec formule Haversine
- ✨ **Filtrage par proximité** (5 km autour de l'utilisateur)
- ✨ **Callouts personnalisés** avec détails partenaire
- ✨ **Navigation vers partenaires** (Google Maps/Apple Maps)
- ✨ **Bouton carte 🗺️** dans header de l'écran Partners
- ✨ **Zoom automatique** sur partenaires à proximité
- ✨ **Légende** des catégories avec couleurs

**Fonctionnalités Mode Offline:**
- ✨ **Cache intelligent** des données consultées
- ✨ **Détection automatique** de la connexion (NetInfo)
- ✨ **Synchronisation auto** au retour en ligne
- ✨ **File d'attente** pour actions offline
- ✨ **Cache par entité** (partners, offers, content, codes, matches, players)
- ✨ **Gestion des expirations** (5 min à 24h selon l'entité)
- ✨ **Indicateur connexion** dans l'app

**Navigation:**
- Ajout écran MapScreen dans PartnersStack
- Bouton carte dans header de PartnersList
- 3 niveaux de navigation: PartnersList → Map → PartnerDetail

**Technique:**
- Intégration Expo Notifications 0.31
- Intégration React Native Maps 1.22
- Intégration NetInfo 11.x
- Services dédiés: notificationService, cacheService, locationService
- Initialisation des services au démarrage de l'app (App.js)
- Permissions caméra, localisation et notifications

### v1.1.0 (Novembre 2025)

**Fonctionnalités CSS Privilèges:**
- ✨ **Écran de détail partenaire** avec liste complète des offres
- ✨ **Génération de codes CSS Privilèges** (QR/Promo/NFC) depuis les offres
- ✨ **Modal de sélection** du type de code avec UI intuitive
- ✨ **Validation en temps réel** (stock, expiration, Premium requis)
- ✨ **Écran "Mes Codes"** avec gestion complète des codes générés
- ✨ **Filtrage par statut** (Actifs, Utilisés, Expirés, Tous)
- ✨ **Scanner QR Code** avec caméra et validation backend
- ✨ **Zone de scan** avec coins animés et feedback visuel

**Navigation:**
- Ajout onglet "Mes Codes" dans bottom tabs (5 onglets total)
- Stack navigation pour Partners avec détail
- Stack navigation pour Codes avec scanner
- Bouton scanner dans header de Mes Codes

**Technique:**
- Intégration Expo Camera pour QR scanner
- Permissions caméra gérées
- Pull-to-refresh sur liste codes
- Modals avec animations
- Validation côté client et serveur

### v1.0.0 (Novembre 2025)

**Fonctionnalités:**
- Authentification (Login, Register, Logout)
- Écran d'accueil avec stats utilisateur
- Liste des partenaires CSS Privilèges avec filtres
- Liste du contenu CSS avec filtres par type
- Profil utilisateur avec stats et points
- Navigation bottom tabs + stack

**Technique:**
- Setup React Native + Expo
- Intégration API backend Laravel
- State management avec Zustand
- Design system CSS (noir & or)
- AsyncStorage pour persistence

---

## 📞 Support

### Contacts

- **Email support**: support@css.tn
- **Documentation**: [README principal](../README.md)
- **Backend API**: [API Documentation](../API_DOCUMENTATION.md)

### Liens utiles

- [React Native Documentation](https://reactnative.dev/docs/getting-started)
- [Expo Documentation](https://docs.expo.dev)
- [React Navigation](https://reactnavigation.org/docs/getting-started)

---

## 📜 License

Copyright © 2025 Club Sportif Sfaxien. Tous droits réservés.

---

<div align="center">

**⚽ يا CSS يا نجوم السما ⚽**

*CSS Platform Mobile v1.3.0*

</div>
