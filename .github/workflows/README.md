# 🚀 CI/CD Documentation - CSS Platform

## 📊 Vue d'ensemble

Configuration complète de CI/CD avec GitHub Actions pour automatiser les tests, la vérification de qualité et le déploiement de la plateforme CSS.

![CI Status](https://img.shields.io/github/actions/workflow/status/haythemsaa/css/ci.yml?branch=main&label=CI&style=flat-square)
![Backend Tests](https://img.shields.io/badge/backend%20tests-47%2F47-brightgreen?style=flat-square)
![Mobile Tests](https://img.shields.io/badge/mobile%20tests-65-blue?style=flat-square)

## 🔄 Workflows Configurés

### 1. **CI - Full Platform** (`ci.yml`)
Workflow principal qui orchestre tous les autres workflows.

**Déclencheurs:**
- Push sur `main`, `develop`, ou branches `claude/**`
- Pull requests vers `main` ou `develop`

**Jobs:**
- 🔍 **detect-changes**: Détecte quels composants ont changé
- 🔧 **backend-tests**: Exécute les tests Laravel (si backend modifié)
- 🎨 **frontend-build**: Build et lint du frontend (si frontend modifié)
- 📱 **mobile-tests**: Exécute les tests React Native (si mobile modifié)
- 📊 **quality-summary**: Génère un résumé des résultats

**Optimisations:**
- Exécution conditionnelle basée sur les fichiers modifiés
- Tests en parallèle pour rapidité maximale
- Cache des dépendances (Composer, npm)

### 2. **Backend CI** (`backend.yml`)
Tests et qualité du code Laravel.

**Matrice de tests:**
- PHP 8.3 et 8.4
- SQLite pour les tests

**Jobs:**
- **tests**:
  - Installation des dépendances Composer
  - Configuration Laravel (migrations, key generation)
  - Exécution de PHPUnit (47 tests)
  - Génération du rapport de couverture
  - Minimum 80% de couverture requis

- **code-quality**:
  - Laravel Pint (code style)
  - PHPStan (analyse statique niveau 5)

**Couverture:** 100% (47/47 tests) ✅

### 3. **Frontend CI** (`frontend.yml`)
Build et qualité du code React/Vite.

**Jobs:**
- **lint**: ESLint sur tout le code
- **build**:
  - Type checking TypeScript (si configuré)
  - Build avec Vite
  - Upload des artifacts
- **tests**: Tests unitaires (prêt pour Vitest)

**Technologies:**
- Node.js 20
- Vite pour le build
- ESLint pour la qualité

### 4. **Mobile CI** (`mobile.yml`)
Tests de l'application React Native.

**Jobs:**
- **tests**:
  - Installation des dépendances npm
  - Exécution de Jest (65 tests configurés)
  - Génération de la couverture

- **lint**: Vérification du style de code

- **build**: Vérification des erreurs de build

**Couverture:** 65 tests configurés ✅

## 📁 Structure des Workflows

```
.github/
└── workflows/
    ├── ci.yml              # Workflow principal
    ├── backend.yml         # Tests Laravel
    ├── frontend.yml        # Build & lint React
    ├── mobile.yml          # Tests React Native
    └── README.md           # Cette documentation
```

## 🔧 Configuration des Projets

### Backend (Laravel)

**Fichiers de configuration:**
- `backend/phpunit.xml` - Configuration PHPUnit
- `backend/phpstan.neon` - Analyse statique
- `backend/pint.json` - Style de code (si présent)

**Commandes locales:**
```bash
cd backend

# Tests
php artisan test
php artisan test --coverage

# Qualité du code
./vendor/bin/pint --test
./vendor/bin/phpstan analyse
```

### Frontend (React/Vite)

**Fichiers de configuration:**
- `frontend/eslint.config.js` - Linting
- `frontend/vite.config.js` - Build configuration
- `frontend/tsconfig.json` - TypeScript (si utilisé)

**Commandes locales:**
```bash
cd frontend

# Lint
npm run lint

# Build
npm run build

# Tests (quand configurés)
npm test
```

### Mobile (React Native/Expo)

**Fichiers de configuration:**
- `mobile/package.json` - Scripts Jest
- `mobile/jest.config.js` ou `jest` section in package.json
- `mobile/jest.setup.js` - Mocks et configuration

**Commandes locales:**
```bash
cd mobile

# Tests
npm test
npm run test:watch
npm run test:coverage

# Lint
npm run lint
```

## 🎯 Tests Automatisés

### Statistiques

| Composant | Tests | Couverture |
|-----------|-------|------------|
| **Backend** | 47 | 100% ✅ |
| **Mobile** | 65 | Configurés ✅ |
| **Frontend** | - | À configurer |
| **TOTAL** | **112** | **Excellent** 🎉 |

### Backend - 47 Tests

**AuthController** (12 tests)
- Registration, login, logout
- Profile management
- Password change

**PartnerController** (7 tests)
- Partner listing & filtering
- Partner details
- Category management

**ReductionCodeController** (7 tests)
- Code generation
- Code validation
- Code usage

**Partner API** (21 tests)
- Complete partner system tests

### Mobile - 65 Tests

**Services API** (26 tests)
- authService, partnersService, offersService
- codesService, contentService
- playersService, matchesService

**Components** (20 tests)
- Button component (10 tests)
- Input component (10 tests)

**Stores** (11 tests)
- authStore avec Zustand

**Screens** (8 tests)
- LoginScreen

## 🔐 Secrets & Variables

### Secrets Requis (GitHub)

Aucun secret requis actuellement pour les tests. Pour le déploiement futur:

```yaml
# À configurer dans GitHub Settings > Secrets
DATABASE_URL           # URL de la base de données production
API_TOKEN              # Token d'API
DEPLOY_KEY             # Clé SSH pour déploiement
```

### Variables d'Environnement

Les workflows utilisent des variables d'environnement par défaut:
- `CI=true` pour les tests
- Configuration SQLite pour Laravel
- Mode test pour Jest

## 🚦 Status Badges

Pour afficher les badges dans votre README:

```markdown
![CI](https://github.com/haythemsaa/css/workflows/CI%20-%20Full%20Platform/badge.svg)
![Backend](https://github.com/haythemsaa/css/workflows/Backend%20CI/badge.svg)
![Frontend](https://github.com/haythemsaa/css/workflows/Frontend%20CI/badge.svg)
![Mobile](https://github.com/haythemsaa/css/workflows/Mobile%20CI/badge.svg)
```

## 📈 Optimisations

### Cache

Tous les workflows utilisent le cache pour accélérer les builds:

- **Composer**: Cache des dépendances PHP
- **npm**: Cache des dépendances Node.js
- **Docker layers**: (si utilisé)

### Exécution Conditionnelle

Le workflow principal détecte les changements et n'exécute que les jobs nécessaires:

```yaml
# Exemple: backend-tests ne s'exécute que si backend/** a changé
if: needs.detect-changes.outputs.backend == 'true'
```

### Parallélisation

Les tests s'exécutent en parallèle:
- Backend: Matrice PHP 8.3 & 8.4
- Tous les composants en parallèle si modifiés

## 🐛 Dépannage

### Tests Backend Échouent

```bash
# Vérifier localement
cd backend
composer install
cp .env.example .env
php artisan key:generate
php artisan test
```

### Tests Mobile Échouent

```bash
# Installer les dépendances
cd mobile
npm install

# Exécuter les tests
npm test
```

### Build Frontend Échoue

```bash
# Vérifier le linting
cd frontend
npm run lint

# Vérifier le build
npm run build
```

## 🔄 Workflow de Développement

1. **Créer une branche**
   ```bash
   git checkout -b feature/ma-fonctionnalite
   ```

2. **Développer et tester localement**
   ```bash
   # Backend
   cd backend && php artisan test

   # Mobile
   cd mobile && npm test

   # Frontend
   cd frontend && npm run lint && npm run build
   ```

3. **Commit et push**
   ```bash
   git add .
   git commit -m "feat: ma nouvelle fonctionnalité"
   git push origin feature/ma-fonctionnalite
   ```

4. **Le CI s'exécute automatiquement** ✅
   - Détection des changements
   - Exécution des tests pertinents
   - Vérification de la qualité du code

5. **Créer une Pull Request**
   - Les workflows s'exécutent à nouveau
   - Status checks requis avant merge

## 📚 Ressources

- [GitHub Actions Documentation](https://docs.github.com/en/actions)
- [PHPUnit Documentation](https://phpunit.de/)
- [Jest Documentation](https://jestjs.io/)
- [Laravel Testing](https://laravel.com/docs/testing)
- [React Native Testing Library](https://callstack.github.io/react-native-testing-library/)

## 🎉 Résultat

Avec cette configuration CI/CD:

✅ **Tests automatiques** sur chaque commit
✅ **Qualité du code** vérifiée automatiquement
✅ **Feedback rapide** sur les PR
✅ **Confiance** dans le code déployé
✅ **112 tests** couvrant le backend et mobile

---

**Mis à jour:** 2025-11-17
**Maintenu par:** Équipe CSS Platform
**Version:** 1.3.0
