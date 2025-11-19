# Makefile - CSS Platform
# Automatisation complète du projet (Backend + Frontend + Mobile)

.PHONY: help install setup dev test clean deploy docker-up docker-down migrate seed prod-build

# Couleurs pour output
GREEN=\033[0;32m
YELLOW=\033[1;33m
RED=\033[0;31m
NC=\033[0m # No Color

##@ Aide

help: ## Affiche cette aide
	@echo "$(GREEN)CSS Platform - Commandes disponibles:$(NC)"
	@echo ""
	@awk 'BEGIN {FS = ":.*##"; printf "\nUsage:\n  make $(YELLOW)<target>$(NC)\n"} /^[a-zA-Z_-]+:.*?##/ { printf "  $(GREEN)%-20s$(NC) %s\n", $$1, $$2 } /^##@/ { printf "\n$(YELLOW)%s$(NC)\n", substr($$0, 5) } ' $(MAKEFILE_LIST)

##@ Installation & Setup

install: ## Installation complète (Backend + Frontend + Mobile)
	@echo "$(GREEN)📦 Installation complète du projet CSS...$(NC)"
	@make install-backend
	@make install-frontend
	@make install-mobile
	@echo "$(GREEN)✅ Installation terminée!$(NC)"

install-backend: ## Installation Backend (Composer + NPM)
	@echo "$(YELLOW)📦 Installation Backend Laravel...$(NC)"
	cd backend && composer install --optimize-autoloader
	cd backend && npm install
	@echo "$(GREEN)✅ Backend installé!$(NC)"

install-frontend: ## Installation Frontend React
	@echo "$(YELLOW)📦 Installation Frontend React...$(NC)"
	cd frontend && npm install
	@echo "$(GREEN)✅ Frontend installé!$(NC)"

install-mobile: ## Installation Mobile React Native
	@echo "$(YELLOW)📦 Installation Mobile React Native...$(NC)"
	cd mobile && npm install
	@echo "$(GREEN)✅ Mobile installé!$(NC)"

setup: ## Configuration initiale complète (Copie .env, génère clés, migrations)
	@echo "$(GREEN)🔧 Configuration initiale...$(NC)"
	@make setup-backend
	@make setup-frontend
	@echo "$(GREEN)✅ Configuration terminée!$(NC)"

setup-backend: ## Configuration Backend (.env, key, migrations, seeders)
	@echo "$(YELLOW)🔧 Configuration Backend...$(NC)"
	@if [ ! -f backend/.env ]; then \
		cp backend/.env.example backend/.env; \
		echo "$(GREEN)✓ Fichier .env créé$(NC)"; \
	fi
	@if [ ! -f backend/database/database.sqlite ]; then \
		touch backend/database/database.sqlite; \
		echo "$(GREEN)✓ Base SQLite créée$(NC)"; \
	fi
	cd backend && php artisan key:generate
	cd backend && php artisan storage:link
	@make migrate-fresh
	@echo "$(GREEN)✅ Backend configuré!$(NC)"

setup-frontend: ## Configuration Frontend (.env)
	@echo "$(YELLOW)🔧 Configuration Frontend...$(NC)"
	@if [ ! -f frontend/.env ]; then \
		cp frontend/.env.example frontend/.env; \
		echo "$(GREEN)✓ Fichier .env créé$(NC)"; \
	fi
	@echo "$(GREEN)✅ Frontend configuré!$(NC)"

##@ Développement

dev: ## Lance Backend + Frontend en parallèle (mode développement)
	@echo "$(GREEN)🚀 Lancement en mode développement...$(NC)"
	@echo "$(YELLOW)Backend: http://localhost:8000$(NC)"
	@echo "$(YELLOW)Frontend: http://localhost:5173$(NC)"
	@echo "$(YELLOW)Admin: http://localhost:8000/admin$(NC)"
	@make -j2 dev-backend dev-frontend

dev-backend: ## Lance uniquement le Backend (API + Admin)
	@echo "$(YELLOW)🚀 Lancement Backend...$(NC)"
	cd backend && php artisan serve

dev-frontend: ## Lance uniquement le Frontend React
	@echo "$(YELLOW)🚀 Lancement Frontend...$(NC)"
	cd frontend && npm run dev

dev-mobile: ## Lance l'app Mobile avec Expo
	@echo "$(YELLOW)🚀 Lancement Mobile...$(NC)"
	cd mobile && npm start

dev-all: ## Lance Backend + Frontend + Mobile en parallèle
	@echo "$(GREEN)🚀 Lancement COMPLET (Backend + Frontend + Mobile)...$(NC)"
	@make -j3 dev-backend dev-frontend dev-mobile

##@ Base de données

migrate: ## Exécute les migrations
	@echo "$(YELLOW)🗄️  Exécution des migrations...$(NC)"
	cd backend && php artisan migrate
	@echo "$(GREEN)✅ Migrations terminées!$(NC)"

migrate-fresh: ## Reset et exécute les migrations + seeders
	@echo "$(YELLOW)🗄️  Reset de la base de données...$(NC)"
	cd backend && php artisan migrate:fresh --seed
	@echo "$(GREEN)✅ Base de données réinitialisée avec données de test!$(NC)"

seed: ## Exécute les seeders
	@echo "$(YELLOW)🌱 Exécution des seeders...$(NC)"
	cd backend && php artisan db:seed
	@echo "$(GREEN)✅ Seeders terminés!$(NC)"

db-reset: migrate-fresh ## Alias pour migrate-fresh

##@ Tests

test: ## Exécute tous les tests (Backend + Frontend + Mobile)
	@echo "$(GREEN)🧪 Exécution de tous les tests...$(NC)"
	@make test-backend
	@make test-frontend
	@make test-mobile
	@echo "$(GREEN)✅ Tous les tests passent!$(NC)"

test-backend: ## Tests Backend (PHPUnit)
	@echo "$(YELLOW)🧪 Tests Backend...$(NC)"
	cd backend && php artisan test

test-frontend: ## Tests Frontend (Vitest)
	@echo "$(YELLOW)🧪 Tests Frontend...$(NC)"
	cd frontend && npm run test

test-mobile: ## Tests Mobile (Jest)
	@echo "$(YELLOW)🧪 Tests Mobile...$(NC)"
	cd mobile && npm test

test-coverage: ## Tests avec coverage (Backend + Frontend + Mobile)
	@echo "$(GREEN)📊 Tests avec coverage...$(NC)"
	cd backend && php artisan test --coverage
	cd frontend && npm run test:coverage
	cd mobile && npm run test:coverage

##@ Quality & Linting

lint: ## Lint tous les projets
	@echo "$(YELLOW)🔍 Linting...$(NC)"
	cd backend && ./vendor/bin/pint
	cd frontend && npm run lint

lint-fix: ## Fix automatique des problèmes de lint
	@echo "$(YELLOW)🔧 Fixing lint issues...$(NC)"
	cd backend && ./vendor/bin/pint
	cd frontend && npm run lint -- --fix

phpstan: ## Analyse statique PHP (PHPStan niveau 5)
	@echo "$(YELLOW)🔍 Analyse PHPStan...$(NC)"
	cd backend && ./vendor/bin/phpstan analyse

quality: lint phpstan test ## Vérification qualité complète (lint + phpstan + tests)

##@ Build & Production

build: ## Build production (Frontend + Mobile)
	@echo "$(GREEN)🏗️  Build production...$(NC)"
	@make build-frontend
	@echo "$(GREEN)✅ Build terminé!$(NC)"

build-frontend: ## Build Frontend pour production
	@echo "$(YELLOW)🏗️  Build Frontend...$(NC)"
	cd frontend && npm run build
	@echo "$(GREEN)✅ Frontend build créé dans frontend/dist/$(NC)"

build-mobile-android: ## Build Mobile Android (APK)
	@echo "$(YELLOW)📱 Build Android...$(NC)"
	cd mobile && npm run build:android

build-mobile-ios: ## Build Mobile iOS (IPA)
	@echo "$(YELLOW)📱 Build iOS...$(NC)"
	cd mobile && npm run build:ios

optimize-backend: ## Optimise le Backend pour production
	@echo "$(YELLOW)⚡ Optimisation Backend...$(NC)"
	cd backend && php artisan config:cache
	cd backend && php artisan route:cache
	cd backend && php artisan view:cache
	cd backend && php artisan event:cache
	@echo "$(GREEN)✅ Backend optimisé!$(NC)"

prod-build: build optimize-backend ## Build production complet avec optimisations

##@ Docker

docker-up: ## Lance tous les services Docker
	@echo "$(GREEN)🐳 Lancement Docker Compose...$(NC)"
	docker-compose up -d
	@echo "$(GREEN)✅ Services démarrés!$(NC)"
	@echo "$(YELLOW)Backend: http://localhost:8000$(NC)"
	@echo "$(YELLOW)Frontend: http://localhost:5173$(NC)"
	@echo "$(YELLOW)phpMyAdmin: http://localhost:8080$(NC)"
	@echo "$(YELLOW)Redis Commander: http://localhost:8081$(NC)"

docker-down: ## Arrête tous les services Docker
	@echo "$(YELLOW)🐳 Arrêt Docker Compose...$(NC)"
	docker-compose down
	@echo "$(GREEN)✅ Services arrêtés!$(NC)"

docker-restart: ## Redémarre tous les services Docker
	@make docker-down
	@make docker-up

docker-logs: ## Affiche les logs Docker
	docker-compose logs -f

docker-build: ## Build les images Docker
	@echo "$(GREEN)🐳 Build des images Docker...$(NC)"
	docker-compose build
	@echo "$(GREEN)✅ Images construites!$(NC)"

docker-clean: ## Nettoie les containers et volumes Docker
	@echo "$(RED)⚠️  Nettoyage Docker (containers + volumes)...$(NC)"
	docker-compose down -v
	@echo "$(GREEN)✅ Docker nettoyé!$(NC)"

docker-prod-up: ## Lance Docker en mode production
	@echo "$(GREEN)🐳 Lancement Docker Production...$(NC)"
	docker-compose -f docker-compose.prod.yml up -d
	@echo "$(GREEN)✅ Production démarrée!$(NC)"

##@ Nettoyage

clean: ## Nettoie les fichiers temporaires
	@echo "$(YELLOW)🧹 Nettoyage...$(NC)"
	@make clean-backend
	@make clean-frontend
	@make clean-mobile
	@echo "$(GREEN)✅ Nettoyage terminé!$(NC)"

clean-backend: ## Nettoie Backend (cache, logs)
	@echo "$(YELLOW)🧹 Nettoyage Backend...$(NC)"
	cd backend && php artisan cache:clear
	cd backend && php artisan config:clear
	cd backend && php artisan route:clear
	cd backend && php artisan view:clear
	cd backend && rm -rf bootstrap/cache/*.php

clean-frontend: ## Nettoie Frontend (node_modules, dist)
	@echo "$(YELLOW)🧹 Nettoyage Frontend...$(NC)"
	rm -rf frontend/dist
	rm -rf frontend/node_modules/.vite

clean-mobile: ## Nettoie Mobile (cache)
	@echo "$(YELLOW)🧹 Nettoyage Mobile...$(NC)"
	cd mobile && rm -rf .expo

deep-clean: ## Nettoyage profond (node_modules, vendor, cache)
	@echo "$(RED)⚠️  Nettoyage profond (supprime node_modules et vendor)...$(NC)"
	rm -rf backend/vendor
	rm -rf backend/node_modules
	rm -rf frontend/node_modules
	rm -rf mobile/node_modules
	@make clean
	@echo "$(GREEN)✅ Nettoyage profond terminé!$(NC)"

##@ Utilitaires

admin-create: ## Crée un utilisateur admin Filament
	@echo "$(YELLOW)👤 Création utilisateur admin...$(NC)"
	cd backend && php artisan make:filament-user

queue-work: ## Lance le worker de queue
	@echo "$(YELLOW)⚙️  Lancement queue worker...$(NC)"
	cd backend && php artisan queue:work

tinker: ## Lance Laravel Tinker (REPL)
	cd backend && php artisan tinker

logs: ## Affiche les logs Backend
	tail -f backend/storage/logs/laravel.log

status: ## Affiche le status du projet
	@echo "$(GREEN)📊 Status du projet CSS:$(NC)"
	@echo ""
	@echo "$(YELLOW)Backend:$(NC)"
	@if [ -f backend/vendor/autoload.php ]; then echo "  ✅ Composer installé"; else echo "  ❌ Composer non installé"; fi
	@if [ -f backend/.env ]; then echo "  ✅ .env configuré"; else echo "  ❌ .env manquant"; fi
	@if [ -f backend/database/database.sqlite ]; then echo "  ✅ Base SQLite créée"; else echo "  ❌ Base SQLite manquante"; fi
	@echo ""
	@echo "$(YELLOW)Frontend:$(NC)"
	@if [ -d frontend/node_modules ]; then echo "  ✅ NPM installé"; else echo "  ❌ NPM non installé"; fi
	@if [ -f frontend/.env ]; then echo "  ✅ .env configuré"; else echo "  ❌ .env manquant"; fi
	@echo ""
	@echo "$(YELLOW)Mobile:$(NC)"
	@if [ -d mobile/node_modules ]; then echo "  ✅ NPM installé"; else echo "  ❌ NPM non installé"; fi

update: ## Met à jour toutes les dépendances
	@echo "$(YELLOW)🔄 Mise à jour des dépendances...$(NC)"
	cd backend && composer update
	cd frontend && npm update
	cd mobile && npm update
	@echo "$(GREEN)✅ Dépendances mises à jour!$(NC)"

fresh-start: deep-clean install setup ## Réinstallation complète du projet

##@ Quick Commands (Raccourcis)

i: install ## Alias pour install
s: setup ## Alias pour setup
d: dev ## Alias pour dev
t: test ## Alias pour test
b: build ## Alias pour build
c: clean ## Alias pour clean

# Commande par défaut
.DEFAULT_GOAL := help
