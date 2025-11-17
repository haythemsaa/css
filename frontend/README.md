# CSS Platform - Frontend

Interface web React de la plateforme CSS (Club Sportif Sfaxien).

## 🚀 Démarrage rapide

### Prérequis

- Node.js 18+ et NPM
- Backend Laravel en cours d'exécution sur `http://localhost:8000`

### Installation

```bash
# Installer les dépendances
npm install

# Copier le fichier d'environnement
cp .env.example .env

# Lancer le serveur de développement
npm run dev
```

L'application sera accessible sur `http://localhost:5173`

## 📦 Stack Technique

- **React 19** - Framework UI
- **Vite** - Build tool et dev server
- **Tailwind CSS v4** - Framework CSS avec thème personnalisé CSS
- **React Router DOM** - Navigation et routing
- **Zustand** - Gestion d'état globale
- **Axios** - Client HTTP pour les appels API

## 🎨 Design Système

### Couleurs

- **CSS Black** : `#000000` - Couleur principale du club
- **CSS Gold** : `#D4AF37` - Couleur secondaire (or)
- **CSS Gold Light** : `#F0D878`
- **CSS Gold Dark** : `#B8941F`

### Composants disponibles

- `Button` - Bouton avec 5 variantes (primary, secondary, outline, ghost, danger)
- `Card` - Carte avec variantes et hover effects
- `Input` - Champ de formulaire avec icônes et validation
- `Badge` - Badge pour statuts et tags

## 🗂️ Structure du projet

```
frontend/
├── src/
│   ├── components/
│   │   ├── common/           # Composants réutilisables
│   │   ├── layout/           # Header, Footer, MainLayout
│   │   ├── auth/             # Composants d'authentification
│   │   ├── partners/         # Composants partenaires CSS Privilèges
│   │   └── content/          # Composants contenu
│   ├── pages/
│   │   ├── public/           # Pages publiques (Home, etc.)
│   │   ├── auth/             # Login, Register
│   │   └── dashboard/        # Pages dashboard utilisateur
│   ├── services/
│   │   └── api.js            # Configuration Axios et services API
│   ├── stores/
│   │   └── authStore.js      # Store Zustand pour l'authentification
│   ├── hooks/                # Custom React hooks
│   ├── utils/                # Fonctions utilitaires
│   ├── assets/               # Images, icônes, etc.
│   ├── App.jsx               # Composant principal avec routing
│   ├── main.jsx              # Point d'entrée
│   └── index.css             # Styles globaux + thème Tailwind
├── .env                      # Variables d'environnement
├── vite.config.js            # Configuration Vite
└── package.json
```

## 🔐 Authentification

Le système d'authentification utilise Zustand pour la gestion d'état et localStorage pour la persistance.

### Services disponibles

```javascript
import { authService } from './services/api';

// Inscription
await authService.register({ name, email, password, password_confirmation });

// Connexion
await authService.login({ email, password });

// Profil
await authService.getProfile();

// Déconnexion
await authService.logout();
```

### Store d'authentification

```javascript
import useAuthStore from './stores/authStore';

const { user, isAuthenticated, login, logout, isPremium, isSocios } = useAuthStore();
```

## 🌐 Services API

Tous les services API sont disponibles dans `src/services/api.js` :

- `authService` - Authentification et profil
- `partnersService` - Partenaires CSS Privilèges
- `offersService` - Offres partenaires
- `codesService` - Codes de réduction (QR/Promo/NFC)
- `contentService` - Articles, vidéos, galeries
- `playersService` - Joueurs de l'équipe
- `matchesService` - Matchs et calendrier

### Exemple d'utilisation

```javascript
import { partnersService } from './services/api';

// Liste des partenaires
const partners = await partnersService.getPartners({ city: 'Sfax' });

// Partenaires à proximité
const nearby = await partnersService.getNearby(latitude, longitude, 10);
```

## 🎯 Pages implémentées

### ✅ Pages Publiques (100%)

- ✅ **Home** (`/`) - Page d'accueil avec présentation de la plateforme, tarifs, et CTA
- ✅ **Login** (`/login`) - Connexion utilisateur avec comptes de test
- ✅ **Register** (`/register`) - Inscription utilisateur avec validation
- ✅ **Partners** (`/partners`) - Liste des 29 partenaires CSS Privilèges avec filtres avancés
- ✅ **PartnerDetail** (`/partners/:slug`) - Détails d'un partenaire avec ses offres
- ✅ **Content** (`/content`) - Liste des contenus (articles, vidéos, galeries, podcasts)
- ✅ **ContentDetail** (`/content/:slug`) - Détails d'un contenu avec lecteur vidéo/galerie
- ✅ **Players** (`/players`) - Effectif de l'équipe CSS avec filtres par position
- ✅ **Matches** (`/matches`) - Calendrier et résultats des matchs
- ✅ **Upgrade** (`/upgrade`) - Présentation des offres Premium et Socios

### ✅ Pages Protégées (100%)

- ✅ **Dashboard** (`/dashboard`) - Tableau de bord utilisateur (stats, codes, actions rapides)
- ✅ **Profile** (`/profile`) - Gestion du profil utilisateur avec 3 onglets (info, sécurité, préférences)

### 🎯 Fonctionnalités Clés

#### Système CSS Privilèges
- Génération de codes QR/Promo/NFC
- Géolocalisation des partenaires à proximité
- Filtres par catégorie, ville, featured
- Validation des stocks et dates d'expiration
- Calcul automatique des réductions selon user_type

#### Gestion de Contenu
- Types multiples : Articles, Vidéos, Galeries, Podcasts
- Contrôle d'accès Premium
- Système de likes
- Lecteur vidéo intégré
- Galeries d'images

#### Équipe & Matchs
- Filtres par position (Gardiens, Défenseurs, Milieux, Attaquants)
- Stats des joueurs (matchs, buts, assists, cartons)
- Calendrier avec onglets (Prochains matchs / Résultats)
- Badges de compétition
- Détection domicile/extérieur

#### Dashboard Utilisateur
- Vue d'ensemble avec 4 cartes de stats
- Liste des codes actifs avec détails
- Gestion du profil (3 onglets)
- Actions rapides vers toutes les sections

## 🛠️ Scripts disponibles

```bash
# Développement avec hot reload
npm run dev

# Build de production
npm run build

# Prévisualiser le build
npm run preview

# Linting
npm run lint
```

## 🔧 Configuration

### Variables d'environnement

Créer un fichier `.env` à la racine avec :

```env
VITE_API_URL=http://localhost:8000/api/v1
VITE_APP_NAME=CSS Platform
VITE_APP_ENV=development
```

### Connexion au backend

Le frontend se connecte automatiquement au backend Laravel via l'URL définie dans `VITE_API_URL`.

Assurez-vous que :
1. Le backend est en cours d'exécution (`php artisan serve`)
2. CORS est configuré dans `backend/config/cors.php`
3. L'URL du backend correspond à `VITE_API_URL`

## 🧪 Comptes de test

```
admin@css.tn / password (Socios)
premium1@css.tn / password (Premium)
free1@css.tn / password (Free)
```

## 📝 Statut du projet

### ✅ Fonctionnalités complétées

- [x] Page Partenaires avec filtres avancés et géolocalisation
- [x] Génération de codes QR/Promo/NFC
- [x] Dashboard utilisateur complet
- [x] Gestion complète du profil (3 onglets)
- [x] Pages de contenu (articles, vidéos, galeries, podcasts)
- [x] Page équipe avec filtres par position
- [x] Calendrier des matchs (prochains + résultats)
- [x] Page upgrade/pricing
- [x] Système de likes pour le contenu
- [x] Contrôle d'accès Premium
- [x] Responsive design complet

### 🚧 Améliorations futures

- [ ] Intégration passerelle de paiement (Premium/Socios)
- [ ] Système de notifications push
- [ ] Animations et micro-interactions avancées
- [ ] Optimisations performances (lazy loading, code splitting, PWA)
- [ ] Tests unitaires avec Vitest
- [ ] Tests E2E avec Playwright
- [ ] Mode sombre
- [ ] Support multilingue complet (FR/AR/EN)
- [ ] Partage social (Facebook, Twitter, WhatsApp)
- [ ] Téléchargement de contenus offline

## 🤝 Contribution

Ce projet fait partie de la plateforme CSS. Pour contribuer :

1. Créer une branche depuis `develop`
2. Faire les modifications
3. Tester localement
4. Créer une Pull Request

## 📄 License

Copyright © 2025 Club Sportif Sfaxien. Tous droits réservés.

---

**⚽ يا CSS يا نجوم السما ⚽**
