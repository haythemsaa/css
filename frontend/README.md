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
│   │   ├── partners/         # Composants partenaires Freeoui
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
- `partnersService` - Partenaires Freeoui
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

### ✅ Complété

- ✅ Home - Page d'accueil avec présentation de la plateforme
- ✅ Login - Connexion utilisateur
- ✅ Register - Inscription utilisateur

### 🚧 À venir

- 🚧 Partners - Liste et détails des partenaires Freeoui
- 🚧 Content - Articles et actualités
- 🚧 Players - Effectif de l'équipe
- 🚧 Matches - Calendrier et résultats
- 🚧 Dashboard - Tableau de bord utilisateur
- 🚧 Profile - Gestion du profil

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

## 📝 TODO

- [ ] Implémenter la page Partenaires avec filtres et géolocalisation
- [ ] Implémenter la génération de codes QR/Promo/NFC
- [ ] Créer le dashboard utilisateur
- [ ] Ajouter la gestion du profil
- [ ] Implémenter les pages de contenu (articles, vidéos)
- [ ] Ajouter la page équipe avec filtres par position
- [ ] Créer le calendrier des matchs
- [ ] Implémenter le système de notifications
- [ ] Ajouter les animations et transitions
- [ ] Optimiser les performances (lazy loading, code splitting)
- [ ] Tests unitaires avec Vitest
- [ ] Tests E2E avec Playwright

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
