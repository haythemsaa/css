# CSS Socios - Complete Deployment Guide

> **Last Updated:** 2025-11-27
> **Version:** 1.0.0
> **Production Ready:** ✅ Yes

---

## 📋 Table of Contents

1. [Prerequisites](#prerequisites)
2. [Environment Setup](#environment-setup)
3. [Backend Deployment](#backend-deployment)
4. [Web App Deployment](#web-app-deployment)
5. [Mobile App Deployment](#mobile-app-deployment)
6. [Docker Deployment](#docker-deployment)
7. [Production Checklist](#production-checklist)
8. [Monitoring & Maintenance](#monitoring--maintenance)

---

## 🔧 Prerequisites

### Server Requirements

**Backend (Laravel 11):**
- PHP 8.2 or higher
- Composer 2.x
- MySQL 8.0+ or PostgreSQL 14+
- Redis 7.0+
- Nginx or Apache
- SSL Certificate (Let's Encrypt recommended)

**Web (React):**
- Node.js 18+ (LTS)
- npm 9+ or yarn 1.22+
- Nginx for static file serving

**Mobile (Flutter):**
- Flutter SDK 3.10+
- Dart SDK 3.0+
- Xcode 15+ (for iOS)
- Android Studio (for Android)

### Third-Party Services

- **Payment Gateways:**
  - D17 Account + API Keys
  - Konnect Merchant Account
  - Paymee API Credentials
  - Sadad Integration

- **Cloud Storage:**
  - AWS S3 or DigitalOcean Spaces
  - Cloudflare for CDN

- **Email Service:**
  - SendGrid or Mailgun
  - SMTP credentials

- **Push Notifications:**
  - Firebase Cloud Messaging (FCM)
  - Apple Push Notification Service (APNS)

---

## 🌍 Environment Setup

### 1. Clone Repository

```bash
git clone https://github.com/haythemsaa/css.git
cd css
```

### 2. Directory Structure

```
css/
├── backend/         # Laravel API
├── web/             # React Web App
├── mobile/          # Flutter Mobile App
├── infrastructure/  # Docker configs
└── docs/            # Documentation
```

---

## 🔙 Backend Deployment

### 1. Install Dependencies

```bash
cd backend
composer install --optimize-autoloader --no-dev
```

### 2. Environment Configuration

```bash
cp .env.example .env
php artisan key:generate
```

**Edit `.env` file:**

```env
# Application
APP_NAME="CSS Socios"
APP_ENV=production
APP_DEBUG=false
APP_URL=https://api.css.tn

# Database
DB_CONNECTION=mysql
DB_HOST=your-mysql-host
DB_PORT=3306
DB_DATABASE=css_socios
DB_USERNAME=css_user
DB_PASSWORD=your-secure-password

# Redis
REDIS_HOST=your-redis-host
REDIS_PASSWORD=your-redis-password
REDIS_PORT=6379

# Mail
MAIL_MAILER=smtp
MAIL_HOST=smtp.sendgrid.net
MAIL_PORT=587
MAIL_USERNAME=apikey
MAIL_PASSWORD=your-sendgrid-api-key
MAIL_FROM_ADDRESS=noreply@css.tn
MAIL_FROM_NAME="CSS Socios"

# AWS S3
AWS_ACCESS_KEY_ID=your-access-key
AWS_SECRET_ACCESS_KEY=your-secret-key
AWS_DEFAULT_REGION=eu-west-1
AWS_BUCKET=css-socios-media

# Payment Gateways
D17_MERCHANT_ID=your-d17-merchant-id
D17_API_KEY=your-d17-api-key
KONNECT_API_KEY=your-konnect-key
PAYMEE_API_KEY=your-paymee-key
SADAD_MERCHANT_ID=your-sadad-id

# Pusher/Broadcasting
BROADCAST_DRIVER=pusher
PUSHER_APP_ID=your-app-id
PUSHER_APP_KEY=your-app-key
PUSHER_APP_SECRET=your-app-secret
PUSHER_APP_CLUSTER=eu

# Session & Cache
SESSION_DRIVER=redis
CACHE_DRIVER=redis
QUEUE_CONNECTION=redis

# Telescope (disable in production)
TELESCOPE_ENABLED=false
```

### 3. Database Migration

```bash
# Run migrations
php artisan migrate --force

# Seed initial data (categories, permissions, etc.)
php artisan db:seed --class=ProductionSeeder
```

### 4. Cache & Optimize

```bash
# Cache configuration
php artisan config:cache

# Cache routes
php artisan route:cache

# Cache views
php artisan view:cache

# Optimize autoloader
composer dump-autoload --optimize
```

### 5. Storage Setup

```bash
# Create symbolic link for storage
php artisan storage:link

# Set permissions
chmod -R 755 storage bootstrap/cache
chown -R www-data:www-data storage bootstrap/cache
```

### 6. Queue Worker Setup

**Create systemd service** `/etc/systemd/system/css-worker.service`:

```ini
[Unit]
Description=CSS Socios Queue Worker
After=network.target

[Service]
Type=simple
User=www-data
WorkingDirectory=/var/www/css/backend
ExecStart=/usr/bin/php artisan queue:work --sleep=3 --tries=3 --max-time=3600
Restart=always
RestartSec=10s

[Install]
WantedBy=multi-user.target
```

**Enable and start:**

```bash
sudo systemctl enable css-worker
sudo systemctl start css-worker
```

### 7. Task Scheduler

**Add to crontab** (`crontab -e`):

```cron
* * * * * cd /var/www/css/backend && php artisan schedule:run >> /dev/null 2>&1
```

### 8. Nginx Configuration

**Create** `/etc/nginx/sites-available/css-api`:

```nginx
server {
    listen 80;
    listen [::]:80;
    server_name api.css.tn;
    return 301 https://$server_name$request_uri;
}

server {
    listen 443 ssl http2;
    listen [::]:443 ssl http2;
    server_name api.css.tn;

    ssl_certificate /etc/letsencrypt/live/api.css.tn/fullchain.pem;
    ssl_certificate_key /etc/letsencrypt/live/api.css.tn/privkey.pem;
    ssl_protocols TLSv1.2 TLSv1.3;
    ssl_ciphers HIGH:!aNULL:!MD5;

    root /var/www/css/backend/public;
    index index.php;

    client_max_body_size 20M;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.2-fpm.sock;
        fastcgi_index index.php;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
    }

    location ~ /\.(?!well-known).* {
        deny all;
    }

    # Security headers
    add_header X-Frame-Options "SAMEORIGIN" always;
    add_header X-XSS-Protection "1; mode=block" always;
    add_header X-Content-Type-Options "nosniff" always;
    add_header Referrer-Policy "no-referrer-when-downgrade" always;
    add_header Content-Security-Policy "default-src 'self' http: https: data: blob: 'unsafe-inline'" always;

    # Rate limiting
    limit_req_zone $binary_remote_addr zone=api:10m rate=60r/m;
    limit_req zone=api burst=20 nodelay;
}
```

**Enable site:**

```bash
sudo ln -s /etc/nginx/sites-available/css-api /etc/nginx/sites-enabled/
sudo nginx -t
sudo systemctl reload nginx
```

### 9. SSL Certificate

```bash
sudo certbot --nginx -d api.css.tn
```

---

## 🌐 Web App Deployment

### 1. Install Dependencies

```bash
cd web
npm install
```

### 2. Environment Configuration

**Create `.env.production`:**

```env
VITE_API_URL=https://api.css.tn/api/v1
VITE_APP_NAME="CSS Socios"
VITE_APP_ENV=production
```

### 3. Build for Production

```bash
npm run build
```

This creates optimized files in `dist/` directory.

### 4. Nginx Configuration

**Create** `/etc/nginx/sites-available/css-web`:

```nginx
server {
    listen 80;
    listen [::]:80;
    server_name css.tn www.css.tn;
    return 301 https://$server_name$request_uri;
}

server {
    listen 443 ssl http2;
    listen [::]:443 ssl http2;
    server_name css.tn www.css.tn;

    ssl_certificate /etc/letsencrypt/live/css.tn/fullchain.pem;
    ssl_certificate_key /etc/letsencrypt/live/css.tn/privkey.pem;

    root /var/www/css/web/dist;
    index index.html;

    # Gzip compression
    gzip on;
    gzip_vary on;
    gzip_min_length 1024;
    gzip_types text/plain text/css text/xml text/javascript application/x-javascript application/xml+rss application/javascript application/json;

    location / {
        try_files $uri $uri/ /index.html;
    }

    # Cache static assets
    location ~* \.(js|css|png|jpg|jpeg|gif|ico|svg|woff|woff2|ttf|eot)$ {
        expires 1y;
        add_header Cache-Control "public, immutable";
    }

    # Security headers
    add_header X-Frame-Options "DENY" always;
    add_header X-Content-Type-Options "nosniff" always;
    add_header X-XSS-Protection "1; mode=block" always;
}
```

**Enable site:**

```bash
sudo ln -s /etc/nginx/sites-available/css-web /etc/nginx/sites-enabled/
sudo systemctl reload nginx
```

### 5. SSL Certificate

```bash
sudo certbot --nginx -d css.tn -d www.css.tn
```

---

## 📱 Mobile App Deployment

### Flutter iOS Deployment

#### 1. Prepare for App Store

```bash
cd mobile
flutter clean
flutter pub get
```

#### 2. Update Version

**Edit `pubspec.yaml`:**

```yaml
version: 1.0.0+1
```

#### 3. Build iOS

```bash
flutter build ios --release
```

#### 4. Archive & Upload

1. Open `ios/Runner.xcworkspace` in Xcode
2. Select **Product > Archive**
3. Upload to App Store Connect
4. Submit for review

### Flutter Android Deployment

#### 1. Generate Keystore

```bash
keytool -genkey -v -keystore ~/css-release-key.jks -keyalg RSA -keysize 2048 -validity 10000 -alias css
```

#### 2. Configure Signing

**Create `android/key.properties`:**

```properties
storePassword=your-keystore-password
keyPassword=your-key-password
keyAlias=css
storeFile=/path/to/css-release-key.jks
```

**Update `android/app/build.gradle`:**

```gradle
android {
    signingConfigs {
        release {
            keyAlias keystoreProperties['keyAlias']
            keyPassword keystoreProperties['keyPassword']
            storeFile keystoreProperties['storeFile'] ? file(keystoreProperties['storeFile']) : null
            storePassword keystoreProperties['storePassword']
        }
    }

    buildTypes {
        release {
            signingConfig signingConfigs.release
        }
    }
}
```

#### 3. Build APK/Bundle

```bash
# For Play Store (recommended)
flutter build appbundle --release

# For direct distribution
flutter build apk --release --split-per-abi
```

#### 4. Upload to Play Store

1. Go to Google Play Console
2. Create new release
3. Upload `app-release.aab`
4. Fill in release notes
5. Submit for review

---

## 🐳 Docker Deployment

### 1. Build Images

```bash
cd infrastructure/docker

# Build all images
docker-compose build --no-cache
```

### 2. Production Docker Compose

**Create `docker-compose.prod.yml`:**

```yaml
version: '3.8'

services:
  backend:
    build:
      context: ../../backend
      dockerfile: ../infrastructure/docker/backend/Dockerfile
    environment:
      - APP_ENV=production
      - APP_DEBUG=false
    volumes:
      - backend-storage:/var/www/html/storage
    networks:
      - css-network

  web:
    build:
      context: ../../web
      dockerfile: ../infrastructure/docker/web/Dockerfile
    networks:
      - css-network

  mysql:
    image: mysql:8.0
    environment:
      MYSQL_ROOT_PASSWORD: ${DB_ROOT_PASSWORD}
      MYSQL_DATABASE: css_socios
      MYSQL_USER: css_user
      MYSQL_PASSWORD: ${DB_PASSWORD}
    volumes:
      - mysql-data:/var/lib/mysql
    networks:
      - css-network

  redis:
    image: redis:7-alpine
    command: redis-server --requirepass ${REDIS_PASSWORD}
    volumes:
      - redis-data:/data
    networks:
      - css-network

  nginx:
    build:
      context: .
      dockerfile: nginx/Dockerfile
    ports:
      - "80:80"
      - "443:443"
    volumes:
      - ./nginx/ssl:/etc/nginx/ssl:ro
    depends_on:
      - backend
      - web
    networks:
      - css-network

volumes:
  mysql-data:
  redis-data:
  backend-storage:

networks:
  css-network:
    driver: bridge
```

### 3. Deploy with Docker

```bash
# Set environment variables
export DB_PASSWORD=your-secure-password
export DB_ROOT_PASSWORD=your-root-password
export REDIS_PASSWORD=your-redis-password

# Start containers
docker-compose -f docker-compose.prod.yml up -d

# Run migrations
docker-compose exec backend php artisan migrate --force

# Check logs
docker-compose logs -f
```

---

## ✅ Production Checklist

### Security

- [ ] SSL certificates installed
- [ ] All `.env` files secured (not in version control)
- [ ] Database credentials rotated
- [ ] API rate limiting enabled
- [ ] CORS properly configured
- [ ] Security headers configured
- [ ] Firewall rules set up
- [ ] SSH key-based authentication only
- [ ] Regular security updates scheduled

### Performance

- [ ] Redis caching enabled
- [ ] Database indexes optimized
- [ ] CDN configured for static assets
- [ ] Gzip compression enabled
- [ ] Laravel optimizations applied
- [ ] Queue workers running
- [ ] Database connection pooling
- [ ] Image optimization pipeline

### Monitoring

- [ ] Application error tracking (Sentry)
- [ ] Server monitoring (New Relic/Datadog)
- [ ] Uptime monitoring (UptimeRobot)
- [ ] Log aggregation (ELK Stack)
- [ ] Performance monitoring (APM)
- [ ] Database slow query logging
- [ ] Backup verification system

### Backups

- [ ] Database backups (daily)
- [ ] File storage backups (daily)
- [ ] Backup retention policy (30 days)
- [ ] Backup restoration tested
- [ ] Off-site backup storage
- [ ] Automated backup verification

### Documentation

- [ ] API documentation published
- [ ] Admin user guide created
- [ ] Deployment runbook updated
- [ ] Incident response plan
- [ ] Contact escalation list

---

## 📊 Monitoring & Maintenance

### Health Checks

**Backend Health Check:**
```bash
curl https://api.css.tn/health
```

**Database Check:**
```bash
php artisan db:monitor
```

**Queue Status:**
```bash
php artisan queue:monitor
```

### Log Monitoring

**Laravel Logs:**
```bash
tail -f storage/logs/laravel.log
```

**Nginx Access Logs:**
```bash
tail -f /var/log/nginx/access.log
```

**Nginx Error Logs:**
```bash
tail -f /var/log/nginx/error.log
```

### Performance Monitoring

**Database Queries:**
```bash
php artisan telescope:prune --hours=48
```

**Redis Monitor:**
```bash
redis-cli monitor
```

### Backup Commands

**Database Backup:**
```bash
php artisan backup:run --only-db
```

**Full Backup:**
```bash
php artisan backup:run
```

**Clean Old Backups:**
```bash
php artisan backup:clean
```

### Maintenance Mode

**Enable:**
```bash
php artisan down --secret="css-maintenance-2025"
```

**Disable:**
```bash
php artisan up
```

**Access during maintenance:**
```
https://api.css.tn/css-maintenance-2025
```

---

## 🚨 Troubleshooting

### Common Issues

**502 Bad Gateway:**
- Check PHP-FPM status: `systemctl status php8.2-fpm`
- Check Nginx error logs
- Verify PHP socket path in Nginx config

**500 Internal Server Error:**
- Check Laravel logs: `storage/logs/laravel.log`
- Verify file permissions
- Clear cache: `php artisan cache:clear`

**Database Connection Failed:**
- Verify MySQL is running: `systemctl status mysql`
- Check database credentials in `.env`
- Test connection: `php artisan tinker` → `DB::connection()->getPdo();`

**Queue Not Processing:**
- Check worker status: `systemctl status css-worker`
- Restart worker: `systemctl restart css-worker`
- Check failed jobs: `php artisan queue:failed`

---

## 📞 Support Contacts

**Development Team:**
- Email: dev@css.tn
- Slack: #css-dev

**Infrastructure:**
- Email: ops@css.tn
- On-call: +216 XX XXX XXX

**Emergency:**
- Escalation: CTO
- Backup: Lead Developer

---

## 📝 Deployment History

| Date | Version | Changes | Deployed By |
|------|---------|---------|-------------|
| 2025-11-27 | 1.0.0 | Initial production release | DevOps Team |

---

**End of Deployment Guide**

*Last updated: 2025-11-27*
*Next review: 2025-12-27*
