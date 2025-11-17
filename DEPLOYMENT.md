# Guide de Déploiement - Plateforme CSS

Ce guide détaille les étapes pour déployer la plateforme CSS en environnement de staging et production.

## 📋 Table des matières

- [Prérequis](#prérequis)
- [Architecture](#architecture)
- [Environnements](#environnements)
- [Déploiement Backend](#déploiement-backend)
- [Déploiement Frontend](#déploiement-frontend)
- [Configuration Base de Données](#configuration-base-de-données)
- [SSL/HTTPS](#sslhttps)
- [Optimisations](#optimisations)
- [Monitoring](#monitoring)
- [Troubleshooting](#troubleshooting)

---

## 🔧 Prérequis

### Serveur Backend (Laravel)

- **OS**: Ubuntu 22.04 LTS ou similaire
- **Web Server**: Nginx 1.18+ ou Apache 2.4+
- **PHP**: 8.4+
- **Extensions PHP requises**:
  - BCMath
  - Ctype
  - Fileinfo
  - JSON
  - Mbstring
  - OpenSSL
  - PDO
  - Tokenizer
  - XML
  - GD
  - SQLite3 (dev) / MySQL (prod)
  - Redis (optionnel, recommandé)
- **Composer**: 2.x
- **Base de données**:
  - MySQL 8.0+ ou PostgreSQL 14+ (production)
  - SQLite (développement)
- **Queue Worker**: Supervisor
- **Cache**: Redis (recommandé)

### Serveur Frontend (React)

- **Node.js**: 18+ LTS
- **NPM**: 9+
- **Web Server**: Nginx pour servir les fichiers statiques

### Outils Requis

- **Git** pour le déploiement
- **Certbot** pour SSL/HTTPS
- **PM2** ou **Supervisor** pour les processus

---

## 🏗️ Architecture

### Architecture Recommandée

```
┌─────────────────────────────────────────────┐
│           Load Balancer / CDN               │
│         (Cloudflare, AWS CloudFront)        │
└────────────┬───────────────────┬────────────┘
             │                   │
    ┌────────▼────────┐ ┌───────▼────────┐
    │  Frontend Nginx │ │ Backend Nginx  │
    │   (Static)      │ │  (PHP-FPM)     │
    │  Port 80/443    │ │  Port 80/443   │
    └─────────────────┘ └────────┬───────┘
                                 │
                    ┌────────────▼──────────────┐
                    │    Laravel Application    │
                    │      + Filament Admin     │
                    └────────┬──────────┬───────┘
                             │          │
                   ┌─────────▼──┐   ┌──▼─────────┐
                   │  Database  │   │   Redis    │
                   │   MySQL    │   │   Cache    │
                   └────────────┘   └────────────┘
```

### Domaines Suggérés

- **Frontend**: `www.css-sfax.tn` ou `app.css-sfax.tn`
- **Backend API**: `api.css-sfax.tn`
- **Admin**: `admin.css-sfax.tn`

---

## 🌍 Environnements

### 1. Développement (Local)

```bash
Backend:  http://localhost:8000
Frontend: http://localhost:5173
Database: SQLite
```

### 2. Staging

```bash
Backend:  https://staging-api.css-sfax.tn
Frontend: https://staging.css-sfax.tn
Database: MySQL (cloud ou serveur dédié)
```

### 3. Production

```bash
Backend:  https://api.css-sfax.tn
Frontend: https://www.css-sfax.tn
Database: MySQL (cluster avec réplication)
```

---

## 🚀 Déploiement Backend

### Étape 1: Préparation du Serveur

```bash
# Mise à jour du système
sudo apt update && sudo apt upgrade -y

# Installation des dépendances
sudo apt install -y nginx php8.4-fpm php8.4-cli php8.4-common \
  php8.4-mysql php8.4-mbstring php8.4-xml php8.4-curl \
  php8.4-gd php8.4-zip php8.4-bcmath php8.4-redis \
  mysql-server redis-server git composer supervisor
```

### Étape 2: Configuration de la Base de Données

```bash
# Se connecter à MySQL
sudo mysql

# Créer la base de données et l'utilisateur
CREATE DATABASE css_platform CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
CREATE USER 'css_user'@'localhost' IDENTIFIED BY 'STRONG_PASSWORD_HERE';
GRANT ALL PRIVILEGES ON css_platform.* TO 'css_user'@'localhost';
FLUSH PRIVILEGES;
EXIT;
```

### Étape 3: Clonage du Projet

```bash
# Créer le répertoire du projet
sudo mkdir -p /var/www/css
sudo chown -R $USER:$USER /var/www/css

# Cloner le repository
cd /var/www/css
git clone https://github.com/haythemsaa/css.git .
cd backend
```

### Étape 4: Configuration Laravel

```bash
# Copier le fichier d'environnement
cp .env.example .env

# Éditer le fichier .env
nano .env
```

**Configuration `.env` pour Production:**

```env
APP_NAME="CSS Platform"
APP_ENV=production
APP_KEY=  # Sera généré
APP_DEBUG=false
APP_URL=https://api.css-sfax.tn

LOG_CHANNEL=stack
LOG_LEVEL=error

# Database
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=css_platform
DB_USERNAME=css_user
DB_PASSWORD=STRONG_PASSWORD_HERE

# Redis
BROADCAST_DRIVER=redis
CACHE_DRIVER=redis
FILESYSTEM_DISK=public
QUEUE_CONNECTION=redis
SESSION_DRIVER=redis

REDIS_HOST=127.0.0.1
REDIS_PASSWORD=null
REDIS_PORT=6379

# Mail (Configure selon votre provider)
MAIL_MAILER=smtp
MAIL_HOST=smtp.mailtrap.io
MAIL_PORT=2525
MAIL_USERNAME=your_username
MAIL_PASSWORD=your_password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS="noreply@css-sfax.tn"
MAIL_FROM_NAME="${APP_NAME}"

# Frontend URL
FRONTEND_URL=https://www.css-sfax.tn

# CORS
CORS_ALLOWED_ORIGINS=https://www.css-sfax.tn,https://app.css-sfax.tn

# Filament
FILAMENT_PANEL_PATH=admin
```

### Étape 5: Installation des Dépendances

```bash
# Installer les dépendances Composer
composer install --optimize-autoloader --no-dev

# Générer la clé d'application
php artisan key:generate

# Optimisations
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan event:cache
```

### Étape 6: Migration de la Base de Données

```bash
# Exécuter les migrations
php artisan migrate --force

# Exécuter les seeders (uniquement en staging ou première installation)
php artisan db:seed --force
```

### Étape 7: Permissions des Fichiers

```bash
# Définir les permissions correctes
sudo chown -R www-data:www-data /var/www/css/backend/storage
sudo chown -R www-data:www-data /var/www/css/backend/bootstrap/cache

chmod -R 775 /var/www/css/backend/storage
chmod -R 775 /var/www/css/backend/bootstrap/cache
```

### Étape 8: Configuration Nginx

```bash
# Créer le fichier de configuration
sudo nano /etc/nginx/sites-available/css-api
```

**Configuration Nginx:**

```nginx
server {
    listen 80;
    server_name api.css-sfax.tn;
    root /var/www/css/backend/public;

    add_header X-Frame-Options "SAMEORIGIN";
    add_header X-Content-Type-Options "nosniff";

    index index.php;

    charset utf-8;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location = /favicon.ico { access_log off; log_not_found off; }
    location = /robots.txt  { access_log off; log_not_found off; }

    error_page 404 /index.php;

    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.4-fpm.sock;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
    }

    location ~ /\.(?!well-known).* {
        deny all;
    }
}
```

```bash
# Activer le site
sudo ln -s /etc/nginx/sites-available/css-api /etc/nginx/sites-enabled/

# Tester la configuration
sudo nginx -t

# Redémarrer Nginx
sudo systemctl restart nginx
```

### Étape 9: Configuration Queue Worker (Supervisor)

```bash
# Créer le fichier de configuration
sudo nano /etc/supervisor/conf.d/css-worker.conf
```

```ini
[program:css-worker]
process_name=%(program_name)s_%(process_num)02d
command=php /var/www/css/backend/artisan queue:work redis --sleep=3 --tries=3 --max-time=3600
autostart=true
autorestart=true
stopasgroup=true
killasgroup=true
user=www-data
numprocs=4
redirect_stderr=true
stdout_logfile=/var/www/css/backend/storage/logs/worker.log
stopwaitsecs=3600
```

```bash
# Recharger Supervisor
sudo supervisorctl reread
sudo supervisorctl update
sudo supervisorctl start css-worker:*
```

### Étape 10: Configuration Horizon (Optionnel)

```bash
# Si vous utilisez Laravel Horizon au lieu de queue:work
sudo nano /etc/supervisor/conf.d/css-horizon.conf
```

```ini
[program:css-horizon]
process_name=%(program_name)s
command=php /var/www/css/backend/artisan horizon
autostart=true
autorestart=true
user=www-data
redirect_stderr=true
stdout_logfile=/var/www/css/backend/storage/logs/horizon.log
stopwaitsecs=3600
```

---

## 🎨 Déploiement Frontend

### Étape 1: Build de Production

```bash
# Sur votre machine de développement ou serveur
cd /var/www/css/frontend

# Installer les dépendances
npm install

# Créer le fichier .env de production
nano .env.production
```

**Configuration `.env.production`:**

```env
VITE_API_URL=https://api.css-sfax.tn/api/v1
VITE_APP_NAME=CSS Platform
VITE_APP_ENV=production
```

```bash
# Build pour production
npm run build

# Les fichiers seront dans le dossier dist/
```

### Étape 2: Déploiement des Fichiers Statiques

```bash
# Copier les fichiers build vers le serveur web
sudo mkdir -p /var/www/css-frontend
sudo cp -r dist/* /var/www/css-frontend/

# Définir les permissions
sudo chown -R www-data:www-data /var/www/css-frontend
```

### Étape 3: Configuration Nginx pour Frontend

```bash
sudo nano /etc/nginx/sites-available/css-frontend
```

```nginx
server {
    listen 80;
    server_name www.css-sfax.tn css-sfax.tn;
    root /var/www/css-frontend;

    index index.html;

    location / {
        try_files $uri $uri/ /index.html;
    }

    # Gzip compression
    gzip on;
    gzip_vary on;
    gzip_min_length 1024;
    gzip_types text/plain text/css text/xml text/javascript application/x-javascript application/xml+rss application/json application/javascript;

    # Cache des assets
    location ~* \.(js|css|png|jpg|jpeg|gif|ico|svg|woff|woff2|ttf|eot)$ {
        expires 1y;
        add_header Cache-Control "public, immutable";
    }

    # Security headers
    add_header X-Frame-Options "SAMEORIGIN" always;
    add_header X-Content-Type-Options "nosniff" always;
    add_header X-XSS-Protection "1; mode=block" always;
}
```

```bash
# Activer le site
sudo ln -s /etc/nginx/sites-available/css-frontend /etc/nginx/sites-enabled/

# Tester et redémarrer
sudo nginx -t
sudo systemctl restart nginx
```

---

## 🔒 SSL/HTTPS

### Installation Certbot

```bash
# Installer Certbot
sudo apt install -y certbot python3-certbot-nginx

# Obtenir les certificats SSL
sudo certbot --nginx -d api.css-sfax.tn
sudo certbot --nginx -d www.css-sfax.tn -d css-sfax.tn

# Renouvellement automatique (vérifier)
sudo certbot renew --dry-run
```

### Configuration Auto-renewal

```bash
# Ajouter au crontab
sudo crontab -e

# Ajouter cette ligne
0 12 * * * /usr/bin/certbot renew --quiet
```

---

## ⚡ Optimisations

### Backend

```bash
# OPcache PHP
sudo nano /etc/php/8.4/fpm/conf.d/10-opcache.ini
```

```ini
opcache.enable=1
opcache.memory_consumption=256
opcache.interned_strings_buffer=16
opcache.max_accelerated_files=20000
opcache.validate_timestamps=0
opcache.revalidate_freq=0
opcache.fast_shutdown=1
```

```bash
# Redémarrer PHP-FPM
sudo systemctl restart php8.4-fpm
```

### MySQL

```bash
sudo nano /etc/mysql/mysql.conf.d/mysqld.cnf
```

```ini
[mysqld]
innodb_buffer_pool_size = 1G
innodb_log_file_size = 256M
max_connections = 200
query_cache_size = 64M
```

### Redis

```bash
sudo nano /etc/redis/redis.conf
```

```
maxmemory 512mb
maxmemory-policy allkeys-lru
```

---

## 📊 Monitoring

### Logs à Surveiller

```bash
# Backend logs
tail -f /var/www/css/backend/storage/logs/laravel.log

# Nginx access
tail -f /var/log/nginx/access.log

# Nginx errors
tail -f /var/log/nginx/error.log

# PHP-FPM
tail -f /var/log/php8.4-fpm.log
```

### Monitoring Recommandé

- **Uptime**: UptimeRobot, Pingdom
- **Performance**: New Relic, DataDog
- **Logs**: Papertrail, Loggly
- **Errors**: Sentry, Bugsnag

---

## 🔧 Troubleshooting

### Problème: 500 Internal Server Error

```bash
# Vérifier les logs
tail -f /var/www/css/backend/storage/logs/laravel.log
tail -f /var/log/nginx/error.log

# Vérifier les permissions
sudo chown -R www-data:www-data /var/www/css/backend/storage
chmod -R 775 /var/www/css/backend/storage
```

### Problème: Queue Workers ne fonctionnent pas

```bash
# Vérifier le status
sudo supervisorctl status css-worker:*

# Redémarrer
sudo supervisorctl restart css-worker:*

# Vérifier les logs
tail -f /var/www/css/backend/storage/logs/worker.log
```

### Problème: CORS Errors

```bash
# Vérifier la configuration CORS dans backend/config/cors.php
# S'assurer que le frontend URL est dans CORS_ALLOWED_ORIGINS

# Nettoyer le cache
php artisan config:clear
php artisan cache:clear
php artisan config:cache
```

### Problème: Frontend ne charge pas

```bash
# Vérifier les permissions
sudo chown -R www-data:www-data /var/www/css-frontend

# Vérifier la configuration Nginx
sudo nginx -t

# Vérifier le fichier index.html existe
ls -la /var/www/css-frontend/index.html
```

---

## 🔄 Mise à Jour

### Backend

```bash
cd /var/www/css/backend

# Pull les dernières modifications
git pull origin main

# Mise à jour des dépendances
composer install --optimize-autoloader --no-dev

# Migrations
php artisan migrate --force

# Clear et recache
php artisan config:clear
php artisan cache:clear
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Redémarrer les workers
sudo supervisorctl restart css-worker:*
```

### Frontend

```bash
cd /var/www/css/frontend

# Pull les modifications
git pull origin main

# Rebuild
npm install
npm run build

# Copier vers le dossier de production
sudo cp -r dist/* /var/www/css-frontend/
```

---

## 📝 Checklist de Déploiement

### Avant le Déploiement

- [ ] Tests passent (backend)
- [ ] Build frontend sans erreurs
- [ ] Variables d'environnement configurées
- [ ] Base de données sauvegardée
- [ ] SSL certificats prêts
- [ ] DNS configuré

### Après le Déploiement

- [ ] Site accessible via HTTPS
- [ ] API répond correctement
- [ ] Admin Filament fonctionne
- [ ] Queue workers actifs
- [ ] Logs ne montrent pas d'erreurs
- [ ] Monitoring configuré
- [ ] Backups automatiques configurés

---

## 🆘 Support

Pour toute question ou problème :
- **Documentation Laravel**: https://laravel.com/docs
- **Documentation React**: https://react.dev
- **Documentation Nginx**: https://nginx.org/en/docs

---

**⚽ Club Sportif Sfaxien - يا CSS يا نجوم السما ⚽**
