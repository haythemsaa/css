# 🚀 Quick Start Guide - CSS Platform

**Démarrez le projet CSS en moins de 5 minutes!**

---

## ⚡ Installation Express (Méthode Recommandée)

### Option 1: Script Automatique (1 commande)

```bash
./setup.sh
```

C'est tout! Le script va:
- ✅ Vérifier les prérequis (PHP, Composer, Node.js)
- ✅ Installer toutes les dépendances (Backend + Frontend + Mobile)
- ✅ Configurer les fichiers .env
- ✅ Créer la base de données SQLite
- ✅ Exécuter les migrations et seeders (102 utilisateurs, 29 partenaires, 64 offres, etc.)
- ✅ Configurer le storage

**⏱️ Temps estimé: 2-3 minutes**

---

### Option 2: Makefile (Commandes simples)

```bash
# Installation complète
make install        # Installe Backend + Frontend + Mobile

# Configuration
make setup         # Configure .env, migrations, seeders

# Démarrer le projet
make dev           # Lance Backend + Frontend en parallèle
```

**⏱️ Temps estimé: 3-4 minutes**

---

### Option 3: Docker Compose (Tout en un)

```bash
# Lancer tous les services
docker-compose up -d

# Vérifier les logs
docker-compose logs -f
```

Services inclus:
- 🐳 Backend Laravel (port 8000)
- 🐳 Frontend React (port 5173)
- 🐳 MySQL (port 3306)
- 🐳 Redis (port 6379)
- 🐳 phpMyAdmin (port 8080)
- 🐳 Redis Commander (port 8081)

**⏱️ Temps estimé: 5 minutes (premier build)**

---

## 🎯 Accès Rapide

Après l'installation, accédez au projet:

### URLs

| Service | URL | Description |
|---------|-----|-------------|
| **Frontend** | http://localhost:5173 | Application web React |
| **Backend API** | http://localhost:8000/api/v1 | API REST |
| **Admin Panel** | http://localhost:8000/admin | Panel Filament |
| **phpMyAdmin** | http://localhost:8080 | Gestion MySQL (Docker) |
| **Redis Commander** | http://localhost:8081 | Visualisation Redis (Docker) |

### Compte Admin par défaut

```
Email:    admin@css.tn
Password: password
```

### Comptes de test

```
Premium:  premium1@css.tn  / password
Free:     free1@css.tn     / password
Socios:   admin@css.tn     / password (vérifié, 5000 pts)
```

---

## 📱 Lancer l'Application Mobile

### Avec Expo (Recommandé)

```bash
cd mobile
npm start

# Ou avec Makefile
make dev-mobile
```

1. Scannez le QR code avec **Expo Go** (iOS/Android)
2. L'app se lance automatiquement

### Sur émulateur

```bash
npm run android   # Android
npm run ios       # iOS (Mac uniquement)
```

**Configuration API:** Éditez `mobile/src/constants/config.js`:

```javascript
// Pour appareil physique, remplacer par l'IP de votre machine
export const API_BASE_URL = 'http://192.168.1.X:8000/api/v1';
```

---

## 🧪 Vérifier que tout fonctionne

### 1. Tester l'API Backend

```bash
curl http://localhost:8000/api/v1/health
# Réponse: {"status":"ok","service":"CSS API"}
```

### 2. Tester l'authentification

```bash
curl -X POST http://localhost:8000/api/v1/auth/login \
  -H "Content-Type: application/json" \
  -d '{"email":"admin@css.tn","password":"password"}'
```

### 3. Exécuter les tests

```bash
make test                 # Tous les tests (Backend + Frontend + Mobile)
make test-backend         # Backend uniquement (PHPUnit)
make test-frontend        # Frontend uniquement (Vitest)
make test-mobile          # Mobile uniquement (Jest)
```

**✅ 239+ tests doivent passer!**

---

## 🛠️ Commandes Essentielles

### Développement

```bash
make dev              # Lance Backend + Frontend
make dev-all          # Lance Backend + Frontend + Mobile
make dev-mobile       # Lance uniquement Mobile

# Ou manuellement:
cd backend && php artisan serve          # Backend
cd frontend && npm run dev               # Frontend
cd mobile && npm start                   # Mobile
```

### Base de données

```bash
make migrate          # Exécuter les migrations
make migrate-fresh    # Reset + migrations + seeders
make seed             # Exécuter les seeders uniquement
```

### Tests et qualité

```bash
make test             # Tous les tests
make test-coverage    # Tests avec coverage
make lint             # Linter (Backend + Frontend)
make quality          # Lint + PHPStan + Tests
```

### Build production

```bash
make build            # Build Frontend + optimisations
make prod-build       # Build + optimisations Backend
```

### Docker

```bash
make docker-up        # Lancer Docker Compose
make docker-down      # Arrêter Docker Compose
make docker-logs      # Voir les logs
make docker-clean     # Nettoyer (containers + volumes)
```

### Utilitaires

```bash
make clean            # Nettoyer cache et fichiers temp
make deep-clean       # Nettoyer node_modules + vendor
make status           # Afficher le status du projet
make fresh-start      # Réinstaller complètement
make help             # Voir toutes les commandes
```

---

## 📊 Données de Test Disponibles

Après `make setup` ou `./setup.sh`, vous aurez:

### Utilisateurs (102 au total)

| Email | Type | Points Fidélité | Description |
|-------|------|-----------------|-------------|
| admin@css.tn | Socios | 5000 | Admin vérifié |
| premium1@css.tn | Premium | 1200 | Utilisateur Premium actif |
| free1@css.tn | Free | 0 | Utilisateur gratuit |

### Partenaires CSS Privilèges (29)

- 🍽️ **8 Restaurants** (Le Corail, La Daurade, etc.)
- 🛍️ **6 Magasins** (Monoprix, Carrefour, etc.)
- 💪 **5 Salles de sport** (Fitness Plus, etc.)
- 🏥 **4 Cliniques/Pharmacies**
- 🎬 **6 Autres** (Cinéma, Voyages, etc.)

### Offres (64)

- 20 Offres standard
- 15 Offres flash (stock limité)
- 10 Offres saisonnières
- 19 Offres exclusives Socios

### Contenu

- 40 articles, vidéos, galeries, podcasts
- 23 joueurs avec stats complètes
- 20 matchs (5 compétitions)
- 653 cartes à collectionner

---

## 🎯 Cas d'usage typiques

### 1. Générer un code CSS Privilèges (Frontend)

1. Connectez-vous avec `premium1@css.tn` / `password`
2. Allez sur **Partenaires** → Choisir un partenaire
3. Sélectionnez une offre
4. Cliquez sur **Générer un code**
5. Choisissez le type (QR / Promo / NFC)
6. Utilisez le code généré (QR-XXXXXX)

### 2. Valider un code (API)

```bash
# Valider un code
curl -X POST http://localhost:8000/api/v1/codes/validate \
  -H "Content-Type: application/json" \
  -d '{"code":"QR-A8F3K9L2"}'

# Utiliser un code (transaction 50 TND)
curl -X POST http://localhost:8000/api/v1/codes/QR-A8F3K9L2/use \
  -H "Content-Type: application/json" \
  -d '{"amount":50.00}'
```

### 3. Scanner un QR code (Mobile)

1. Lancez l'app mobile
2. Connectez-vous
3. Allez dans l'onglet **Mes Codes**
4. Appuyez sur l'icône **Scanner QR**
5. Scannez un code CSS Privilèges

### 4. Gérer les partenaires (Admin)

1. Allez sur http://localhost:8000/admin
2. Login: `admin@css.tn` / `password`
3. Cliquez sur **CSS Privilèges** → **Partenaires**
4. Créez/Modifiez/Supprimez des partenaires et offres

---

## 🐛 Troubleshooting

### Backend ne démarre pas

```bash
# Vérifier les dépendances
composer install

# Recréer .env
cp .env.example .env
php artisan key:generate

# Vérifier la base
touch database/database.sqlite
php artisan migrate:fresh --seed
```

### Frontend affiche une erreur 404 API

Vérifiez que l'API URL dans `frontend/src/services/api.js` pointe vers `http://localhost:8000/api/v1`:

```javascript
const API_BASE_URL = 'http://localhost:8000/api/v1';
```

### Mobile ne se connecte pas à l'API

Éditez `mobile/src/constants/config.js` avec l'IP de votre machine (pas localhost):

```javascript
// Trouvez votre IP avec: ifconfig (Mac/Linux) ou ipconfig (Windows)
export const API_BASE_URL = 'http://192.168.1.10:8000/api/v1';
```

### Docker échoue

```bash
# Nettoyer complètement
make docker-clean

# Rebuild
docker-compose build --no-cache

# Relancer
docker-compose up -d
```

### Erreur "Class not found"

```bash
cd backend
composer dump-autoload
php artisan clear-compiled
php artisan config:clear
```

### Tests échouent

```bash
# Backend
cd backend
composer install
php artisan test

# Frontend
cd frontend
npm install
npm run test

# Mobile
cd mobile
npm install
npm test
```

---

## 📚 Documentation Complète

Pour aller plus loin:

| Document | Description |
|----------|-------------|
| **README.md** | Vue d'ensemble complète du projet |
| **API_DOCUMENTATION.md** | Tous les endpoints REST (60+) |
| **DEPLOYMENT.md** | Guide de déploiement production |
| **FILAMENT_ADMIN.md** | Utilisation du panel admin |
| **DOCKER.md** | Documentation Docker Compose |
| **frontend/README.md** | Documentation Frontend React |
| **mobile/README.md** | Documentation Mobile React Native |
| **frontend/TESTING.md** | Tests Frontend (127 tests) |
| **mobile/TESTING.md** | Tests Mobile (65 tests) |

---

## 🆘 Support

### Problème technique?

1. Vérifiez le **Troubleshooting** ci-dessus
2. Consultez la documentation complète
3. Ouvrez une issue sur GitHub

### Commandes utiles

```bash
make status          # Vérifier le statut du projet
make help            # Voir toutes les commandes
make fresh-start     # Réinstaller complètement
```

---

## 🎉 C'est parti!

Le projet est prêt! Lancez simplement:

```bash
make dev
```

Puis ouvrez http://localhost:5173 dans votre navigateur.

**⚽ يا CSS يا نجوم السما ⚽**

---

<div align="center">

**Développé avec ❤️ pour le Club Sportif Sfaxien**

*CSS Platform v1.4.0 - Novembre 2025*

</div>
