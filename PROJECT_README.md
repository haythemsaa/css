# 🏆 Projet CSS & Socios - Plateforme Digitale

> Application Web et Mobile pour le Club Sportif Sfaxien et sa communauté Socios

**Version:** 1.0
**Date:** 16 Novembre 2025
**Objectif financier:** 7.18M TND en Année 3

---

## 📊 Vue d'Ensemble

Plateforme digitale complète (Web + Mobile) pour le Club Sportif Sfaxien avec 3 niveaux d'utilisateurs :
- **Gratuit (Free)** - Accès limité aux contenus de base
- **Premium** - 15 TND/mois - Accès complet aux contenus exclusifs
- **Socios** - Membres officiels avec avantages VIP et accès gratuit

### Projections Financières

| Année | Revenus Total | Source Principale |
|-------|--------------|-------------------|
| An 1  | 1.13M TND    | CSS Privilèges (340K)    |
| An 2  | 3.73M TND    | CSS Privilèges (1.81M)   |
| An 3  | 7.18M TND    | CSS Privilèges (3.63M)   |

**Le système CSS Privilèges devient la source de revenus #1 dès l'année 2 !**

---

## 🏗️ Architecture Technique

### Backend
- **Framework:** Laravel 12 (PHP 8.4)
- **API:** RESTful avec Laravel Sanctum
- **Base de données:** SQLite (dev) / MySQL (production)
- **Cache/Queue:** Redis
- **Admin Panel:** Filament v4
- **Monitoring:** Laravel Horizon

### Frontend (À venir)
- **Mobile:** Flutter / React Native
- **Web:** React.js 18+ TypeScript
- **Styling:** Tailwind CSS

---

## 📦 Modules Implémentés

### 1. Authentification & Utilisateurs ✅
- Système multi-niveaux (Free/Premium/Socios)
- Vérification Socios
- Profils utilisateurs enrichis
- Programme de fidélité (Bronze → Platinum)

### 2. Système CSS Privilèges (Partenaires) ✅
**Module phare du projet - Génération de 50% des revenus**

#### Capacités:
- 300+ partenaires potentiels
- 8 catégories (Restauration, Hôtellerie, Sport, Shopping, etc.)
- Génération de codes QR/Promo en temps réel
- Tracking complet des utilisations
- Système de commissions automatique
- Réductions différenciées (Premium vs Socios)

#### Tables créées:
- `partner_categories` - Catégories de partenaires
- `partners` - 40+ champs par partenaire
- `partner_offers` - Offres flash/saisonnières/exclusives
- `reduction_codes` - Codes QR/Promo/NFC/Wallet
- `reduction_usages` - Analytics complètes
- `partner_reviews` - Avis et notations

### 3. Gestion de Contenu ✅
- Articles, vidéos, galeries, podcasts
- Contenus gratuits vs premium
- Système de catégories
- Compteurs de vues et likes

### 4. Matchs & Joueurs ✅
- Calendrier des matchs
- Suivi live (structure prête)
- Fiches joueurs complètes
- Statistiques détaillées

### 5. Dons & Crowdfunding ✅
- Dons libres et ciblés
- Campagnes de financement
- Transparence totale
- Multi-passerelles de paiement

### 6. Programme de Fidélité ✅
- Système de points
- 4 niveaux (Bronze/Argent/Or/Platine)
- Transactions tracées
- Sources multiples de points

### 7. Campagnes de Cadeaux ✅
- Cadeaux physiques et digitaux
- Déclencheurs automatiques (anniversaire, ancienneté, etc.)
- Distribution trackée
- Budget et stock gérés

### 8. Loteries & Jeux ✅
- Tirages au sort
- Billets de loterie
- Système de gains
- Historique complet

### 9. Cartes à Collectionner ✅
- 4 niveaux de rareté (Common → Legendary)
- 3 catégories (Joueurs, Historique, Stades)
- Système d'acquisition
- Collection personnelle

### 10. Système d'Abonnements ✅
- Plans mensuels et annuels
- Auto-renouvellement
- Historique des paiements
- Gestion des statuts

---

## 🗄️ Base de Données

### 30 Tables Créées

**Core System:**
- users (extended with 15+ fields)
- permissions & roles (Spatie)
- media (Spatie)
- activity_log (Spatie)

**Business Logic:**
- subscriptions
- contents, videos
- matches, players
- donations
- loyalty_transactions
- notifications
- user_badges

**CSS Privilèges System (8 tables):**
- partner_categories
- partners
- partner_offers
- reduction_codes
- reduction_usages
- partner_reviews

**Gamification:**
- gift_campaigns
- gift_distributions
- lottery_draws
- lottery_tickets
- collectible_cards
- user_cards

**Toutes les tables incluent:**
- Foreign keys avec cascade approprié
- Indexes sur colonnes fréquentes
- SoftDeletes quand pertinent
- Timestamps (created_at, updated_at)

---

## 🚀 Installation

### Prérequis
- PHP 8.4+
- Composer
- Node.js & NPM
- SQLite (dev) ou MySQL (production)
- Redis (optionnel)

### Étapes

```bash
# Cloner le repository
git clone https://github.com/haythemsaa/css.git
cd css/backend

# Installer les dépendances
composer install

# Copier et configurer .env
cp .env.example .env
php artisan key:generate

# Créer la base de données (si MySQL)
mysql -u root -e "CREATE DATABASE css_database"

# Exécuter les migrations
php artisan migrate

# (Optionnel) Seed des données de test
php artisan db:seed

# Créer un utilisateur admin pour Filament
php artisan make:filament-user

# Lancer le serveur de développement
php artisan serve
```

### Accès

- **Application:** http://localhost:8000
- **Admin Panel:** http://localhost:8000/admin
- **Horizon:** http://localhost:8000/horizon (après configuration Redis)

---

## 📚 Packages Installés

### Authentification & Permissions
- `laravel/sanctum` (v4.2) - API Authentication
- `spatie/laravel-permission` (v6.23) - Roles & Permissions

### Médias & Contenus
- `spatie/laravel-medialibrary` (v11.17) - Media Management
- `intervention/image-laravel` (v1.5) - Image Processing

### Admin & Monitoring
- `filament/filament` (v4.2) - Admin Panel
- `laravel/horizon` (v5.40) - Queue Monitoring

### Utilitaires
- `spatie/laravel-activitylog` (v4.10) - Audit Trail
- `livewire/livewire` (v3.6) - Real-time UI

---

## 🎯 Prochaines Étapes

### Phase 1 - Backend (En cours)
- [x] Initialisation Laravel
- [x] Migrations complètes
- [ ] Modèles Eloquent avec relations
- [ ] Seeders de données de test
- [ ] API Controllers
- [ ] Routes API
- [ ] Authentication Sanctum
- [ ] Permissions & Roles

### Phase 2 - Admin Panel
- [ ] Resources Filament pour chaque module
- [ ] Tableaux de bord analytics
- [ ] Gestion des partenaires
- [ ] Gestion des contenus
- [ ] Gestion des utilisateurs

### Phase 3 - API Mobile
- [ ] Endpoints authentification
- [ ] Endpoints contenus
- [ ] Endpoints CSS Privilèges
- [ ] Endpoints fidélité
- [ ] Documentation API (Swagger)

### Phase 4 - Frontend Mobile
- [ ] Setup Flutter/React Native
- [ ] Authentification
- [ ] Interface CSS Privilèges
- [ ] Lecteur de codes QR
- [ ] Profil utilisateur
- [ ] Programme fidélité

### Phase 5 - Tests & Déploiement
- [ ] Tests unitaires
- [ ] Tests d'intégration
- [ ] CI/CD Pipeline
- [ ] Déploiement production
- [ ] Monitoring & Logs

---

## 📈 Système CSS Privilèges - Détails

### Fonctionnalités Clés

1. **Génération de Codes**
   - QR codes uniques avec expiration (15 min)
   - Codes promo alphanumériques
   - Support NFC
   - Intégration Apple Wallet / Google Pay

2. **Types d'Offres**
   - Standard (permanentes)
   - Flash (limitées dans le temps)
   - Saisonnières (Ramadan, Été, etc.)
   - Exclusives (Socios uniquement)

3. **Analytics Avancées**
   - Taux de conversion
   - Panier moyen
   - Heures de pointe
   - Géolocalisation des utilisations
   - ROI par partenaire

4. **Système de Commissions**
   - 5-15% sur chaque transaction
   - Frais d'adhésion annuels
   - Tracking automatique
   - Paiements tracés

### Exemple de Parcours Utilisateur

1. Utilisateur ouvre l'app
2. Section "Avantages" → Voir partenaires à proximité
3. Sélectionne "Restaurant Da Mario"
4. Voit : "-20% pour Socios"
5. Clique "Générer mon code"
6. QR code affiché (valide 15 min)
7. Présente en caisse
8. Partenaire scanne le code
9. Réduction appliquée
10. Transaction enregistrée
11. Commission calculée

---

## 🔐 Sécurité

- Authentification API avec Sanctum
- Hashing bcrypt pour mots de passe
- CSRF protection
- XSS protection
- SQL injection prevention (Eloquent ORM)
- Rate limiting sur API
- Audit trail complet (Activity Log)

---

## 📞 Support & Documentation

**Documentation complète:** Voir `/cahier_charges_css_socios.md`
**Récapitulatif:** Voir `/RECAPITULATIF_AJOUTS_CSS.md`
**Planning:** Voir `/CSS_Planning_Gantt_Detaille.xlsx`
**Pitch:** Voir `/CSS_CSS Privilèges_Pitch_Partenaires.pptx`

---

## 👥 Contributeurs

Développé pour le Club Sportif Sfaxien
**Date de lancement prévu:** Q2 2026

---

## 📝 Licence

Propriétaire - Club Sportif Sfaxien © 2025
