# 🏆 CSS Club Sportif Sfaxien - Application Complète

> Plateforme digitale complète pour le Club Sportif Sfaxien incluant applications mobiles, web et système de gestion de contenu.

## 📋 Vue d'Ensemble du Projet

Ce repository contient l'ensemble du code source pour la plateforme digitale du Club Sportif Sfaxien, comprenant:

- **Backend API Laravel** - API RESTful complète
- **Application Mobile Flutter** - iOS & Android
- **Application Web React** - Progressive Web App
- **Documentation complète** - Architecture et API

## 🎯 Fonctionnalités Principales

### 👥 Gestion des Utilisateurs (4 Niveaux)
1. **Gratuit** - Accès de base avec publicités
2. **Premium** - 15 TND/mois - Contenu exclusif + Freeoui
3. **Socios** - Membres officiels - Accès VIP à vie
4. **Administrateurs** - Gestion complète du système

### 📱 Modules Principaux

#### 1. Authentification & Profils
- Multi-méthodes (Email, Téléphone, Social OAuth)
- Vérification OTP
- Gestion de profil complète
- Programme de parrainage

#### 2. Système de Contenu
- Articles, Vidéos HD/4K, Podcasts, Stories
- Streaming sécurisé (Cloudflare Stream)
- Catégories et tags
- Contenu Premium vs Gratuit
- Analytics et recommandations

#### 3. Matchs & Compétitions
- Calendrier complet
- Scores en direct
- Statistiques temps réel
- Prédictions communautaires
- Compositions d'équipe

#### 4. Base de Données Joueurs
- Profils détaillés
- Statistiques de performance
- Vidéos highlights
- Suivi blessures/suspensions

#### 5. Système Freeoui ⭐
**50+ Partenaires dans 8 Catégories:**
- 🍽️ Restaurants & Alimentation
- 🏨 Hôtels & Tourisme
- 💪 Sports & Bien-être
- 🛍️ Shopping
- 💼 Services
- 🎬 Divertissement
- 📚 Éducation
- 🏥 Santé

**Fonctionnalités:**
- Génération QR codes (expiration 15min)
- Géolocalisation des offres
- Réductions: 10-15% Premium, 20-30% Socios
- Tracking des économies
- Commission pour le club (5%)
- Validation avec vérification localisation

#### 6. Cadeaux & Gamification
- Cadeaux mensuels par niveau de fidélité
- Système de loterie
- Cartes à collectionner échangeables
- Badges de réalisations
- Points de fidélité (4 niveaux)

#### 7. Dons & Crowdfunding
- Dons libres (min 5 TND)
- Campagnes ciblées
- Paiements tunisiens (D17, Konnect, Paymee, Sadad)
- Certificats de don
- Rapports de transparence trimestriels

#### 8. Espace Socios Exclusif
- Dashboard personnel
- Événements VIP (2 invitations/saison)
- Réductions majorées
- Droit de vote aux assemblées
- Kit de bienvenue
- Cadeaux d'anniversaire

#### 9. Forum Communautaire
- Catégories thématiques
- Système de votes
- Modération
- Réputation utilisateurs

#### 10. Sondages & Votes
- Sondages créés par admins
- Choix multiples
- Résultats en temps réel
- Historique de votes

## 🗂️ Structure du Repository

```
css/
├── backend/                 # API Laravel
│   ├── app/
│   │   ├── Http/
│   │   │   └── Controllers/
│   │   ├── Models/
│   │   └── Services/
│   ├── database/
│   │   └── migrations/     # 40+ tables
│   ├── routes/
│   │   └── api.php         # 60+ endpoints
│   └── config/
│
├── mobile/                 # App Flutter
│   ├── lib/
│   │   ├── screens/
│   │   ├── widgets/
│   │   ├── models/
│   │   ├── services/
│   │   └── config/
│   └── pubspec.yaml
│
├── web/                    # App React
│   ├── src/
│   │   ├── components/
│   │   ├── pages/
│   │   ├── services/
│   │   └── store/
│   ├── package.json
│   └── vite.config.ts
│
├── docs/                   # Documentation
│   ├── api/
│   ├── database/
│   └── architecture/
│
└── infrastructure/         # DevOps
    ├── docker/
    └── kubernetes/
```

## 🛠️ Stack Technique

### Backend
- **Framework:** Laravel 11 (PHP 8.2+)
- **Base de données:** MySQL 8.0+ / PostgreSQL
- **Cache & Queue:** Redis
- **Authentification:** Laravel Sanctum
- **Stockage:** AWS S3 / Cloudflare R2
- **Streaming:** Cloudflare Stream
- **Recherche:** Meilisearch
- **Emails:** SMTP / Mailgun
- **SMS:** API Tunisie

### Mobile
- **Framework:** Flutter 3.0+
- **State Management:** Riverpod
- **API Client:** Dio
- **Storage:** Hive + Secure Storage
- **Push:** Firebase Cloud Messaging
- **Paiements:** Stripe + D17/Konnect/Paymee
- **Maps:** Google Maps

### Web
- **Framework:** React 18 + TypeScript
- **Build Tool:** Vite
- **Styling:** Tailwind CSS
- **State:** Zustand
- **Data Fetching:** React Query
- **Routing:** React Router
- **Forms:** React Hook Form + Zod

## 📊 Base de Données

### 40+ Tables Créées

**Authentification & Utilisateurs (4 tables)**
- users, password_reset_tokens, sessions, personal_access_tokens

**Abonnements (2 tables)**
- subscriptions, subscription_items

**Contenu (4 tables)**
- contents, content_categories, content_tags, content_tag (pivot)

**Sports (3 tables)**
- teams, players, matches

**Dons (2 tables)**
- campaigns, donations

**Partenaires Freeoui (7 tables)**
- partner_categories, partners, partner_offers, reduction_codes, reduction_usages, partner_reviews, partner_favorites

**Cadeaux & Gamification (9 tables)**
- gift_campaigns, gift_distributions, lottery_draws, lottery_tickets, collectible_cards, user_cards, card_trades, achievement_badges, user_badges

**Communauté (4 tables)**
- forum_categories, forum_topics, forum_replies, polls, poll_votes

**Socios (2 tables)**
- socios_benefits, socios_benefit_redemptions

**Système (4 tables)**
- referral_program, notifications, push_notification_tokens, activity_logs

## 🔌 API REST - 60+ Endpoints

### Authentification (7)
- POST /auth/register
- POST /auth/login
- POST /auth/logout
- POST /auth/verify-otp
- POST /auth/social-login/{provider}
- GET /user/profile
- PUT /user/profile

### Contenu (6)
- GET /contents
- GET /contents/{slug}
- POST /contents/{id}/like
- GET /videos/{id}/stream
- ... et plus

### Matchs (6)
- GET /matches
- GET /matches/{id}/live
- POST /matches/{id}/prediction
- ... et plus

### Freeoui (13)
- GET /partners
- POST /reductions/generate
- POST /reductions/{code}/validate
- ... et plus

### Cadeaux & Loterie (9)
- GET /gifts/available
- POST /lottery/{id}/buy-ticket
- ... et plus

[Voir documentation complète dans `/docs/api/`]

## 📈 Projections Financières

### Revenus Année 3: **7.18M TND**

**Sources de revenus:**
1. Abonnements Premium (15 TND/mois)
2. Commissions Freeoui (5% sur transactions)
3. Dons & Crowdfunding
4. E-commerce (merchandising)
5. Publicités (niveau gratuit)
6. Loteries

**Freeoui devient la source #1 dès l'année 2!**

## 🚀 Installation & Démarrage

### 1. Backend Laravel

```bash
cd backend

# Installation
composer install
cp .env.example .env
php artisan key:generate

# Base de données
php artisan migrate
php artisan db:seed

# Lancer le serveur
php artisan serve
```

### 2. Application Mobile

```bash
cd mobile

# Installation
flutter pub get

# Lancer
flutter run
```

### 3. Application Web

```bash
cd web

# Installation
npm install

# Lancer
npm run dev
```

## 📝 Configuration

### Variables d'Environnement Backend

```env
APP_URL=http://localhost:8000
DB_CONNECTION=mysql
DB_DATABASE=css_database
REDIS_HOST=127.0.0.1

# Paiements Tunisie
D17_API_KEY=your_key
KONNECT_API_KEY=your_key
PAYMEE_API_KEY=your_key

# Cloudflare Stream
CLOUDFLARE_STREAM_API_TOKEN=your_token

# Firebase
FCM_SERVER_KEY=your_key
```

### Configuration Mobile

```dart
// lib/config/env.dart
class Env {
  static const String apiBaseUrl = 'http://localhost:8000/api/v1';
  static const String stripeKey = 'your_key';
}
```

### Configuration Web

```env
VITE_API_URL=http://localhost:8000/api/v1
VITE_STRIPE_PUBLIC_KEY=your_key
```

## 🧪 Tests

```bash
# Backend
cd backend
php artisan test

# Mobile
cd mobile
flutter test

# Web
cd web
npm run test
```

## 🚀 Déploiement

### Backend
```bash
# Optimiser
composer install --optimize-autoloader --no-dev
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Configurer serveur (Nginx + PHP-FPM)
# Configurer workers (Supervisor pour queues)
# Configurer cron pour scheduler
```

### Mobile
```bash
# Android
flutter build appbundle --release

# iOS
flutter build ios --release
```

### Web
```bash
npm run build
# Déployer dist/ sur Vercel/Netlify/CDN
```

## 📄 Documentation

- [Architecture](docs/architecture/)
- [API Reference](docs/api/)
- [Database Schema](docs/database/)
- [Deployment Guide](docs/deployment/)

## 🔒 Sécurité

- HTTPS obligatoire
- Rate limiting (60 req/min)
- CORS configuré
- SQL injection prevention
- XSS protection
- CSRF tokens
- Input validation
- JWT tokens avec expiration
- Stockage sécurisé des passwords (bcrypt)

## 📊 Monitoring

- **Laravel Telescope** - Debugging (dev)
- **Laravel Horizon** - Queue monitoring
- **Sentry** - Error tracking
- **Firebase Analytics** - App analytics
- **Mixpanel** - User analytics

## 🤝 Contribution

1. Fork le projet
2. Créer une branche feature (`git checkout -b feature/AmazingFeature`)
3. Commit les changements (`git commit -m 'Add AmazingFeature'`)
4. Push vers la branche (`git push origin feature/AmazingFeature`)
5. Ouvrir une Pull Request

## 👥 Équipe de Développement

**Rôles nécessaires:**
- 2x Développeurs Backend (Laravel)
- 2x Développeurs Mobile (Flutter)
- 1x Développeur Frontend (React)
- 1x UI/UX Designer
- 1x QA Engineer
- 1x DevOps Engineer
- 1x Product Manager

## 📅 Timeline de Développement

**Phase 1: MVP (4-5 mois)**
- Authentification
- Contenu de base
- Matchs
- Profils joueurs
- Premium paywall

**Phase 2: Engagement (3-4 mois)**
- Dons & crowdfunding
- Espace Socios
- Forum
- Statistiques avancées

**Phase 2.5: Freeoui & Gamification (3-4 mois)**
- Système partenaires complet
- QR codes & géolocalisation
- Cadeaux & loteries
- Cartes à collectionner

**Phase 3: Monétisation (2-3 mois)**
- E-commerce
- Optimisations
- A/B testing

**Phase 4: Innovation (3-4 mois)**
- Musée virtuel 3D
- RA (Réalité Augmentée)
- Chatbot IA
- Recommandations IA

**Total: 18-20 mois**

## 📞 Support

- Email technique: dev@css-sfax.tn
- Documentation: https://docs.css-app.tn
- Issues: GitHub Issues

## 📄 License

Propriétaire - Club Sportif Sfaxien © 2024

---

**Développé avec ❤️ pour le CSS et ses supporters**
