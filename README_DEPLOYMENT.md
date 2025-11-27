# 🚀 Guide de Déploiement CSS Socios

## 📋 Prérequis

- Docker & Docker Compose installés
- Git
- Node.js 18+ (pour développement local)
- PHP 8.2+ (pour développement local)
- Composer (pour développement local)

## 🐳 Démarrage avec Docker

### 1. Cloner le projet

```bash
git clone https://github.com/haythemsaa/css.git
cd css
```

### 2. Configuration Backend

```bash
cd backend
cp .env.example .env
```

Modifier `.env` avec vos configurations:

```env
APP_NAME="CSS Socios"
APP_ENV=local
APP_DEBUG=true
APP_URL=http://localhost

DB_CONNECTION=mysql
DB_HOST=mysql
DB_PORT=3306
DB_DATABASE=css_database
DB_USERNAME=css_user
DB_PASSWORD=css_password

REDIS_HOST=redis
REDIS_PORT=6379
```

### 3. Configuration Frontend

```bash
cd ../web
```

Créer `.env.local`:

```env
VITE_API_URL=http://localhost:8000/api/v1
```

### 4. Lancer avec Docker Compose

```bash
cd ..
docker-compose up -d
```

Cela va démarrer:
- **Backend Laravel** : http://localhost:8000
- **Frontend React** : http://localhost:3000
- **MySQL** : localhost:3306
- **Redis** : localhost:6379
- **PhpMyAdmin** : http://localhost:8080
- **Nginx** : http://localhost

### 5. Migrations et Seeds

```bash
docker-compose exec backend php artisan migrate --seed
```

### 6. Créer un utilisateur admin

```bash
docker-compose exec backend php artisan make:filament-user
```

## 🌐 URLs du Projet

| Service | URL | Description |
|---------|-----|-------------|
| Frontend | http://localhost:3000 | Application React |
| Backend API | http://localhost:8000/api/v1 | API Laravel |
| Admin Filament | http://localhost:8000/admin | Interface Admin |
| PhpMyAdmin | http://localhost:8080 | Gestion BD |
| Nginx | http://localhost | Reverse Proxy |

## 📦 Structure des Containers

```
css/
├── backend (PHP 8.2 + Laravel)
├── web (Node 18 + React + Vite)
├── mysql (MySQL 8.0)
├── redis (Redis 7)
├── nginx (Nginx Alpine)
└── phpmyadmin (PhpMyAdmin)
```

## 🔧 Commandes Utiles

### Voir les logs
```bash
docker-compose logs -f backend
docker-compose logs -f web
```

### Accéder au container
```bash
docker-compose exec backend sh
docker-compose exec web sh
```

### Arrêter les containers
```bash
docker-compose down
```

### Rebuild les containers
```bash
docker-compose up -d --build
```

### Nettoyer tout (ATTENTION: supprime les données)
```bash
docker-compose down -v
```

## 🛠️ Développement Local (Sans Docker)

### Backend

```bash
cd backend
composer install
php artisan key:generate
php artisan migrate --seed
php artisan serve
```

### Frontend

```bash
cd web
npm install
npm run dev
```

## 📱 Application Mobile Flutter

### Installation

```bash
cd mobile
flutter pub get
```

### Lancer sur émulateur

```bash
# Android
flutter run

# iOS
flutter run -d ios
```

### Build pour production

```bash
# Android
flutter build appbundle --release

# iOS
flutter build ios --release
```

## 🚀 Déploiement Production

### Backend (Laravel)

1. **Serveur Requirements:**
   - PHP 8.2+
   - MySQL 8.0+
   - Redis
   - Nginx/Apache
   - Composer

2. **Optimisations:**
```bash
composer install --optimize-autoloader --no-dev
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan optimize
```

3. **Queue Worker:**
```bash
php artisan queue:work redis --sleep=3 --tries=3
```

Utiliser **Supervisor** pour gérer les workers.

### Frontend (React)

1. **Build:**
```bash
npm run build
```

2. **Déployer sur Vercel/Netlify:**
   - Connecter le repository GitHub
   - Configurer les variables d'environnement
   - Build command: `npm run build`
   - Output directory: `dist`

### Application Mobile

1. **Android:**
   - Générer le bundle: `flutter build appbundle --release`
   - Upload sur Google Play Console

2. **iOS:**
   - Générer l'archive: `flutter build ios --release`
   - Upload sur App Store Connect via Xcode

## 🔒 Sécurité Production

- ✅ HTTPS obligatoire
- ✅ Rate limiting activé
- ✅ CORS configuré
- ✅ JWT tokens avec expiration
- ✅ Validation des entrées
- ✅ Protection CSRF
- ✅ Logs d'activité

## 📊 Monitoring

- **Laravel Telescope** (dev)
- **Laravel Horizon** (queues)
- **Sentry** (errors)
- **Firebase Analytics** (mobile)

## 💾 Backup

### Base de données

```bash
docker-compose exec mysql mysqldump -u css_user -p css_database > backup.sql
```

### Restauration

```bash
docker-compose exec -T mysql mysql -u css_user -p css_database < backup.sql
```

## 🆘 Support

- Email: dev@css-sfax.tn
- Documentation: https://docs.css-app.tn
- Issues: GitHub Issues

---

**Développé avec ❤️ pour le CSS et ses supporters**
