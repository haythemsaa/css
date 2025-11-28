#!/bin/bash

# CSS Socios - Production Deployment Script
# This script automates the deployment process

set -e  # Exit on any error

echo "🚀 CSS Socios - Déploiement Production"
echo "======================================="

# Colors for output
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
NC='\033[0m' # No Color

# Configuration
PROJECT_DIR="/var/www/css"
BACKEND_DIR="$PROJECT_DIR/backend"
WEB_DIR="$PROJECT_DIR/web"
BACKUP_DIR="/var/backups/css"

# Function to print colored messages
print_success() {
    echo -e "${GREEN}✓ $1${NC}"
}

print_error() {
    echo -e "${RED}✗ $1${NC}"
}

print_warning() {
    echo -e "${YELLOW}⚠ $1${NC}"
}

# Check if running as root
if [ "$EUID" -ne 0 ]; then
    print_error "Ce script doit être exécuté en tant que root"
    exit 1
fi

# Step 1: Enable maintenance mode
echo ""
echo "📝 Étape 1: Activation du mode maintenance..."
cd "$BACKEND_DIR"
php artisan down --message="Mise à jour en cours..." --retry=60 || true
print_success "Mode maintenance activé"

# Step 2: Backup database
echo ""
echo "💾 Étape 2: Sauvegarde de la base de données..."
TIMESTAMP=$(date +"%Y%m%d_%H%M%S")
mkdir -p "$BACKUP_DIR"
mysqldump -u root css_database > "$BACKUP_DIR/db_backup_$TIMESTAMP.sql"
print_success "Base de données sauvegardée: $BACKUP_DIR/db_backup_$TIMESTAMP.sql"

# Step 3: Pull latest code
echo ""
echo "📥 Étape 3: Récupération du code depuis Git..."
cd "$PROJECT_DIR"
git fetch origin
git pull origin main
print_success "Code mis à jour"

# Step 4: Backend deployment
echo ""
echo "⚙️  Étape 4: Déploiement Backend (Laravel)..."
cd "$BACKEND_DIR"

# Install dependencies (production only)
composer install --no-dev --optimize-autoloader --no-interaction
print_success "Dépendances Composer installées"

# Run migrations
php artisan migrate --force
print_success "Migrations exécutées"

# Clear and cache configs
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan event:cache
print_success "Caches optimisés"

# Step 5: Frontend deployment
echo ""
echo "🎨 Étape 5: Déploiement Frontend (React)..."
cd "$WEB_DIR"

# Install dependencies
npm ci --production
print_success "Dépendances npm installées"

# Build for production
npm run build
print_success "Build frontend créé"

# Set correct permissions
chown -R www-data:www-data "$WEB_DIR/dist"
print_success "Permissions configurées"

# Step 6: Restart services
echo ""
echo "🔄 Étape 6: Redémarrage des services..."

# Restart PHP-FPM
systemctl restart php8.2-fpm
print_success "PHP-FPM redémarré"

# Restart queue workers
if systemctl is-active --quiet laravel-worker; then
    systemctl restart laravel-worker
    print_success "Laravel Worker redémarré"
fi

if systemctl is-active --quiet laravel-horizon; then
    php artisan horizon:terminate
    systemctl restart laravel-horizon
    print_success "Laravel Horizon redémarré"
fi

# Reload Nginx
nginx -t && systemctl reload nginx
print_success "Nginx rechargé"

# Step 7: Clear caches
echo ""
echo "🧹 Étape 7: Nettoyage des caches..."
cd "$BACKEND_DIR"
php artisan cache:clear
php artisan queue:restart
print_success "Caches nettoyés"

# Step 8: Disable maintenance mode
echo ""
echo "✅ Étape 8: Désactivation du mode maintenance..."
php artisan up
print_success "Mode maintenance désactivé"

# Final verification
echo ""
echo "🔍 Vérification finale..."
if curl -f -s https://api.css-sfax.tn/api/v1/health > /dev/null; then
    print_success "API opérationnelle"
else
    print_warning "API health check échoué - vérifier les logs"
fi

echo ""
echo "======================================="
echo -e "${GREEN}✓ Déploiement terminé avec succès!${NC}"
echo "======================================="
echo ""
echo "Backup: $BACKUP_DIR/db_backup_$TIMESTAMP.sql"
echo ""
echo "Logs:"
echo "  - Laravel: $BACKEND_DIR/storage/logs/laravel.log"
echo "  - Nginx: /var/log/nginx/css-api-error.log"
echo "  - Queue: journalctl -u laravel-worker -f"
echo ""
