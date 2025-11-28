# 🚀 Production Pre-Flight Checklist - CSS Socios

## ✅ AVANT DE DÉPLOYER

### 1. Configuration Serveur Production
- [ ] Serveur Ubuntu 22.04 LTS configuré
- [ ] 4 CPU cores minimum
- [ ] 8 GB RAM minimum
- [ ] 50 GB SSD minimum
- [ ] Nom de domaine configuré (ex: api.css-sfax.tn, app.css-sfax.tn)

### 2. Credentials et Secrets
- [ ] **Base de données**: Créer utilisateur MySQL avec mot de passe fort
- [ ] **Redis**: Configurer mot de passe Redis
- [ ] **APP_KEY**: Généré via `php artisan key:generate`
- [ ] **JWT_SECRET**: Générer secret sécurisé (32+ caractères aléatoires)
- [ ] **Mail SMTP**: Configurer service email (Mailgun, SendGrid, etc.)
- [ ] **SMS API**: Configurer gateway SMS tunisien
- [ ] **AWS S3**: Créer bucket et credentials (stockage fichiers)
- [ ] **Stripe**: Clés API production (paiements internationaux)
- [ ] **D17**: Clés API production (paiements mobiles Tunisie)
- [ ] **Konnect**: Clés API production (wallet Tunisie)
- [ ] **Firebase FCM**: Clés pour notifications push mobile
- [ ] **Pusher**: Clés pour broadcasting temps réel
- [ ] **Social Auth**: Client ID/Secret Facebook et Google

### 3. Configuration .env Production
```bash
# Copier et modifier .env.example
cp .env.example .env

# IMPORTANT: Modifier ces valeurs
APP_ENV=production
APP_DEBUG=false
APP_URL=https://api.css-sfax.tn
FRONTEND_URL=https://app.css-sfax.tn

# Database
DB_PASSWORD=votre_mot_de_passe_securise

# Redis
REDIS_PASSWORD=votre_mot_de_passe_redis

# Email (exemple avec Mailgun)
MAIL_MAILER=smtp
MAIL_HOST=smtp.mailgun.org
MAIL_PORT=587
MAIL_USERNAME=postmaster@css-sfax.tn
MAIL_PASSWORD=votre_cle_mailgun
MAIL_ENCRYPTION=tls

# Sentry (monitoring erreurs)
SENTRY_LARAVEL_DSN=https://votre_cle@sentry.io/projet
```

### 4. Sécurité
- [ ] `.env` ajouté au `.gitignore` (déjà fait)
- [ ] Firewall UFW activé (ports 80, 443, 22 uniquement)
- [ ] Fail2Ban installé et configuré
- [ ] SSL/TLS certificat Let's Encrypt installé
- [ ] Permissions fichiers correctes (storage et bootstrap/cache en 775)
- [ ] Utilisateur www-data propriétaire des fichiers

### 5. Optimisations Laravel Production
```bash
# À exécuter sur le serveur après déploiement
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan event:cache
composer install --optimize-autoloader --no-dev
```

### 6. Base de Données
```bash
# Migrations
php artisan migrate --force

# Seeders PRODUCTION (données initiales uniquement)
php artisan db:seed --class=AdminUserSeeder
php artisan db:seed --class=TeamSeeder
php artisan db:seed --class=PlayerSeeder
php artisan db:seed --class=BadgeSeeder
php artisan db:seed --class=PaymentMethodSeeder
php artisan db:seed --class=SubscriptionTierSeeder
php artisan db:seed --class=SociosBenefitSeeder
php artisan db:seed --class=ContentCategorySeeder
php artisan db:seed --class=PartnerCategorySeeder

# ⚠️ NE PAS exécuter les autres seeders en production
# (ContentSeeder, ProductSeeder, etc. sont pour données de test)
```

### 7. Queue Workers (Systemd)
- [ ] Service Laravel queue worker créé
- [ ] Service démarré et enabled au boot
```bash
sudo systemctl start laravel-worker
sudo systemctl enable laravel-worker
sudo systemctl status laravel-worker
```

### 8. Cron Scheduler
- [ ] Crontab configuré pour Laravel scheduler
```bash
* * * * * cd /var/www/css/backend && php artisan schedule:run >> /dev/null 2>&1
```

### 9. Nginx Configuration
- [ ] Configuration API (api.css-sfax.tn)
- [ ] Configuration Frontend (app.css-sfax.tn)
- [ ] SSL configuré pour les deux
- [ ] Nginx redémarre sans erreur
```bash
sudo nginx -t
sudo systemctl reload nginx
```

### 10. Frontend Web (React)
```bash
cd frontend
npm install
npm run build
# Les fichiers sont dans frontend/build/
# Déjà configuré pour être servi par Nginx
```

### 11. Mobile App
- [ ] **Android**: Build APK/AAB pour Google Play
  ```bash
  cd mobile
  flutter build apk --release
  flutter build appbundle --release
  ```
- [ ] **iOS**: Build IPA pour App Store
  ```bash
  flutter build ipa --release
  ```

### 12. Backups Automatiques
- [ ] Script backup MySQL configuré (daily à 2h)
- [ ] Script backup fichiers configuré (daily à 3h)
- [ ] Backups stockés sur S3 ou stockage externe
- [ ] Testez la restauration d'un backup

### 13. Monitoring
- [ ] Logs Laravel accessibles (`storage/logs/laravel.log`)
- [ ] Nginx logs (`/var/log/nginx/access.log` et `error.log`)
- [ ] Sentry configuré pour tracking erreurs
- [ ] Telescope désactivé en production (ou protégé par auth)
```env
TELESCOPE_ENABLED=false
```

### 14. Tests Pré-Production
```bash
# Backend
cd backend
php artisan test

# Frontend
cd frontend
npm run test
```

### 15. Vérifications Post-Déploiement
- [ ] **API Health Check**: `curl https://api.css-sfax.tn/api/v1/health`
- [ ] **Frontend charge**: Ouvrir https://app.css-sfax.tn
- [ ] **Inscription utilisateur** fonctionne
- [ ] **Login utilisateur** fonctionne
- [ ] **Admin panel** accessible (Filament)
- [ ] **Notifications push** fonctionnent
- [ ] **Upload fichiers** fonctionne (S3)
- [ ] **Paiement test** fonctionne
- [ ] **Email test** envoyé
- [ ] **SMS test** envoyé (si applicable)

---

## 🔥 COMMANDES RAPIDES PRODUCTION

### Déploiement Backend
```bash
cd /var/www/css/backend
git pull origin main
composer install --optimize-autoloader --no-dev
php artisan migrate --force
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan queue:restart
sudo systemctl restart laravel-worker
```

### Déploiement Frontend
```bash
cd /var/www/css/frontend
git pull origin main
npm install
npm run build
sudo systemctl reload nginx
```

### Vider les caches
```bash
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear
redis-cli FLUSHALL
```

### Logs en temps réel
```bash
# Laravel
tail -f storage/logs/laravel.log

# Nginx
tail -f /var/log/nginx/error.log

# Queue worker
journalctl -u laravel-worker -f
```

---

## ⚠️ IMPORTANT - SÉCURITÉ

1. **Ne JAMAIS commiter le fichier `.env`**
2. **Toujours utiliser HTTPS en production**
3. **APP_DEBUG=false** en production
4. **Mots de passe forts** pour DB, Redis, Admin
5. **Backups quotidiens** testés régulièrement
6. **Monitoring actif** avec Sentry
7. **Rate limiting** activé sur API
8. **CORS** configuré correctement
9. **Firewall** actif avec ports minimaux
10. **Updates régulières** des dépendances

---

## 📞 SUPPORT

En cas de problème:
1. Vérifier les logs Laravel: `storage/logs/laravel.log`
2. Vérifier les logs Nginx: `/var/log/nginx/error.log`
3. Vérifier le status des services: `systemctl status`
4. Vérifier les permissions fichiers
5. Consulter QUICK_INSTALL.md pour troubleshooting détaillé

---

## ✅ CHECKLIST FINALE

Avant de lancer en production:
- [ ] Tous les tests passent
- [ ] Toutes les configurations vérifiées
- [ ] Backups configurés et testés
- [ ] SSL actif sur tous les domaines
- [ ] Monitoring et alertes actifs
- [ ] Documentation équipe à jour
- [ ] Plan de rollback préparé
- [ ] Équipe technique en alerte

**Une fois tout coché ci-dessus, vous êtes prêt pour la production! 🚀**
