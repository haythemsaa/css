# CSS Club Sportif Sfaxien - Application Mobile

Application mobile officielle du Club Sportif Sfaxien développée avec Flutter.

## 🚀 Fonctionnalités

- **Authentification multi-méthodes** (Email, Téléphone, Social OAuth)
- **Contenu exclusif** (Articles, Vidéos, Podcasts, Stories)
- **Matchs en direct** avec statistiques temps réel
- **Système Freeoui** - Réductions partenaires avec QR codes
- **Cadeaux & Loteries** - Programme de récompenses gamifié
- **Cartes à collectionner** - Système de trading
- **Forum communautaire**
- **Dons & Crowdfunding**
- **Espace Socios** exclusif
- **Notifications push**

## 📱 Prérequis

- Flutter SDK 3.0+
- Dart 3.0+
- Android Studio / VS Code
- Xcode (pour iOS)

## 🔧 Installation

1. **Cloner le repository**
```bash
git clone <repo-url>
cd mobile
```

2. **Installer les dépendances**
```bash
flutter pub get
```

3. **Configuration**

Créer le fichier `lib/config/env.dart`:
```dart
class Env {
  static const String apiBaseUrl = 'YOUR_API_URL';
  static const String googleMapsApiKey = 'YOUR_GOOGLE_MAPS_KEY';
  static const String stripePublishableKey = 'YOUR_STRIPE_KEY';
}
```

4. **Lancer l'application**
```bash
# Android
flutter run

# iOS
flutter run -d ios

# Web
flutter run -d chrome
```

## 📂 Structure du Projet

```
lib/
├── config/              # Configuration de l'app
│   ├── app_config.dart
│   ├── theme.dart
│   └── routes.dart
├── models/              # Modèles de données
│   ├── user.dart
│   ├── content.dart
│   ├── partner.dart
│   └── ...
├── providers/           # State management (Riverpod)
│   ├── auth_provider.dart
│   ├── content_provider.dart
│   └── ...
├── screens/             # Écrans de l'application
│   ├── home/
│   ├── auth/
│   ├── content/
│   ├── freeoui/
│   ├── socios/
│   └── ...
├── widgets/             # Widgets réutilisables
│   ├── buttons/
│   ├── cards/
│   └── ...
├── services/            # Services API et logique métier
│   ├── api_service.dart
│   ├── auth_service.dart
│   ├── storage_service.dart
│   └── ...
└── utils/               # Utilitaires
    ├── constants.dart
    ├── helpers.dart
    └── validators.dart
```

## 🎨 Design System

### Couleurs CSS
- **Noir principal**: `#000000`
- **Or principal**: `#FFD700`
- **Blanc**: `#FFFFFF`
- **Rouge accent**: `#DC143C`

### Typographie
- **Principale**: Poppins
- **Arabe**: Cairo

## 🔐 Authentification

L'application utilise:
- JWT tokens via Laravel Sanctum
- Stockage sécurisé avec `flutter_secure_storage`
- OAuth (Facebook, Google)

## 📡 API

L'application communique avec l'API Laravel via Dio:
```dart
final dio = Dio(BaseOptions(
  baseUrl: AppConfig.apiBaseUrl,
  headers: {'Authorization': 'Bearer $token'},
));
```

## 🔔 Notifications Push

Firebase Cloud Messaging pour:
- Notifications matchs
- Nouveaux contenus
- Offres partenaires
- Cadeaux disponibles

## 📍 Géolocalisation

Utilisé pour:
- Partenaires à proximité
- Validation codes de réduction
- Check-in événements

## 🧪 Tests

```bash
# Tests unitaires
flutter test

# Tests d'intégration
flutter test integration_test/

# Coverage
flutter test --coverage
```

## 📦 Build

### Android
```bash
flutter build apk --release
flutter build appbundle --release
```

### iOS
```bash
flutter build ios --release
```

## 🚀 Déploiement

### Google Play Store
1. Préparer le bundle: `flutter build appbundle`
2. Uploader sur Google Play Console
3. Remplir les métadonnées
4. Soumettre pour révision

### Apple App Store
1. Build iOS: `flutter build ios --release`
2. Ouvrir Xcode et archiver
3. Uploader via App Store Connect
4. Soumettre pour révision

## 📝 Environnements

- **Development**: Configuration locale
- **Staging**: Serveur de test
- **Production**: Serveur live

## 🔧 Configuration Firebase

1. Créer projet Firebase
2. Télécharger `google-services.json` (Android)
3. Télécharger `GoogleService-Info.plist` (iOS)
4. Placer dans les dossiers respectifs

## 📄 License

Propriétaire - Club Sportif Sfaxien
