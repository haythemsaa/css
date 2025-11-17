# CSS Platform - Club Sportif Sfaxien

<div align="center">

![CSS Logo](https://via.placeholder.com/150x150?text=CSS)

**Plateforme digitale complète pour le Club Sportif Sfaxien**

[![Laravel](https://img.shields.io/badge/Laravel-12-FF2D20?logo=laravel)](https://laravel.com)
[![React](https://img.shields.io/badge/React-19-61DAFB?logo=react)](https://react.dev)
[![Filament](https://img.shields.io/badge/Filament-v4-F59E0B?logo=php)](https://filamentphp.com)
[![PHP](https://img.shields.io/badge/PHP-8.4-777BB4?logo=php)](https://php.net)
[![Tailwind](https://img.shields.io/badge/Tailwind-v4-38B2AC?logo=tailwind-css)](https://tailwindcss.com)
[![License](https://img.shields.io/badge/License-Proprietary-red)](LICENSE)

🎉 **Projet Complet à 100%** - Backend + Frontend + Documentation

</div>

---

## 📋 Table des matières

- [Vue d'ensemble](#-vue-densemble)
- [Fonctionnalités principales](#-fonctionnalités-principales)
- [Architecture technique](#-architecture-technique)
- [Installation](#-installation)
- [Configuration](#-configuration)
- [Documentation](#-documentation)
- [Utilisation](#-utilisation)
- [Roadmap](#-roadmap)
- [Support](#-support)

---

## 🎯 Vue d'ensemble

La plateforme CSS est une solution digitale complète pour le Club Sportif Sfaxien, offrant trois niveaux d'adhésion (Free, Premium, Socios) avec un système de monétisation innovant basé sur **Freeoui** - un réseau de partenaires offrant des réductions exclusives.

### Objectifs financiers (Projections Année 3)

- **Revenus totaux**: 7.18M TND
- **Freeoui**: 3.63M TND (50.6% du CA)
- **Abonnements Premium**: 2.16M TND (30.1%)
- **Socios**: 1.08M TND (15.0%)
- **Autres sources**: 310K TND (4.3%)

### Utilisateurs cibles

- **150,000 utilisateurs Free** : Accès basique gratuit
- **18,000 utilisateurs Premium** : 15 TND/mois
- **3,000 utilisateurs Socios** : Membres officiels VIP

---

## ✨ Fonctionnalités principales

### 1. 💳 Système Freeoui (Monétisation)

Le cœur du modèle économique de la plateforme.

- **29 partenaires** dans 8 catégories (Restauration, Shopping, Santé, Sport, etc.)
- **64+ offres actives** avec réductions personnalisées
- **Génération de codes** : QR, Promo, NFC
- **Tracking temps réel** : stock, validité, utilisation
- **Réductions différenciées** :
  - Premium : 10-15% en moyenne
  - Socios : 15-25% en moyenne
- **Géolocalisation** : Recherche de partenaires à proximité (formule Haversine)
- **Commission CSS** : Pourcentage sur chaque transaction

**Exemple d'utilisation** :
```
1. Utilisateur Premium parcourt les partenaires à Sfax
2. Sélectionne "Restaurant Le Corail" (-15%)
3. Génère un QR code pour "Menu du jour -20%"
4. Présente le QR au restaurant
5. Bénéficie de 20% de réduction
6. Gagne 10% du montant en points de fidélité
```

### 2. 👥 Gestion des Utilisateurs

- **3 types d'utilisateurs** :
  - **Free** : Accès basique (contenu public, navigation partenaires)
  - **Premium** : 15 TND/mois (contenu premium, génération codes Freeoui)
  - **Socios** : Membres officiels (réductions maximales, accès VIP)

- **Programme de Fidélité** :
  - 4 niveaux : Bronze → Silver → Gold → Platinum
  - Points gagnés sur chaque achat (10%)
  - Avantages progressifs

- **Vérification Socios** :
  - Upload documents justificatifs
  - Validation manuelle par admin
  - Attribution numéro unique (CSS-XXXXXX)

### 3. 📰 Gestion de Contenu

- **Articles** : Actualités du club
- **Vidéos** : Highlights, interviews, résumés matchs (SD/HD/4K)
- **Galeries** : Photos matchs, événements
- **Podcasts** : Émissions, analyses

**Contrôle d'accès** :
- Contenu public : Accessible à tous
- Contenu premium : Réservé Premium/Socios

**Engagement** :
- Compteur de vues
- Système de likes (authentification requise)
- Partage social

### 4. ⚽ Informations Club

**Effectif (23 joueurs)** :
- Fiche complète : photo, stats, contrat
- Filtrage par position
- Statistiques en temps réel

**Calendrier & Résultats** :
- Matchs à venir / passés
- Scores en direct
- 5 compétitions : Ligue 1, Coupe, Champions League CAF, etc.
- Détails match : adversaire, stade, affluence

### 5. 🎁 Gamification

- **Cartes à collectionner** : 653 cartes distribuées
- **Badges d'accomplissement**
- **Tombola** pour membres Socios
- **Cadeaux exclusifs**

---

## 🏗️ Architecture technique

### Stack Full-Stack

```
┌─────────────────────────────────────┐
│       React 19 Frontend (Vite)      │
│  ┌─────────────────────────────┐   │
│  │  12 Pages (Public/Private)  │   │
│  │  Zustand State Management   │   │
│  │  Tailwind CSS v4 Design     │   │
│  │  React Router DOM           │   │
│  └─────────────────────────────┘   │
└──────────────┬──────────────────────┘
               │ REST API (Axios)
┌──────────────▼──────────────────────┐
│         Laravel 12 Backend          │
│  ┌─────────────────────────────┐   │
│  │   Laravel Sanctum (Auth)    │   │
│  ├─────────────────────────────┤   │
│  │   6 API Controllers         │   │
│  │   17 API Resources          │   │
│  │   60+ Endpoints REST        │   │
│  ├─────────────────────────────┤   │
│  │   Filament v4 Admin Panel   │   │
│  │   6 Resources CRUD          │   │
│  └─────────────────────────────┘   │
└──────────────┬──────────────────────┘
               │
┌──────────────▼──────────────────────┐
│      SQLite Database (Dev)          │
│      MySQL/PostgreSQL (Prod)        │
│                                     │
│  • 30 tables                        │
│  • 21 models Eloquent               │
│  • 60+ relations                    │
│  • Soft deletes activés             │
└─────────────────────────────────────┘
```

### Technologies clés

**Backend:**

| Composant | Technologie | Version | Rôle |
|-----------|-------------|---------|------|
| Framework | Laravel | 12.x | Backend core |
| API Auth | Sanctum | 4.x | Token-based authentication |
| Admin Panel | Filament | 4.x | Interface d'administration |
| ORM | Eloquent | - | Database abstraction |
| Permissions | Spatie Permission | - | Rôles & permissions |
| Media | Spatie Media Library | - | Gestion fichiers |
| Images | Intervention Image | - | Manipulation images |
| Queue | Laravel Horizon | - | Gestion files d'attente |
| Database | SQLite/MySQL | 8.0+ | Stockage données |
| PHP | 8.4 | - | Langage backend |

**Frontend:**

| Composant | Technologie | Version | Rôle |
|-----------|-------------|---------|------|
| Framework | React | 19.x | UI library |
| Build Tool | Vite | 6.x | Fast build & HMR |
| CSS Framework | Tailwind CSS | 4.0 | Utility-first CSS |
| Router | React Router DOM | 7.x | Client-side routing |
| State | Zustand | 5.x | State management |
| HTTP Client | Axios | 1.x | API requests |
| Icons | Lucide React | - | Icon library |

### Structure du projet

```
css/
├── backend/                    # Application Laravel
│   ├── app/
│   │   ├── Http/
│   │   │   ├── Controllers/Api/      # 6 controllers API
│   │   │   ├── Resources/            # 17 API resources
│   │   │   └── Requests/             # Form requests
│   │   ├── Models/                   # 21 Eloquent models
│   │   ├── Filament/
│   │   │   └── Resources/            # 6 admin resources
│   │   └── Policies/                 # Authorization policies
│   ├── database/
│   │   ├── migrations/               # 30 migrations
│   │   └── seeders/                  # 7 seeders
│   ├── routes/
│   │   ├── api.php                   # API routes
│   │   └── web.php                   # Web routes
│   └── config/                       # Configuration files
├── frontend/                   # Application React
│   ├── src/
│   │   ├── components/
│   │   │   ├── common/               # Composants réutilisables
│   │   │   ├── layout/               # Header, Footer, MainLayout
│   │   │   ├── partners/             # PartnerCard, OfferCard
│   │   │   └── content/              # ContentCard, etc.
│   │   ├── pages/
│   │   │   ├── public/               # 8 pages publiques
│   │   │   ├── auth/                 # Login, Register
│   │   │   └── dashboard/            # Dashboard, Profile
│   │   ├── services/
│   │   │   └── api.js                # API integration layer
│   │   ├── stores/
│   │   │   └── authStore.js          # Zustand auth store
│   │   ├── App.jsx                   # Main app component
│   │   └── index.css                 # Tailwind v4 configuration
│   ├── public/                       # Static assets
│   └── README.md                     # Frontend docs
├── docs/
│   ├── API_DOCUMENTATION.md          # Documentation API complète
│   ├── FILAMENT_ADMIN.md             # Guide panel admin
│   └── cahier_charges_css_socios.md  # Specs originales
├── DEPLOYMENT.md                     # Guide de déploiement
└── README.md                         # Ce fichier
```

---

## 🚀 Installation

### Prérequis

- PHP 8.4+
- Composer 2.x
- Node.js 18+ & NPM
- SQLite (dev) ou MySQL 8+ (prod)
- Git

### Installation complète (Backend + Frontend)

#### 1. Backend (Laravel API + Admin)

**Cloner et configurer**
```bash
git clone https://github.com/haythemsaa/css.git
cd css/backend
```

**Installer les dépendances**
```bash
composer install
npm install
```

**Configuration de l'environnement**
```bash
cp .env.example .env
php artisan key:generate
```

**Configurer la base de données**

Éditer `.env` :
```env
# SQLite (Development)
DB_CONNECTION=sqlite

# MySQL (Production)
# DB_CONNECTION=mysql
# DB_HOST=127.0.0.1
# DB_PORT=3306
# DB_DATABASE=css_db
# DB_USERNAME=root
# DB_PASSWORD=

# CORS pour le frontend
FRONTEND_URL=http://localhost:5173
```

**Créer la base de données SQLite**
```bash
touch database/database.sqlite
```

**Exécuter les migrations et seeders**
```bash
php artisan migrate:fresh --seed
```

Cela va créer :
- 30 tables
- 102 utilisateurs (dont admin@css.tn)
- 29 partenaires Freeoui
- 64 offres
- 23 joueurs
- 20 matchs
- 40 contenus
- 653 cartes collectibles

**Lancer le serveur backend**
```bash
php artisan serve
# Backend accessible sur http://localhost:8000
# Admin panel sur http://localhost:8000/admin
```

**Accéder au panel admin**
```
URL: http://localhost:8000/admin
Email: admin@css.tn
Password: password
```

#### 2. Frontend (React Application)

**Ouvrir un nouveau terminal**
```bash
cd css/frontend
```

**Installer les dépendances**
```bash
npm install
```

**Configuration**

Éditer `src/services/api.js` si nécessaire pour pointer vers votre backend :
```javascript
const API_BASE_URL = 'http://localhost:8000/api/v1';
```

**Lancer le serveur de développement**
```bash
npm run dev
# Frontend accessible sur http://localhost:5173
```

**Build pour production**
```bash
npm run build
# Build créé dans le dossier dist/
```

### Quick Start (Les deux en même temps)

**Terminal 1 - Backend:**
```bash
cd css/backend && php artisan serve
```

**Terminal 2 - Frontend:**
```bash
cd css/frontend && npm run dev
```

Accès :
- **Frontend**: http://localhost:5173
- **Backend API**: http://localhost:8000/api/v1
- **Admin Panel**: http://localhost:8000/admin

---

## ⚙️ Configuration

### Sanctum (API Authentication)

```bash
php artisan vendor:publish --provider="Laravel\Sanctum\SanctumServiceProvider"
```

### Filament (Admin Panel)

Déjà configuré. Panel accessible sur `/admin`.

Pour personnaliser :
```bash
php artisan vendor:publish --tag=filament-config
```

### Spatie Permission

```bash
php artisan vendor:publish --provider="Spatie\Permission\PermissionServiceProvider"
```

### Configuration production

Avant déploiement :

```bash
# Optimiser l'application
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Générer les clés
php artisan key:generate

# Migrer la production
php artisan migrate --force

# Créer un admin
php artisan make:filament-user
```

Variables d'environnement importantes :
```env
APP_ENV=production
APP_DEBUG=false
APP_URL=https://votre-domaine.com

# Database
DB_CONNECTION=mysql
DB_DATABASE=css_production

# Queue (recommandé)
QUEUE_CONNECTION=redis

# Cache
CACHE_DRIVER=redis
SESSION_DRIVER=redis

# Mail
MAIL_MAILER=smtp
MAIL_HOST=smtp.example.com
```

---

## 📚 Documentation

### Documents disponibles

1. **[DEPLOYMENT.md](DEPLOYMENT.md)** (650+ lignes) **[NOUVEAU]**
   - Guide de déploiement complet
   - Configuration staging et production
   - Nginx, SSL/HTTPS, optimisations
   - Monitoring et troubleshooting

2. **[API_DOCUMENTATION.md](API_DOCUMENTATION.md)** (200+ lignes)
   - Tous les endpoints REST
   - Exemples de requêtes/réponses
   - Codes d'erreur
   - Authentication flows

3. **[FILAMENT_ADMIN.md](FILAMENT_ADMIN.md)** (200+ lignes)
   - Guide d'utilisation panel admin
   - Gestion des partenaires
   - Configuration des offres
   - Permissions et sécurité

4. **[frontend/README.md](frontend/README.md)** **[NOUVEAU]**
   - Documentation frontend React
   - Architecture des composants
   - Pages et fonctionnalités
   - Build et déploiement

5. **[PROJECT_README.md](PROJECT_README.md)**
   - Documentation technique complète
   - Architecture et modèles
   - Setup et seeders

### Endpoints principaux

```
POST   /api/v1/auth/register          # Inscription
POST   /api/v1/auth/login              # Connexion
GET    /api/v1/partners                # Liste partenaires
POST   /api/v1/codes/generate/{slug}   # Générer code Freeoui
GET    /api/v1/content                 # Contenus
GET    /api/v1/players                 # Effectif
GET    /api/v1/matches/upcoming        # Prochains matchs
```

Voir [API_DOCUMENTATION.md](API_DOCUMENTATION.md) pour la liste complète.

---

## 💻 Utilisation

### Cas d'usage typiques

#### 1. Utilisateur Free s'inscrit

```bash
# Inscription
curl -X POST http://localhost:8000/api/v1/auth/register \
  -H "Content-Type: application/json" \
  -d '{
    "name": "Ahmed Ben Ali",
    "email": "ahmed@example.com",
    "password": "password123",
    "password_confirmation": "password123"
  }'

# Réponse: token + profil user_type=free
```

#### 2. Utilisateur Premium génère un code Freeoui

```bash
# Login
curl -X POST http://localhost:8000/api/v1/auth/login \
  -H "Content-Type: application/json" \
  -d '{"email":"premium@css.tn","password":"password"}'

# Parcourir les partenaires
curl http://localhost:8000/api/v1/partners?city=Sfax

# Générer un QR code pour une offre
curl -X POST http://localhost:8000/api/v1/codes/generate/menu-du-jour-20 \
  -H "Authorization: Bearer {token}" \
  -H "Content-Type: application/json" \
  -d '{"type":"qr"}'

# Réponse: code QR-A8F3K9L2 valide jusqu'au 30/11/2025
```

#### 3. Partenaire valide un code

```bash
# Valider le code
curl -X POST http://localhost:8000/api/v1/codes/validate \
  -H "Content-Type: application/json" \
  -d '{"code":"QR-A8F3K9L2"}'

# Utiliser le code (montant: 50 TND)
curl -X POST http://localhost:8000/api/v1/codes/QR-A8F3K9L2/use \
  -H "Content-Type: application/json" \
  -d '{"amount":50.00}'

# Réponse:
# - Montant original: 50 TND
# - Réduction: 10 TND (20%)
# - Montant final: 40 TND
# - Points fidélité gagnés: 4
```

#### 4. Admin gère les partenaires

```bash
# Se connecter au panel admin
http://localhost:8000/admin
Email: admin@css.tn
Password: password

# Dans le panel:
1. Aller dans "Freeoui > Partenaires"
2. Cliquer "Nouveau partenaire"
3. Remplir le formulaire
4. Sauvegarder
```

### Données de test disponibles

**Utilisateurs** (102 au total):
```
admin@css.tn / password (Socios vérifié, 5000 pts)
premium1@css.tn / password (Premium actif)
free1@css.tn / password (Free)
```

**Partenaires** (29):
- 8 Restaurants (Le Corail, La Daurade, etc.)
- 6 Magasins Shopping
- 5 Salles de sport
- 4 Cliniques/Pharmacies
- 6 Autres (Cinéma, Voyages, etc.)

**Offres** (64):
- 20 Offres standard
- 15 Offres flash
- 10 Offres saisonnières
- 19 Offres exclusives

---

## 🗺️ Roadmap

### ✅ Phase 1 - Backend (TERMINÉ)
- [x] Database design (30 migrations)
- [x] Eloquent models (21 models, 60+ relations)
- [x] Seeders avec données réalistes
- [x] API REST complète (60+ endpoints)
- [x] Authentication Sanctum
- [x] Panel Admin Filament
- [x] Documentation complète

### ✅ Phase 2 - Frontend Web (TERMINÉ) **[NOUVEAU]**
- [x] Design système (Tailwind CSS v4)
- [x] Pages publiques (Home, Partners, Content, Players, Matches, Upgrade)
- [x] Authentification utilisateur (Login, Register)
- [x] Dashboard Premium/Socios avec stats et tabs
- [x] Génération et gestion codes Freeoui (QR/Promo/NFC)
- [x] Profil utilisateur et préférences (3 tabs)
- [x] Responsive design avec Tailwind
- [x] State management (Zustand avec persistance)
- [x] API integration complète (Axios + interceptors)
- [x] Build optimisé (376 kB bundle)

### 📱 Phase 3 - Application Mobile
- [ ] React Native setup
- [ ] Navigation et UI/UX
- [ ] Intégration API
- [ ] Scanner QR codes
- [ ] Notifications push
- [ ] Géolocalisation partenaires
- [ ] Mode offline

### 🧪 Phase 4 - Tests & Qualité
- [ ] Tests unitaires (Models, Controllers)
- [ ] Tests d'intégration (API)
- [ ] Tests E2E (Frontend)
- [ ] CI/CD Pipeline (GitHub Actions)
- [ ] Code coverage > 80%

### 🚀 Phase 5 - Production
- [ ] Serveur production (VPS/Cloud)
- [ ] SSL/HTTPS
- [ ] CDN pour médias
- [ ] Monitoring (Sentry, New Relic)
- [ ] Backups automatiques
- [ ] Documentation déploiement

### 📊 Phase 6 - Analytics & Business
- [ ] Dashboard analytics (revenus Freeoui)
- [ ] Rapports partenaires
- [ ] KPIs et métriques
- [ ] A/B testing
- [ ] Email marketing (newsletters)
- [ ] CRM intégration

---

## 🛠️ Développement

### Créer une nouvelle ressource Filament

```bash
php artisan make:filament-resource NomModele --generate
```

### Créer un nouveau contrôleur API

```bash
php artisan make:controller Api/NomController
php artisan make:resource NomResource
```

### Lancer les tests

```bash
# Tous les tests
php artisan test

# Tests spécifiques
php artisan test --filter=AuthenticationTest

# Avec coverage
php artisan test --coverage
```

### Commandes utiles

```bash
# Refresh database avec seeds
php artisan migrate:fresh --seed

# Créer un nouveau seeder
php artisan make:seeder NomSeeder

# Créer une migration
php artisan make:migration create_nom_table

# Créer un modèle avec migration et factory
php artisan make:model Nom -mf

# Vider le cache
php artisan cache:clear
php artisan config:clear
php artisan route:clear
```

---

## 🤝 Contribution

Ce projet est propriétaire du Club Sportif Sfaxien.

Pour contribuer :
1. Fork le projet
2. Créer une branche (`git checkout -b feature/AmazingFeature`)
3. Commit vos changements (`git commit -m 'Add: AmazingFeature'`)
4. Push vers la branche (`git push origin feature/AmazingFeature`)
5. Ouvrir une Pull Request

### Standards de code

- PSR-12 pour PHP
- Laravel best practices
- Noms de variables en anglais
- Commentaires en français
- Messages de commit conventionnels

---

## 📞 Support

### Contacts

- **Email technique**: dev@css.tn
- **Email support**: support@css.tn
- **Issues GitHub**: https://github.com/haythemsaa/css/issues

### Liens utiles

- [Laravel Documentation](https://laravel.com/docs)
- [Filament Documentation](https://filamentphp.com/docs)
- [Sanctum Documentation](https://laravel.com/docs/sanctum)

---

## 📜 License

Copyright © 2025 Club Sportif Sfaxien. Tous droits réservés.

Ce projet est propriétaire et confidentiel. Toute utilisation, reproduction ou distribution non autorisée est strictement interdite.

---

## 👥 Équipe

**Club Sportif Sfaxien - Digital Team**

Développé avec ❤️ pour les supporters du CSS

---

<div align="center">

**⚽ يا CSS يا نجوم السما ⚽**

*Plateforme CSS v1.0 - Novembre 2025*

</div>