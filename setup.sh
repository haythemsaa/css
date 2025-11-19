#!/bin/bash

##############################################################################
# Script d'installation automatique - CSS Platform
# Ce script configure le projet complet en une seule commande
##############################################################################

set -e  # Arrête le script en cas d'erreur

# Couleurs
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
RED='\033[0;31m'
BLUE='\033[0;34m'
NC='\033[0m' # No Color

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

# Vérification des prérequis
check_prerequisites() {
    print_header "🔍 Vérification des prérequis"

    local missing_deps=()

    # Vérifier PHP
    if ! command -v php &> /dev/null; then
        print_error "PHP n'est pas installé"
        missing_deps+=("PHP 8.4+")
    else
        PHP_VERSION=$(php -r "echo PHP_VERSION;" | cut -d'.' -f1,2)
        print_info "PHP $PHP_VERSION détecté"
        if (( $(echo "$PHP_VERSION < 8.4" | bc -l) )); then
            print_warning "PHP 8.4+ est recommandé (version actuelle: $PHP_VERSION)"
        fi
    fi

    # Vérifier Composer
    if ! command -v composer &> /dev/null; then
        print_error "Composer n'est pas installé"
        missing_deps+=("Composer 2.x")
    else
        print_info "Composer $(composer --version | cut -d' ' -f3) détecté"
    fi

    # Vérifier Node.js
    if ! command -v node &> /dev/null; then
        print_error "Node.js n'est pas installé"
        missing_deps+=("Node.js 18+")
    else
        NODE_VERSION=$(node --version | cut -d'v' -f2 | cut -d'.' -f1)
        print_info "Node.js v$(node --version | cut -d'v' -f2) détecté"
        if (( NODE_VERSION < 18 )); then
            print_warning "Node.js 18+ est recommandé (version actuelle: $NODE_VERSION)"
        fi
    fi

    # Vérifier NPM
    if ! command -v npm &> /dev/null; then
        print_error "NPM n'est pas installé"
        missing_deps+=("NPM")
    else
        print_info "NPM $(npm --version) détecté"
    fi

    # Vérifier Git
    if ! command -v git &> /dev/null; then
        print_warning "Git n'est pas installé (optionnel)"
    else
        print_info "Git $(git --version | cut -d' ' -f3) détecté"
    fi

    # Si des dépendances manquent, afficher l'erreur et quitter
    if [ ${#missing_deps[@]} -gt 0 ]; then
        print_error "Dépendances manquantes:"
        for dep in "${missing_deps[@]}"; do
            echo "  - $dep"
        done
        echo ""
        print_info "Installez les dépendances manquantes et relancez ce script"
        exit 1
    fi

    print_success "Tous les prérequis sont satisfaits!\n"
}

# Installation Backend
install_backend() {
    print_header "📦 Installation Backend (Laravel)"

    cd backend

    # Installation Composer
    print_info "Installation des dépendances Composer..."
    composer install --optimize-autoloader --no-interaction
    print_success "Dépendances Composer installées"

    # Installation NPM pour assets
    print_info "Installation des dépendances NPM..."
    npm install --silent
    print_success "Dépendances NPM installées"

    cd ..
}

# Configuration Backend
setup_backend() {
    print_header "🔧 Configuration Backend"

    cd backend

    # Copie .env
    if [ ! -f .env ]; then
        print_info "Création du fichier .env..."
        cp .env.example .env
        print_success "Fichier .env créé"
    else
        print_warning "Fichier .env existe déjà (non écrasé)"
    fi

    # Génération clé application
    print_info "Génération de la clé d'application..."
    php artisan key:generate --no-interaction
    print_success "Clé d'application générée"

    # Création base SQLite
    if [ ! -f database/database.sqlite ]; then
        print_info "Création de la base de données SQLite..."
        touch database/database.sqlite
        print_success "Base SQLite créée"
    else
        print_warning "Base SQLite existe déjà"
    fi

    # Storage link
    print_info "Création du lien symbolique storage..."
    php artisan storage:link --no-interaction 2>/dev/null || true
    print_success "Lien storage créé"

    # Migrations et seeders
    print_info "Exécution des migrations et seeders (cela peut prendre 1-2 minutes)..."
    php artisan migrate:fresh --seed --force --no-interaction
    print_success "Base de données initialisée avec données de test"

    print_info "📊 Données créées:"
    echo "   • 102 utilisateurs (Free, Premium, Socios)"
    echo "   • 29 partenaires CSS Privilèges"
    echo "   • 64 offres actives"
    echo "   • 23 joueurs"
    echo "   • 20 matchs"
    echo "   • 40 contenus"
    echo "   • 653 cartes collectibles"

    cd ..
}

# Installation Frontend
install_frontend() {
    print_header "📦 Installation Frontend (React)"

    cd frontend

    print_info "Installation des dépendances NPM..."
    npm install --silent
    print_success "Dépendances Frontend installées"

    cd ..
}

# Configuration Frontend
setup_frontend() {
    print_header "🔧 Configuration Frontend"

    cd frontend

    if [ ! -f .env ]; then
        print_info "Création du fichier .env..."
        cp .env.example .env
        print_success "Fichier .env créé"
    else
        print_warning "Fichier .env existe déjà (non écrasé)"
    fi

    cd ..
}

# Installation Mobile
install_mobile() {
    print_header "📦 Installation Mobile (React Native)"

    cd mobile

    print_info "Installation des dépendances NPM..."
    npm install --silent
    print_success "Dépendances Mobile installées"

    cd ..
}

# Affichage des informations finales
show_final_info() {
    print_header "🎉 Installation terminée avec succès!"

    echo -e "${GREEN}Le projet CSS est prêt à être utilisé!${NC}\n"

    echo -e "${BLUE}━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━${NC}"
    echo -e "${YELLOW}📍 INFORMATIONS IMPORTANTES${NC}"
    echo -e "${BLUE}━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━${NC}\n"

    echo -e "${GREEN}🚀 Pour démarrer le projet:${NC}"
    echo "   make dev                # Lance Backend + Frontend"
    echo "   make dev-mobile         # Lance l'app Mobile"
    echo ""

    echo -e "${GREEN}🔗 URLs d'accès:${NC}"
    echo "   Frontend:     http://localhost:5173"
    echo "   Backend API:  http://localhost:8000/api/v1"
    echo "   Admin Panel:  http://localhost:8000/admin"
    echo ""

    echo -e "${GREEN}👤 Compte Admin par défaut:${NC}"
    echo "   Email:        admin@css.tn"
    echo "   Password:     password"
    echo ""

    echo -e "${GREEN}📚 Documentation:${NC}"
    echo "   README.md            - Vue d'ensemble"
    echo "   QUICKSTART.md        - Guide rapide"
    echo "   API_DOCUMENTATION.md - Documentation API"
    echo "   DEPLOYMENT.md        - Guide de déploiement"
    echo ""

    echo -e "${GREEN}🛠️  Commandes utiles:${NC}"
    echo "   make help            - Voir toutes les commandes"
    echo "   make test            - Exécuter tous les tests"
    echo "   make docker-up       - Lancer avec Docker"
    echo "   make fresh-start     - Réinstaller complètement"
    echo ""

    echo -e "${BLUE}━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━${NC}\n"

    echo -e "${YELLOW}⚡ Conseil: Lancez 'make dev' dans un terminal pour démarrer!${NC}\n"
}

# Menu principal
main() {
    clear
    echo -e "${GREEN}"
    cat << "EOF"
   _____ _____ _____   _____  _       _    __
  / ____/ ____/ ____| |  __ \| |     | |  / _|
 | |   | (___| (___   | |__) | | __ _| |_| |_ ___  _ __ _ __ ___
 | |    \___ \\___ \  |  ___/| |/ _` | __|  _/ _ \| '__| '_ ` _ \
 | |________) |___) | | |    | | (_| | |_| || (_) | |  | | | | | |
  \_____|_____/_____/  |_|    |_|\__,_|\__|_| \___/|_|  |_| |_| |_|

  Setup Automatique - Club Sportif Sfaxien
EOF
    echo -e "${NC}\n"

    print_info "Ce script va installer et configurer le projet CSS complet"
    print_info "Composants: Backend (Laravel) + Frontend (React) + Mobile (React Native)\n"

    read -p "Continuer l'installation? (y/n) " -n 1 -r
    echo
    if [[ ! $REPLY =~ ^[Yy]$ ]]; then
        print_warning "Installation annulée"
        exit 0
    fi

    echo ""

    # Exécution des étapes
    check_prerequisites
    install_backend
    setup_backend
    install_frontend
    setup_frontend
    install_mobile
    show_final_info
}

# Lancement du script
main "$@"
