# CSS Club Sportif Sfaxien - Application Web

Application web progressive (PWA) du Club Sportif Sfaxien développée avec React, TypeScript et Tailwind CSS.

## 🚀 Technologies

- **React 18** - Framework UI
- **TypeScript** - Type safety
- **Vite** - Build tool ultra-rapide
- **Tailwind CSS** - Utility-first CSS
- **React Router** - Navigation
- **Zustand** - State management léger
- **React Query** - Data fetching et caching
- **Axios** - HTTP client
- **React Hook Form** - Forms management
- **Zod** - Validation de schémas

## 📦 Installation

```bash
# Installer les dépendances
npm install

# Lancer en développement
npm run dev

# Build pour production
npm run build

# Preview du build
npm run preview
```

## 📂 Structure du Projet

```
src/
├── components/          # Composants réutilisables
│   ├── ui/             # Composants UI de base
│   ├── layout/         # Layout components (Header, Footer, etc.)
│   └── features/       # Composants métier
├── pages/              # Pages de l'application
│   ├── HomePage.tsx
│   ├── LoginPage.tsx
│   ├── ContentPage.tsx
│   ├── FreeoiPage.tsx
│   └── ...
├── services/           # Services API
│   ├── api.ts
│   ├── auth.ts
│   ├── content.ts
│   └── ...
├── store/              # State management (Zustand)
│   ├── useAuthStore.ts
│   ├── useContentStore.ts
│   └── ...
├── hooks/              # Custom hooks
│   ├── useAuth.ts
│   ├── useContent.ts
│   └── ...
├── utils/              # Utilitaires
│   ├── constants.ts
│   ├── helpers.ts
│   └── validators.ts
├── types/              # Types TypeScript
│   ├── user.ts
│   ├── content.ts
│   └── ...
└── App.tsx            # Component racine
```

## 🎨 Design System

### Couleurs CSS
```css
--css-black: #000000;
--css-gold: #FFD700;
--css-white: #FFFFFF;
--css-red: #DC143C;
```

### Tailwind Classes
- `bg-css-black` - Fond noir CSS
- `text-css-gold` - Texte or CSS
- `border-css-gold` - Bordure or

## 🔐 Authentification

```typescript
import { useAuthStore } from '@/store/useAuthStore'

const { user, login, logout } = useAuthStore()

// Login
await login(email, password)

// Logout
logout()
```

## 📡 API Calls

```typescript
import { api } from '@/services/api'

// GET request
const contents = await api.get('/contents')

// POST request
const response = await api.post('/donations', {
  amount: 50,
  campaign_id: 1,
})
```

## 🎯 Features

### 1. Authentification
- Login/Register
- Social OAuth (Facebook, Google)
- OTP verification
- Password reset

### 2. Contenu
- Articles, vidéos, galeries
- Streaming vidéo sécurisé
- Catégories et tags
- Recherche et filtres

### 3. Matchs
- Calendrier des matchs
- Live scores et statistiques
- Prédictions communautaires
- Compositions d'équipe

### 4. Freeoui
- Liste des partenaires
- Génération de QR codes
- Géolocalisation des offres
- Historique d'utilisation

### 5. Cadeaux & Loteries
- Cadeaux disponibles
- Achats de tickets loterie
- Historique des gains
- Cartes à collectionner

### 6. Espace Socios
- Dashboard personnel
- Avantages exclusifs
- Événements VIP
- Points de fidélité

### 7. Dons
- Campagnes de crowdfunding
- Dons sécurisés
- Certificats de don
- Rapports de transparence

## 🔔 Notifications

Web Push API pour notifications en temps réel:
- Nouveaux matchs
- Nouveau contenu
- Offres partenaires
- Cadeaux disponibles

## 📱 Progressive Web App (PWA)

L'application peut être installée comme PWA:
- Mode hors ligne
- Icône sur écran d'accueil
- Notifications push
- Performances optimisées

## 🧪 Tests

```bash
# Tests unitaires
npm run test

# Tests avec coverage
npm run test:coverage
```

## 🚀 Déploiement

### Vercel
```bash
npm install -g vercel
vercel
```

### Netlify
```bash
npm install -g netlify-cli
netlify deploy --prod
```

### Build manuel
```bash
npm run build
# Les fichiers sont dans le dossier dist/
```

## 🌐 Variables d'Environnement

Créer un fichier `.env`:

```env
VITE_API_URL=http://localhost:8000/api/v1
VITE_STRIPE_PUBLIC_KEY=your_stripe_key
VITE_GOOGLE_MAPS_KEY=your_maps_key
VITE_FACEBOOK_APP_ID=your_fb_app_id
VITE_GOOGLE_CLIENT_ID=your_google_client_id
```

## 🔧 Configuration

### Vite Config
```typescript
export default defineConfig({
  server: {
    port: 3000,
    proxy: {
      '/api': 'http://localhost:8000',
    },
  },
})
```

### Tailwind Config
```javascript
module.exports = {
  theme: {
    extend: {
      colors: {
        css: {
          black: '#000000',
          gold: '#FFD700',
        },
      },
    },
  },
}
```

## 📝 Scripts npm

- `npm run dev` - Serveur de développement
- `npm run build` - Build de production
- `npm run preview` - Preview du build
- `npm run lint` - Linter le code
- `npm run test` - Lancer les tests

## 📄 License

Propriétaire - Club Sportif Sfaxien
