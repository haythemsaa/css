#!/bin/bash

##############################################################################
# Script de vérification de santé - CSS Platform
# Vérifie tous les services et composants du système
##############################################################################

set -e

# Couleurs
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
RED='\033[0;31m'
BLUE='\033[0;34m'
NC='\033[0m'

# Configuration
ENV="${1:-development}"
BACKEND_URL="${BACKEND_URL:-http://localhost:8000}"
FRONTEND_URL="${FRONTEND_URL:-http://localhost:5173}"
TIMEOUT=10

# Compteurs
TOTAL_CHECKS=0
PASSED_CHECKS=0
FAILED_CHECKS=0
WARNING_CHECKS=0

# Fonction pour afficher les messages
print_success() {
    echo -e "${GREEN}✅ $1${NC}"
    ((PASSED_CHECKS++))
}

print_info() {
    echo -e "${BLUE}ℹ️  $1${NC}"
}

print_warning() {
    echo -e "${YELLOW}⚠️  $1${NC}"
    ((WARNING_CHECKS++))
}

print_error() {
    echo -e "${RED}❌ $1${NC}"
    ((FAILED_CHECKS++))
}

print_header() {
    echo -e "\n${BLUE}━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━${NC}"
    echo -e "${GREEN}$1${NC}"
    echo -e "${BLUE}━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━${NC}\n"
}

increment_total() {
    ((TOTAL_CHECKS++))
}

# Vérifier la connectivité réseau
check_network() {
    print_header "🌐 Vérification réseau"

    increment_total
    if ping -c 1 google.com &> /dev/null; then
        print_success "Connexion Internet disponible"
    else
        print_error "Pas de connexion Internet"
    fi

    increment_total
    if ping -c 1 localhost &> /dev/null; then
        print_success "Localhost accessible"
    else
        print_error "Localhost non accessible"
    fi
}

# Vérifier les services système
check_system_services() {
    print_header "🖥️  Services système"

    # PHP
    increment_total
    if command -v php &> /dev/null; then
        PHP_VERSION=$(php -r "echo PHP_VERSION;")
        print_success "PHP $PHP_VERSION installé"
    else
        print_error "PHP non installé"
    fi

    # Composer
    increment_total
    if command -v composer &> /dev/null; then
        print_success "Composer installé"
    else
        print_warning "Composer non installé"
    fi

    # Node.js
    increment_total
    if command -v node &> /dev/null; then
        NODE_VERSION=$(node --version)
        print_success "Node.js $NODE_VERSION installé"
    else
        print_warning "Node.js non installé"
    fi

    # NPM
    increment_total
    if command -v npm &> /dev/null; then
        NPM_VERSION=$(npm --version)
        print_success "NPM $NPM_VERSION installé"
    else
        print_warning "NPM non installé"
    fi

    # Docker (si utilisé)
    increment_total
    if command -v docker &> /dev/null; then
        print_success "Docker installé"
    else
        print_warning "Docker non installé (optionnel)"
    fi
}

# Vérifier la base de données
check_database() {
    print_header "🗄️  Base de données"

    cd backend

    # Test connexion
    increment_total
    if php artisan db:show &> /dev/null; then
        print_success "Connexion base de données OK"
    else
        print_error "Impossible de se connecter à la base de données"
    fi

    # Vérifier les migrations
    increment_total
    if php artisan migrate:status &> /dev/null; then
        PENDING=$(php artisan migrate:status | grep -c "Pending" || echo "0")
        if [ "$PENDING" -eq 0 ]; then
            print_success "Toutes les migrations sont à jour"
        else
            print_warning "$PENDING migration(s) en attente"
        fi
    else
        print_error "Impossible de vérifier les migrations"
    fi

    cd ..
}

# Vérifier l'API Backend
check_backend_api() {
    print_header "🔌 API Backend"

    # Health endpoint
    increment_total
    if curl -f -s -m $TIMEOUT "$BACKEND_URL/api/v1/health" > /dev/null 2>&1; then
        print_success "Endpoint /health accessible"
    else
        print_error "Endpoint /health non accessible"
    fi

    # Test endpoint public
    increment_total
    if curl -f -s -m $TIMEOUT "$BACKEND_URL/api/v1/partners" > /dev/null 2>&1; then
        print_success "Endpoint /partners accessible"
    else
        print_error "Endpoint /partners non accessible"
    fi

    # Test endpoint auth
    increment_total
    RESPONSE=$(curl -s -w "%{http_code}" -m $TIMEOUT "$BACKEND_URL/api/v1/auth/user" -o /dev/null)
    if [ "$RESPONSE" == "401" ]; then
        print_success "Endpoint /auth/user protégé (401 attendu)"
    else
        print_warning "Endpoint /auth/user retourne $RESPONSE (attendu: 401)"
    fi

    # Temps de réponse
    increment_total
    START_TIME=$(date +%s%3N)
    curl -f -s -m $TIMEOUT "$BACKEND_URL/api/v1/health" > /dev/null 2>&1
    END_TIME=$(date +%s%3N)
    RESPONSE_TIME=$((END_TIME - START_TIME))

    if [ $RESPONSE_TIME -lt 500 ]; then
        print_success "Temps de réponse API: ${RESPONSE_TIME}ms (excellent)"
    elif [ $RESPONSE_TIME -lt 1000 ]; then
        print_success "Temps de réponse API: ${RESPONSE_TIME}ms (bon)"
    else
        print_warning "Temps de réponse API: ${RESPONSE_TIME}ms (lent)"
    fi
}

# Vérifier le Frontend
check_frontend() {
    print_header "🎨 Frontend"

    increment_total
    if curl -f -s -m $TIMEOUT "$FRONTEND_URL" > /dev/null 2>&1; then
        print_success "Frontend accessible"
    else
        print_error "Frontend non accessible"
    fi

    increment_total
    if [ -d "frontend/dist" ]; then
        print_success "Build frontend existe (dist/)"
    else
        print_warning "Pas de build frontend (dist/ manquant)"
    fi
}

# Vérifier les fichiers de configuration
check_configuration() {
    print_header "⚙️  Configuration"

    # Backend .env
    increment_total
    if [ -f "backend/.env" ]; then
        print_success "Fichier backend/.env existe"

        # Vérifier APP_KEY
        increment_total
        if grep -q "APP_KEY=base64:" backend/.env; then
            print_success "APP_KEY définie"
        else
            print_error "APP_KEY manquante ou invalide"
        fi
    else
        print_error "Fichier backend/.env manquant"
    fi

    # Frontend .env
    increment_total
    if [ -f "frontend/.env" ]; then
        print_success "Fichier frontend/.env existe"
    else
        print_warning "Fichier frontend/.env manquant"
    fi

    # Storage link
    increment_total
    if [ -L "backend/public/storage" ]; then
        print_success "Lien symbolique storage créé"
    else
        print_warning "Lien symbolique storage manquant"
    fi
}

# Vérifier les permissions
check_permissions() {
    print_header "🔐 Permissions"

    # Backend storage
    increment_total
    if [ -w "backend/storage" ]; then
        print_success "Dossier backend/storage accessible en écriture"
    else
        print_error "Dossier backend/storage non accessible en écriture"
    fi

    # Backend bootstrap/cache
    increment_total
    if [ -w "backend/bootstrap/cache" ]; then
        print_success "Dossier backend/bootstrap/cache accessible en écriture"
    else
        print_error "Dossier backend/bootstrap/cache non accessible en écriture"
    fi

    # Database SQLite
    increment_total
    if [ -f "backend/database/database.sqlite" ]; then
        if [ -w "backend/database/database.sqlite" ]; then
            print_success "Base SQLite accessible en écriture"
        else
            print_error "Base SQLite non accessible en écriture"
        fi
    else
        print_info "Base SQLite non trouvée (normal si MySQL)"
    fi
}

# Vérifier les dépendances
check_dependencies() {
    print_header "📦 Dépendances"

    # Composer
    increment_total
    if [ -d "backend/vendor" ]; then
        print_success "Dépendances Composer installées"
    else
        print_error "Dépendances Composer manquantes"
    fi

    # Frontend node_modules
    increment_total
    if [ -d "frontend/node_modules" ]; then
        print_success "Dépendances Frontend NPM installées"
    else
        print_warning "Dépendances Frontend NPM manquantes"
    fi

    # Mobile node_modules
    increment_total
    if [ -d "mobile/node_modules" ]; then
        print_success "Dépendances Mobile NPM installées"
    else
        print_warning "Dépendances Mobile NPM manquantes"
    fi
}

# Vérifier les logs
check_logs() {
    print_header "📝 Logs"

    increment_total
    if [ -f "backend/storage/logs/laravel.log" ]; then
        LOG_SIZE=$(du -h backend/storage/logs/laravel.log | cut -f1)
        print_success "Log Laravel existe (taille: $LOG_SIZE)"

        # Vérifier les erreurs récentes
        increment_total
        ERROR_COUNT=$(grep -c "ERROR" backend/storage/logs/laravel.log 2>/dev/null | tail -100 || echo "0")
        if [ "$ERROR_COUNT" -eq 0 ]; then
            print_success "Aucune erreur récente dans les logs"
        else
            print_warning "$ERROR_COUNT erreur(s) dans les 100 dernières lignes"
        fi
    else
        print_info "Pas de log Laravel (normal si jamais lancé)"
    fi
}

# Vérifier la mémoire et le disque
check_system_resources() {
    print_header "💾 Ressources système"

    # Mémoire disponible
    increment_total
    if command -v free &> /dev/null; then
        MEMORY_AVAILABLE=$(free -m | awk 'NR==2{printf "%.0f", $7}')
        if [ "$MEMORY_AVAILABLE" -gt 1000 ]; then
            print_success "Mémoire disponible: ${MEMORY_AVAILABLE}MB"
        elif [ "$MEMORY_AVAILABLE" -gt 500 ]; then
            print_warning "Mémoire disponible: ${MEMORY_AVAILABLE}MB (limite basse)"
        else
            print_error "Mémoire disponible: ${MEMORY_AVAILABLE}MB (critique)"
        fi
    else
        print_info "Impossible de vérifier la mémoire (commande 'free' non disponible)"
    fi

    # Espace disque
    increment_total
    DISK_USAGE=$(df -h . | awk 'NR==2{print $5}' | sed 's/%//')
    if [ "$DISK_USAGE" -lt 80 ]; then
        print_success "Espace disque utilisé: ${DISK_USAGE}%"
    elif [ "$DISK_USAGE" -lt 90 ]; then
        print_warning "Espace disque utilisé: ${DISK_USAGE}% (attention)"
    else
        print_error "Espace disque utilisé: ${DISK_USAGE}% (critique)"
    fi
}

# Vérifier Docker (si utilisé)
check_docker() {
    if command -v docker &> /dev/null; then
        print_header "🐳 Docker"

        increment_total
        if docker ps &> /dev/null; then
            RUNNING=$(docker ps --format "{{.Names}}" | grep css | wc -l)
            if [ "$RUNNING" -gt 0 ]; then
                print_success "$RUNNING container(s) CSS en cours d'exécution"
                docker ps --format "table {{.Names}}\t{{.Status}}" | grep css | while read line; do
                    print_info "  $line"
                done
            else
                print_warning "Aucun container CSS en cours d'exécution"
            fi
        else
            print_error "Impossible de communiquer avec Docker daemon"
        fi
    fi
}

# Résumé final
show_summary() {
    print_header "📊 Résumé"

    echo -e "${BLUE}Total des vérifications: ${NC}$TOTAL_CHECKS"
    echo -e "${GREEN}✅ Réussies: ${NC}$PASSED_CHECKS"
    echo -e "${YELLOW}⚠️  Avertissements: ${NC}$WARNING_CHECKS"
    echo -e "${RED}❌ Échecs: ${NC}$FAILED_CHECKS"
    echo ""

    # Score de santé
    if [ $TOTAL_CHECKS -gt 0 ]; then
        HEALTH_SCORE=$(( (PASSED_CHECKS * 100) / TOTAL_CHECKS ))
        echo -e "${BLUE}Score de santé: ${NC}"

        if [ $HEALTH_SCORE -ge 90 ]; then
            echo -e "  ${GREEN}${HEALTH_SCORE}% - Excellent! 🎉${NC}"
        elif [ $HEALTH_SCORE -ge 75 ]; then
            echo -e "  ${YELLOW}${HEALTH_SCORE}% - Bon ✅${NC}"
        elif [ $HEALTH_SCORE -ge 50 ]; then
            echo -e "  ${YELLOW}${HEALTH_SCORE}% - Acceptable ⚠️${NC}"
        else
            echo -e "  ${RED}${HEALTH_SCORE}% - Problèmes détectés ❌${NC}"
        fi
    fi

    echo ""

    # Recommandations
    if [ $FAILED_CHECKS -gt 0 ]; then
        echo -e "${RED}⚠️  Actions recommandées:${NC}"
        echo "  1. Vérifier les erreurs ci-dessus"
        echo "  2. Consulter les logs: tail -f backend/storage/logs/laravel.log"
        echo "  3. Relancer setup.sh si nécessaire"
        echo ""
    fi

    # Exit code
    if [ $FAILED_CHECKS -gt 0 ]; then
        exit 1
    else
        exit 0
    fi
}

# Menu principal
main() {
    clear
    echo -e "${GREEN}"
    cat << "EOF"
   _____ _____ _____   _   _            _ _   _
  / ____/ ____/ ____| | | | |          | | | | |
 | |   | (___| (___   | |_| | ___  __ _| | |_| |__
 | |    \___ \\___ \  |  _  |/ _ \/ _` | | __| '_ \
 | |____) |___) |___) | | | |  __/ (_| | | |_| | | |
  \_____|_____/_____/ \_| |_/\___|\__,_|_|\__|_| |_|

  Vérification de santé - CSS Platform
EOF
    echo -e "${NC}\n"

    print_info "Environnement: $ENV"
    print_info "Backend: $BACKEND_URL"
    print_info "Frontend: $FRONTEND_URL"
    echo ""

    # Exécution des vérifications
    check_network
    check_system_services
    check_database
    check_backend_api
    check_frontend
    check_configuration
    check_permissions
    check_dependencies
    check_logs
    check_system_resources
    check_docker
    show_summary
}

# Lancement du script
main "$@"
