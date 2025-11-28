# 🚀 Installation Rapide - CSS Socios Production

Guide d'installation express pour déployer CSS Socios en production en **30 minutes**.

---

## 📋 Prérequis

### Serveur (Ubuntu 22.04 LTS recommandé)
- **CPU:** 4 cores minimum
- **RAM:** 8 GB minimum
- **Stockage:** 50 GB SSD
- **Domaine:** css-socios.tn (exemple)

### Logiciels requis
```bash
# Mise à jour système
sudo apt update && sudo apt upgrade -y

# Installation des dépendances
sudo apt install -y nginx mysql-server redis-server git curl unzip \
    php8.2 php8.2-fpm php8.2-mysql php8.2-redis php8.2-mbstring \
    php8.2-xml php8.2-curl php8.2-zip php8.2-gd php8.2-bcmath \
    nodejs npm certbot python3-certbot-nginx
```

---

## ⚡ Installation Rapide (Script Automatisé)

### Option 1: Installation Automatique (RECOMMANDÉ)

```bash
# 1. Télécharger le script d'installation
curl -O https://raw.githubusercontent.com/votre-repo/css/main/install.sh
chmod +x install.sh

# 2. Lancer l'installation
sudo ./install.sh

# Le script va:
# - Configurer MySQL, Redis, Nginx
# - Installer Composer et les dépendances PHP
# - Configurer le backend Laravel
# - Builder l'app React
# - Configurer SSL avec Let's Encrypt
# - Démarrer tous les services
```

---

## 🔧 Installation Manuelle (Pas à Pas)

### 1️⃣ Backend (Laravel API)

```bash
# Cloner le projet
cd /var/www
sudo git clone https://github.com/votre-repo/css.git
sudo chown -R www-data:www-data css

# Installer Composer
cd css/backend
curl -sS https://getcomposer.org/installer | php
sudo mv composer.phar /usr/local/bin/composer

# Installer dépendances
composer install --optimize-autoloader --no-dev

# Configuration
cp .env.example .env
php artisan key:generate

# Éditer .env
nano .env
```

**Configuration .env (Production):**
```env
APP_NAME="CSS Socios"
APP_ENV=production
APP_DEBUG=false
APP_URL=https://api.css-socios.tn

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=css_socios
DB_USERNAME=css_user
DB_PASSWORD=VOTRE_MOT_DE_PASSE_FORT

REDIS_HOST=127.0.0.1
REDIS_PASSWORD=null
REDIS_PORT=6379

SANCTUM_STATEFUL_DOMAINS=css-socios.tn,www.css-socios.tn
SESSION_DOMAIN=.css-socios.tn
```

```bash
# Base de données
sudo mysql -u root -p

CREATE DATABASE css_socios CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
CREATE USER 'css_user'@'localhost' IDENTIFIED BY 'VOTRE_MOT_DE_PASSE_FORT';
GRANT ALL PRIVILEGES ON css_socios.* TO 'css_user'@'localhost';
FLUSH PRIVILEGES;
EXIT;

# Migrations
php artisan migrate --force
php artisan db:seed --force

# Optimisations
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Permissions
sudo chown -R www-data:www-data storage bootstrap/cache
sudo chmod -R 775 storage bootstrap/cache
```

### 2️⃣ Web (React App)

```bash
cd /var/www/css/web

# Installer dépendances
npm install

# Configuration
cp .env.example .env
nano .env
```

**Configuration .env (Web):**
```env
VITE_API_URL=https://api.css-socios.tn/api/v1
VITE_APP_NAME="CSS Socios"
```

```bash
# Build production
npm run build

# Les fichiers sont dans dist/
```

### 3️⃣ Nginx Configuration

```bash
# Backend API
sudo nano /etc/nginx/sites-available/api.css-socios.tn
```

```nginx
server {
    listen 80;
    server_name api.css-socios.tn;
    root /var/www/css/backend/public;
    index index.php;

    add_header X-Frame-Options "SAMEORIGIN";
    add_header X-Content-Type-Options "nosniff";

    charset utf-8;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.2-fpm.sock;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
    }

    location ~ /\.(?!well-known).* {
        deny all;
    }
}
```

```bash
# Frontend Web
sudo nano /etc/nginx/sites-available/css-socios.tn
```

```nginx
server {
    listen 80;
    server_name css-socios.tn www.css-socios.tn;
    root /var/www/css/web/dist;
    index index.html;

    location / {
        try_files $uri $uri/ /index.html;
    }

    location ~* \.(js|css|png|jpg|jpeg|gif|svg|ico|woff|woff2|ttf|eot)$ {
        expires 1y;
        add_header Cache-Control "public, immutable";
    }
}
```

```bash
# Activer les sites
sudo ln -s /etc/nginx/sites-available/api.css-socios.tn /etc/nginx/sites-enabled/
sudo ln -s /etc/nginx/sites-available/css-socios.tn /etc/nginx/sites-enabled/

# Tester et redémarrer
sudo nginx -t
sudo systemctl restart nginx
```

### 4️⃣ SSL (Let's Encrypt)

```bash
# Certificats SSL gratuits
sudo certbot --nginx -d api.css-socios.tn
sudo certbot --nginx -d css-socios.tn -d www.css-socios.tn

# Renouvellement automatique
sudo certbot renew --dry-run
```

### 5️⃣ Queue Worker (Background Jobs)

```bash
# Créer un service systemd
sudo nano /etc/systemd/system/css-queue-worker.service
```

```ini
[Unit]
Description=CSS Socios Queue Worker
After=network.target

[Service]
Type=simple
User=www-data
WorkingDirectory=/var/www/css/backend
ExecStart=/usr/bin/php /var/www/css/backend/artisan queue:work --sleep=3 --tries=3 --max-time=3600
Restart=always
RestartSec=10

[Install]
WantedBy=multi-user.target
```

```bash
# Activer et démarrer
sudo systemctl enable css-queue-worker
sudo systemctl start css-queue-worker
sudo systemctl status css-queue-worker
```

### 6️⃣ Cron (Scheduler)

```bash
# Éditer crontab
sudo crontab -e -u www-data

# Ajouter cette ligne
* * * * * cd /var/www/css/backend && php artisan schedule:run >> /dev/null 2>&1
```

---

## 📱 Application Mobile (Flutter)

### Android

```bash
cd mobile

# Configuration
cp .env.example .env
nano .env
```

```env
API_URL=https://api.css-socios.tn/api/v1
APP_NAME=CSS Socios
```

```bash
# Build APK
flutter clean
flutter pub get
flutter build apk --release

# Le fichier APK est dans: build/app/outputs/flutter-apk/app-release.apk
```

**Publier sur Google Play:**
1. Créer un compte Google Play Developer (25$ unique)
2. Créer une nouvelle app
3. Uploader `app-release.apk`
4. Remplir les infos (description, captures d'écran)
5. Soumettre pour review

### iOS

```bash
# Build IPA (nécessite un Mac)
flutter clean
flutter pub get
flutter build ios --release

# Ouvrir dans Xcode
open ios/Runner.xcworkspace
```

**Publier sur App Store:**
1. Compte Apple Developer (99$/an)
2. Archive dans Xcode
3. Upload vers App Store Connect
4. Soumettre pour review

---

## 🔐 Sécurité Production

### Firewall

```bash
# UFW (Uncomplicated Firewall)
sudo ufw allow OpenSSH
sudo ufw allow 'Nginx Full'
sudo ufw enable
sudo ufw status
```

### Fail2Ban (Protection brute force)

```bash
sudo apt install fail2ban -y
sudo systemctl enable fail2ban
sudo systemctl start fail2ban
```

### Sauvegardes Automatiques

```bash
# Script de backup
sudo nano /usr/local/bin/backup-css.sh
```

```bash
#!/bin/bash
DATE=$(date +%Y%m%d_%H%M%S)
BACKUP_DIR="/backups/css"

# Créer répertoire
mkdir -p $BACKUP_DIR

# Backup base de données
mysqldump -u css_user -p'VOTRE_MOT_DE_PASSE' css_socios | gzip > $BACKUP_DIR/db_$DATE.sql.gz

# Backup fichiers
tar -czf $BACKUP_DIR/files_$DATE.tar.gz /var/www/css/backend/storage

# Garder seulement les 30 derniers jours
find $BACKUP_DIR -type f -mtime +30 -delete

echo "Backup terminé: $DATE"
```

```bash
# Rendre exécutable
sudo chmod +x /usr/local/bin/backup-css.sh

# Cron quotidien (3h du matin)
sudo crontab -e
0 3 * * * /usr/local/bin/backup-css.sh >> /var/log/css-backup.log 2>&1
```

---

## 📊 Monitoring

### Logs

```bash
# Nginx logs
sudo tail -f /var/log/nginx/error.log
sudo tail -f /var/log/nginx/access.log

# Laravel logs
sudo tail -f /var/www/css/backend/storage/logs/laravel.log

# Queue worker logs
sudo journalctl -u css-queue-worker -f
```

### Performance

```bash
# Activer OPcache (PHP)
sudo nano /etc/php/8.2/fpm/php.ini

# Ajouter:
opcache.enable=1
opcache.memory_consumption=256
opcache.interned_strings_buffer=16
opcache.max_accelerated_files=10000

sudo systemctl restart php8.2-fpm
```

---

## ✅ Vérification Post-Installation

### Tests API

```bash
# Health check
curl https://api.css-socios.tn/health

# Devrait retourner: {"status":"ok","timestamp":"..."}

# Test login
curl -X POST https://api.css-socios.tn/api/v1/auth/login \
  -H "Content-Type: application/json" \
  -d '{"email":"admin@css.tn","password":"password"}'
```

### Tests Frontend

```bash
# Ouvrir dans le navigateur
https://css-socios.tn

# Vérifier:
# - Page d'accueil charge
# - Login fonctionne
# - API calls fonctionnent
```

### Performance

```bash
# Test de charge avec Apache Bench
ab -n 1000 -c 10 https://api.css-socios.tn/health
```

---

## 🚨 Dépannage

### Problème: Erreur 500

```bash
# Vérifier logs Laravel
sudo tail -100 /var/www/css/backend/storage/logs/laravel.log

# Vérifier permissions
sudo chown -R www-data:www-data /var/www/css/backend/storage
sudo chmod -R 775 /var/www/css/backend/storage
```

### Problème: Queue ne fonctionne pas

```bash
# Redémarrer le worker
sudo systemctl restart css-queue-worker
sudo systemctl status css-queue-worker

# Vérifier logs
sudo journalctl -u css-queue-worker -n 50
```

### Problème: Base de données

```bash
# Vérifier connexion MySQL
sudo mysql -u css_user -p

# Vérifier migrations
cd /var/www/css/backend
php artisan migrate:status
```

---

## 📞 Support

**En cas de problème:**
- **Logs Laravel:** `/var/www/css/backend/storage/logs/`
- **Logs Nginx:** `/var/log/nginx/`
- **Logs système:** `journalctl -xe`

---

## 🎯 Checklist Finale

- [ ] Backend API accessible (https://api.css-socios.tn)
- [ ] Frontend Web accessible (https://css-socios.tn)
- [ ] SSL actif (cadenas vert)
- [ ] Base de données migrée
- [ ] Queue worker actif
- [ ] Cron scheduler actif
- [ ] Firewall configuré
- [ ] Backups automatiques configurés
- [ ] App Android publiée
- [ ] App iOS publiée
- [ ] Monitoring actif

---

## 🚀 Temps Estimé

- **Backend:** 15 minutes
- **Frontend:** 10 minutes
- **Configuration serveur:** 15 minutes
- **SSL:** 5 minutes
- **Mobile (build):** 10 minutes

**TOTAL: ~55 minutes** ⚡

---

**Le projet est maintenant en PRODUCTION ! 🎉**

Pour les détails complets, voir `DEPLOY_GUIDE.md`
