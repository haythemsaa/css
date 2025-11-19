#!/bin/bash

##############################################################################
# Script de déploiement automatique - CSS Platform
# Ce script déploie automatiquement le projet en production
##############################################################################

set -e  # Arrête le script en cas d'erreur

# Couleurs
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
RED='\033[0;31m'
BLUE='\033[0;34m'
NC='\033[0m' # No Color

# Configuration
DEPLOY_ENV="${1:-production}"  # production, staging, ou development
PROJECT_DIR="$(pwd)"
BACKUP_DIR="$PROJECT_DIR/backups"
TIMESTAMP=$(date +"%Y%m%d_%H%M%S")

# Fonction pour afficher les messages
print_success() {
    echo -e "${GREEN}✅ $1${NC}"
}

print_info() {
    echo -e "${BLUE}ℹ️  $1${NC}"
}

print_warning() {
    echo -e "${YELLOW}⚠️  $1${NC}"
}

print_error() {
    echo -e "${RED}❌ $1${NC}"
}

print_header() {
    echo -e "\n${BLUE}━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━${NC}"
    echo -e "${GREEN}$1${NC}"
    echo -e "${BLUE}━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━${NC}\n"
}

# Vérifier l'environnement
check_environment() {
    print_header "🔍 Vérification de l'environnement"

    if [ "$DEPLOY_ENV" != "production" ] && [ "$DEPLOY_ENV" != "staging" ] && [ "$DEPLOY_ENV" != "development" ]; then
        print_error "Environnement invalide: $DEPLOY_ENV"
        echo "Usage: ./deploy.sh [production|staging|development]"
        exit 1
    fi

    print_info "Environnement de déploiement: $DEPLOY_ENV"

    # Vérifier Git
    if ! command -v git &> /dev/null; then
        print_error "Git n'est pas installé"
        exit 1
    fi

    # Vérifier Docker (si utilisé)
    if [ "$DEPLOY_ENV" == "production" ]; then
        if ! command -v docker &> /dev/null; then
            print_warning "Docker n'est pas installé (déploiement manuel requis)"
        fi
        if ! command -v docker-compose &> /dev/null; then
            print_warning "Docker Compose n'est pas installé"
        fi
    fi

    print_success "Environnement vérifié"
}

# Créer un backup
create_backup() {
    print_header "💾 Création du backup"

    mkdir -p "$BACKUP_DIR"

    # Backup de la base de données
    if [ -f "backend/database/database.sqlite" ]; then
        print_info "Backup SQLite..."
        cp backend/database/database.sqlite "$BACKUP_DIR/database_$TIMESTAMP.sqlite"
        print_success "Backup SQLite créé"
    fi

    # Backup des fichiers .env
    print_info "Backup des fichiers .env..."
    if [ -f "backend/.env" ]; then
        cp backend/.env "$BACKUP_DIR/backend_env_$TIMESTAMP"
    fi
    if [ -f "frontend/.env" ]; then
        cp frontend/.env "$BACKUP_DIR/frontend_env_$TIMESTAMP"
    fi

    print_success "Backup créé dans $BACKUP_DIR"
}

# Mettre en mode maintenance
enable_maintenance_mode() {
    if [ "$DEPLOY_ENV" == "production" ]; then
        print_header "🔧 Activation du mode maintenance"

        cd backend
        php artisan down --retry=60 --secret="css-deploy-secret-$TIMESTAMP"
        print_success "Mode maintenance activé"
        print_info "Secret pour bypass: css-deploy-secret-$TIMESTAMP"
        cd ..
    fi
}

# Désactiver le mode maintenance
disable_maintenance_mode() {
    if [ "$DEPLOY_ENV" == "production" ]; then
        print_header "✅ Désactivation du mode maintenance"

        cd backend
        php artisan up
        print_success "Mode maintenance désactivé"
        cd ..
    fi
}

# Récupérer les derniers changements
pull_changes() {
    print_header "📥 Récupération des changements Git"

    # Vérifier la branche actuelle
    CURRENT_BRANCH=$(git branch --show-current)
    print_info "Branche actuelle: $CURRENT_BRANCH"

    # Stash les changements locaux si nécessaire
    if ! git diff-index --quiet HEAD --; then
        print_warning "Changements locaux détectés, stash..."
        git stash save "Auto-stash avant deploy $TIMESTAMP"
    fi

    # Pull les changements
    print_info "Pull des changements..."
    git pull origin "$CURRENT_BRANCH"
    print_success "Changements récupérés"
}

# Installer les dépendances Backend
install_backend_dependencies() {
    print_header "📦 Installation dépendances Backend"

    cd backend

    # Composer
    print_info "Installation Composer (production)..."
    composer install --no-dev --optimize-autoloader --no-interaction --prefer-dist
    print_success "Composer installé"

    # NPM pour assets
    print_info "Installation NPM..."
    npm ci --production --silent
    print_success "NPM installé"

    cd ..
}

# Build Frontend
build_frontend() {
    print_header "🏗️  Build Frontend"

    cd frontend

    # Installer les dépendances
    print_info "Installation dépendances..."
    npm ci --silent

    # Build production
    print_info "Build production Vite..."
    npm run build
    print_success "Frontend build créé dans frontend/dist/"

    cd ..
}

# Optimiser Backend pour production
optimize_backend() {
    print_header "⚡ Optimisation Backend"

    cd backend

    # Cache de configuration
    print_info "Cache de configuration..."
    php artisan config:cache
    print_success "Config cached"

    # Cache des routes
    print_info "Cache des routes..."
    php artisan route:cache
    print_success "Routes cached"

    # Cache des views
    print_info "Cache des vues..."
    php artisan view:cache
    print_success "Views cached"

    # Cache des events
    print_info "Cache des events..."
    php artisan event:cache 2>/dev/null || true
    print_success "Events cached"

    cd ..
}

# Exécuter les migrations
run_migrations() {
    print_header "🗄️  Exécution des migrations"

    cd backend

    # Vérifier s'il y a des migrations en attente
    if php artisan migrate:status | grep -q "Pending"; then
        print_warning "Migrations en attente détectées"

        if [ "$DEPLOY_ENV" == "production" ]; then
            read -p "Exécuter les migrations en production? (y/n) " -n 1 -r
            echo
            if [[ $REPLY =~ ^[Yy]$ ]]; then
                php artisan migrate --force
                print_success "Migrations exécutées"
            else
                print_warning "Migrations ignorées"
            fi
        else
            php artisan migrate --force
            print_success "Migrations exécutées"
        fi
    else
        print_info "Aucune migration en attente"
    fi

    cd ..
}

# Redémarrer les services
restart_services() {
    print_header "🔄 Redémarrage des services"

    if [ "$DEPLOY_ENV" == "production" ]; then
        # Avec Docker
        if command -v docker-compose &> /dev/null; then
            print_info "Redémarrage Docker Compose..."
            docker-compose -f docker-compose.prod.yml restart backend queue horizon
            print_success "Services Docker redémarrés"
        fi

        # Queue workers
        print_info "Redémarrage des queue workers..."
        cd backend
        php artisan queue:restart
        print_success "Queue workers redémarrés"
        cd ..

        # Horizon
        print_info "Redémarrage de Horizon..."
        cd backend
        php artisan horizon:terminate 2>/dev/null || true
        print_success "Horizon redémarré"
        cd ..
    fi
}

# Nettoyer les caches
clear_caches() {
    print_header "🧹 Nettoyage des caches"

    cd backend

    # Cache applicatif
    print_info "Nettoyage cache applicatif..."
    php artisan cache:clear
    print_success "Cache applicatif nettoyé"

    # Cache des permissions (Spatie)
    print_info "Nettoyage cache permissions..."
    php artisan permission:cache-reset 2>/dev/null || true
    print_success "Cache permissions nettoyé"

    cd ..
}

# Vérifier la santé de l'application
health_check() {
    print_header "🏥 Vérification de santé"

    # Vérifier l'API
    print_info "Test de l'API..."

    if [ "$DEPLOY_ENV" == "production" ]; then
        API_URL="https://api.css.tn"
    else
        API_URL="http://localhost:8000"
    fi

    # Test simple
    if curl -f -s "$API_URL/api/v1/health" > /dev/null 2>&1; then
        print_success "API opérationnelle"
    else
        print_warning "API non accessible (normal si pas encore démarrée)"
    fi

    # Vérifier la base de données
    print_info "Test connexion base de données..."
    cd backend
    if php artisan db:show 2>/dev/null; then
        print_success "Base de données accessible"
    else
        print_warning "Impossible de vérifier la base de données"
    fi
    cd ..
}

# Afficher le résumé
show_summary() {
    print_header "🎉 Déploiement terminé!"

    echo -e "${GREEN}Résumé du déploiement:${NC}"
    echo -e "  Environnement: ${YELLOW}$DEPLOY_ENV${NC}"
    echo -e "  Timestamp: ${YELLOW}$TIMESTAMP${NC}"
    echo -e "  Branche: ${YELLOW}$(git branch --show-current)${NC}"
    echo -e "  Commit: ${YELLOW}$(git rev-parse --short HEAD)${NC}"
    echo ""

    echo -e "${BLUE}Backups créés:${NC}"
    echo -e "  📁 $BACKUP_DIR"
    echo ""

    if [ "$DEPLOY_ENV" == "production" ]; then
        echo -e "${YELLOW}URLs Production:${NC}"
        echo -e "  Frontend: https://css.tn"
        echo -e "  API: https://api.css.tn"
        echo -e "  Admin: https://api.css.tn/admin"
    else
        echo -e "${YELLOW}URLs Développement:${NC}"
        echo -e "  Frontend: http://localhost:5173"
        echo -e "  API: http://localhost:8000/api/v1"
        echo -e "  Admin: http://localhost:8000/admin"
    fi
    echo ""

    echo -e "${GREEN}Prochaines étapes:${NC}"
    echo "  1. Vérifier les logs: tail -f backend/storage/logs/laravel.log"
    echo "  2. Tester les fonctionnalités critiques"
    echo "  3. Monitorer les performances"
    echo ""
}

# Rollback en cas d'erreur
rollback() {
    print_error "Erreur détectée pendant le déploiement!"
    print_warning "Exécution du rollback..."

    # Restaurer les .env
    if [ -f "$BACKUP_DIR/backend_env_$TIMESTAMP" ]; then
        cp "$BACKUP_DIR/backend_env_$TIMESTAMP" backend/.env
        print_info "Fichier backend/.env restauré"
    fi

    # Désactiver le mode maintenance
    disable_maintenance_mode

    print_error "Déploiement annulé. Vérifiez les logs."
    exit 1
}

# Trap des erreurs pour rollback automatique
trap rollback ERR

# Menu principal
main() {
    clear
    echo -e "${GREEN}"
    cat << "EOF"
   _____ _____ _____   _____             _
  / ____/ ____/ ____| |  __ \           | |
 | |   | (___| (___   | |  | | ___ _ __ | | ___  _   _
 | |    \___ \\___ \  | |  | |/ _ \ '_ \| |/ _ \| | | |
 | |____) |___) |___) | |__| |  __/ |_) | | (_) | |_| |
  \_____|_____/_____/ |_____/ \___| .__/|_|\___/ \__, |
                                  | |             __/ |
                                  |_|            |___/
EOF
    echo -e "${NC}\n"

    print_info "Déploiement CSS Platform - Environnement: $DEPLOY_ENV"
    echo ""

    if [ "$DEPLOY_ENV" == "production" ]; then
        print_warning "⚠️  ATTENTION: Déploiement en PRODUCTION!"
        read -p "Continuer? (y/n) " -n 1 -r
        echo
        if [[ ! $REPLY =~ ^[Yy]$ ]]; then
            print_warning "Déploiement annulé"
            exit 0
        fi
    fi

    echo ""

    # Exécution des étapes
    check_environment
    create_backup
    enable_maintenance_mode
    pull_changes
    install_backend_dependencies
    build_frontend
    optimize_backend
    run_migrations
    clear_caches
    restart_services
    disable_maintenance_mode
    health_check
    show_summary
}

# Lancement du script
main "$@"
