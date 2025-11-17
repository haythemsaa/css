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
- Découvrir et utiliser les réductions **Freeoui** (29 partenaires)
- Consulter les actualités et contenus du club
- Accumuler et gérer leurs points de fidélité
- Accéder aux informations de l'équipe et des matchs

---

## 🎯 Fonctionnalités

### ✅ Implémentées (v1.0.0)

#### Authentification
- ✓ Connexion avec email/mot de passe
- ✓ Inscription gratuite (compte Free)
- ✓ Déconnexion
- ✓ Persistance de session (AsyncStorage)

#### Écran d'accueil
- ✓ Carte de statut utilisateur (Type + Points)
- ✓ Actions rapides (navigation)
- ✓ Section Freeoui avec statistiques
- ✓ Invitation à upgrade pour utilisateurs Free

#### Partenaires Freeoui
- ✓ Liste des 29 partenaires
- ✓ Filtrage par catégorie (Restaurants, Shopping, Sport, Santé)
- ✓ Affichage des réductions selon le type d'utilisateur
- ✓ Compteur d'offres actives par partenaire

#### Actualités
- ✓ Liste du contenu (Articles, Vidéos, Galeries, Podcasts)
- ✓ Filtrage par type de contenu
- ✓ Badge Premium pour contenu exclusif
- ✓ Contrôle d'accès selon le type d'utilisateur

#### Profil
- ✓ Informations utilisateur
- ✓ Statistiques (Type de compte, Niveau de fidélité)
- ✓ Points de fidélité
- ✓ Menu de navigation (à compléter)
- ✓ Déconnexion

### 🚧 À venir (v1.1+)

#### Scanner QR Code
- [ ] Scanner de QR codes avec caméra
- [ ] Validation des codes Freeoui
- [ ] Historique des codes scannés

#### Génération de codes
- [ ] Génération de codes QR/Promo/NFC
- [ ] Gestion des codes actifs
- [ ] Dates d'expiration et stock

#### Géolocalisation
- [ ] Carte interactive des partenaires
- [ ] Filtrage par proximité
- [ ] Itinéraire vers les partenaires

#### Notifications Push
- [ ] Notifications pour les matchs
- [ ] Alertes pour les nouvelles offres Freeoui
- [ ] Actualités du club en temps réel

#### Contenu détaillé
- [ ] Lecteur vidéo intégré
- [ ] Galerie photos swipeable
- [ ] Player podcast/audio
- [ ] Système de likes

#### Mode hors ligne
- [ ] Cache des données consultées
- [ ] Synchronisation automatique
- [ ] Indicateur de connexion

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
│   │   │   └── ContentScreen.js  # Liste contenu
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
| **Premium** | premium1@css.tn | password | Contenu premium + Freeoui 10-15% |
| **Socios** | admin@css.tn | password | Tous avantages + Freeoui 25% |

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

### v1.0.0 (Novembre 2025)

**Fonctionnalités:**
- Authentification (Login, Register, Logout)
- Écran d'accueil avec stats utilisateur
- Liste des partenaires Freeoui avec filtres
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

*CSS Platform Mobile v1.0.0*

</div>
