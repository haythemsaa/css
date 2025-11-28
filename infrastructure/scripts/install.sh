#!/bin/bash

# CSS Socios - Automated Production Installation
# This script automates the entire production setup process

set -e

echo "🚀 CSS Socios - Installation Production Automatisée"
echo "===================================================="
echo ""

# Colors
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
NC='\033[0m'

# Functions
print_success() { echo -e "${GREEN}✓ $1${NC}"; }
print_error() { echo -e "${RED}✗ $1${NC}"; }
print_warning() { echo -e "${YELLOW}⚠ $1${NC}"; }
print_info() { echo -e "${BLUE}ℹ $1${NC}"; }

# Check if running as root
if [ "$EUID" -ne 0 ]; then
    print_error "Ce script doit être exécuté en tant que root (sudo)"
    exit 1
fi

# Configuration
DOMAIN_API="api.css-sfax.tn"
DOMAIN_WEB="app.css-sfax.tn"
EMAIL="admin@css-sfax.tn"  # For Let's Encrypt
PROJECT_DIR="/var/www/css"
DB_NAME="css_database"
DB_USER="css_user"
DB_PASSWORD=$(openssl rand -base64 32)  # Generate random password

echo "Configuration:"
echo "  - API Domain: $DOMAIN_API"
echo "  - Web Domain: $DOMAIN_WEB"
echo "  - Project Dir: $PROJECT_DIR"
echo "  - Database: $DB_NAME"
echo ""
read -p "Continuer avec cette configuration? (y/n) " -n 1 -r
echo ""
if [[ ! $REPLY =~ ^[Yy]$ ]]; then
    exit 1
fi

# Step 1: System Update
echo ""
echo "📦 Étape 1/12: Mise à jour du système..."
apt-get update -qq
apt-get upgrade -y -qq
print_success "Système mis à jour"

# Step 2: Install required packages
echo ""
echo "📦 Étape 2/12: Installation des paquets..."
apt-get install -y -qq \
    nginx \
    mysql-server \
    redis-server \
    php8.2-fpm php8.2-cli php8.2-mysql php8.2-redis php8.2-xml php8.2-mbstring \
    php8.2-curl php8.2-zip php8.2-gd php8.2-intl php8.2-bcmath \
    git curl unzip supervisor certbot python3-certbot-nginx \
    ufw fail2ban
print_success "Paquets installés"

# Step 3: Install Composer
echo ""
echo "📦 Étape 3/12: Installation de Composer..."
if [ ! -f /usr/local/bin/composer ]; then
    curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer
    print_success "Composer installé"
else
    print_info "Composer déjà installé"
fi

# Step 4: Install Node.js & npm
echo ""
echo "📦 Étape 4/12: Installation de Node.js..."
if ! command -v node &> /dev/null; then
    curl -fsSL https://deb.nodesource.com/setup_20.x | bash -
    apt-get install -y nodejs
    print_success "Node.js installé"
else
    print_info "Node.js déjà installé"
fi

# Step 5: Configure MySQL
echo ""
echo "🗄️  Étape 5/12: Configuration MySQL..."
mysql -e "CREATE DATABASE IF NOT EXISTS $DB_NAME CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"
mysql -e "CREATE USER IF NOT EXISTS '$DB_USER'@'localhost' IDENTIFIED BY '$DB_PASSWORD';"
mysql -e "GRANT ALL PRIVILEGES ON $DB_NAME.* TO '$DB_USER'@'localhost';"
mysql -e "FLUSH PRIVILEGES;"
print_success "Base de données configurée"

# Save database credentials
echo "DB_NAME=$DB_NAME" > /root/.css_db_credentials
echo "DB_USER=$DB_USER" >> /root/.css_db_credentials
echo "DB_PASSWORD=$DB_PASSWORD" >> /root/.css_db_credentials
chmod 600 /root/.css_db_credentials

# Step 6: Configure Redis
echo ""
echo "💾 Étape 6/12: Configuration Redis..."
REDIS_PASSWORD=$(openssl rand -base64 32)
sed -i "s/# requirepass .*/requirepass $REDIS_PASSWORD/" /etc/redis/redis.conf
systemctl restart redis-server
print_success "Redis configuré"

# Step 7: Clone project
echo ""
echo "📥 Étape 7/12: Clone du projet..."
if [ ! -d "$PROJECT_DIR" ]; then
    git clone https://github.com/votre-repo/css.git "$PROJECT_DIR"
    print_success "Projet cloné"
else
    print_info "Projet déjà cloné"
fi

# Step 8: Backend setup
echo ""
echo "⚙️  Étape 8/12: Configuration Backend..."
cd "$PROJECT_DIR/backend"

# Install dependencies
composer install --no-dev --optimize-autoloader

# Setup .env
cp .env.example .env
sed -i "s/APP_ENV=.*/APP_ENV=production/" .env
sed -i "s/APP_DEBUG=.*/APP_DEBUG=false/" .env
sed -i "s/APP_URL=.*/APP_URL=https:\/\/$DOMAIN_API/" .env
sed -i "s/DB_DATABASE=.*/DB_DATABASE=$DB_NAME/" .env
sed -i "s/DB_USERNAME=.*/DB_USERNAME=$DB_USER/" .env
sed -i "s/DB_PASSWORD=.*/DB_PASSWORD=$DB_PASSWORD/" .env
sed -i "s/REDIS_PASSWORD=.*/REDIS_PASSWORD=$REDIS_PASSWORD/" .env

# Generate app key
php artisan key:generate --force

# Run migrations
php artisan migrate --force

# Run essential seeders
php artisan db:seed --class=AdminUserSeeder --force
php artisan db:seed --class=TeamSeeder --force
php artisan db:seed --class=BadgeSeeder --force
php artisan db:seed --class=PaymentMethodSeeder --force

# Optimize
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Set permissions
chown -R www-data:www-data "$PROJECT_DIR/backend/storage"
chown -R www-data:www-data "$PROJECT_DIR/backend/bootstrap/cache"
chmod -R 775 "$PROJECT_DIR/backend/storage"
chmod -R 775 "$PROJECT_DIR/backend/bootstrap/cache"

print_success "Backend configuré"

# Step 9: Frontend setup
echo ""
echo "🎨 Étape 9/12: Configuration Frontend..."
cd "$PROJECT_DIR/web"
npm ci --production
npm run build
chown -R www-data:www-data dist
print_success "Frontend configuré"

# Step 10: Nginx configuration
echo ""
echo "🌐 Étape 10/12: Configuration Nginx..."
cp "$PROJECT_DIR/infrastructure/nginx/api.$DOMAIN_API.conf" "/etc/nginx/sites-available/api.$DOMAIN_API"
cp "$PROJECT_DIR/infrastructure/nginx/app.$DOMAIN_WEB.conf" "/etc/nginx/sites-available/app.$DOMAIN_WEB"

# Update domain names in configs if different
sed -i "s/api.css-sfax.tn/$DOMAIN_API/g" "/etc/nginx/sites-available/api.$DOMAIN_API"
sed -i "s/app.css-sfax.tn/$DOMAIN_WEB/g" "/etc/nginx/sites-available/app.$DOMAIN_WEB"

# Enable sites
ln -sf "/etc/nginx/sites-available/api.$DOMAIN_API" /etc/nginx/sites-enabled/
ln -sf "/etc/nginx/sites-available/app.$DOMAIN_WEB" /etc/nginx/sites-enabled/

# Remove default site
rm -f /etc/nginx/sites-enabled/default

# Test and reload
nginx -t
systemctl reload nginx
print_success "Nginx configuré"

# Step 11: SSL Certificates
echo ""
echo "🔒 Étape 11/12: Configuration SSL (Let's Encrypt)..."
certbot --nginx -d "$DOMAIN_API" -d "$DOMAIN_WEB" --non-interactive --agree-tos -m "$EMAIL"
print_success "SSL configuré"

# Step 12: Systemd services
echo ""
echo "⚙️  Étape 12/12: Configuration des services..."
cp "$PROJECT_DIR/infrastructure/systemd/laravel-worker.service" /etc/systemd/system/
systemctl daemon-reload
systemctl enable laravel-worker
systemctl start laravel-worker
print_success "Services configurés"

# Setup cron
echo "* * * * * www-data cd $PROJECT_DIR/backend && php artisan schedule:run >> /dev/null 2>&1" > /etc/cron.d/laravel-scheduler
print_success "Cron configuré"

# Setup firewall
echo ""
echo "🔥 Configuration du firewall..."
ufw --force enable
ufw allow 22
ufw allow 80
ufw allow 443
print_success "Firewall configuré"

# Final message
echo ""
echo "===================================================="
echo -e "${GREEN}✅ Installation terminée avec succès!${NC}"
echo "===================================================="
echo ""
echo "📝 Informations importantes:"
echo ""
echo "API URL: https://$DOMAIN_API"
echo "Web URL: https://$DOMAIN_WEB"
echo ""
echo "Base de données:"
echo "  - Nom: $DB_NAME"
echo "  - Utilisateur: $DB_USER"
echo "  - Mot de passe: (sauvegardé dans /root/.css_db_credentials)"
echo ""
echo "Admin par défaut (créé par AdminUserSeeder):"
echo "  - Email: admin@css.tn"
echo "  - Mot de passe: (vérifier le seeder)"
echo ""
echo "Prochaines étapes:"
echo "  1. Configurer les services externes (.env):"
echo "     - Services de paiement (Stripe, D17, Konnect)"
echo "     - Email (SMTP)"
echo "     - SMS Gateway"
echo "     - AWS S3 pour le stockage"
echo "     - Firebase pour les notifications"
echo "  2. Tester l'application"
echo "  3. Configurer les backups automatiques"
echo ""
echo "Scripts utiles:"
echo "  - Déploiement: $PROJECT_DIR/infrastructure/scripts/deploy.sh"
echo "  - Backup: $PROJECT_DIR/infrastructure/scripts/backup.sh"
echo ""
