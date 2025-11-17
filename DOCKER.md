# Guide Docker - Plateforme CSS

Ce guide explique comment utiliser Docker Compose pour démarrer rapidement l'environnement de développement complet de la plateforme CSS.

## 📋 Prérequis

- Docker 20.10+
- Docker Compose 2.0+

## 🚀 Démarrage rapide

### 1. Première utilisation

```bash
# Cloner le repository
git clone https://github.com/haythemsaa/css.git
cd css

# Démarrer tous les services
docker-compose up -d

# Attendre que tous les services démarrent (environ 30-60 secondes)
docker-compose ps
```

### 2. Configuration initiale du backend

```bash
# Copier le fichier .env
cp backend/.env.example backend/.env

# Générer la clé d'application
docker-compose exec backend php artisan key:generate

# Créer la base de données avec les seeders
docker-compose exec backend php artisan migrate:fresh --seed
```

### 3. Accéder aux services

Une fois tous les services démarrés :

| Service | URL | Identifiants |
|---------|-----|--------------|
| **Frontend React** | http://localhost:5173 | - |
| **Backend API** | http://localhost:8000/api/v1 | - |
| **Admin Panel** | http://localhost:8000/admin | admin@css.tn / password |
| **phpMyAdmin** | http://localhost:8080 | css_user / css_password |
| **Redis Commander** | http://localhost:8081 | - |

## 🛠️ Commandes utiles

### Gestion des conteneurs

```bash
# Démarrer tous les services
docker-compose up -d

# Arrêter tous les services
docker-compose down

# Voir les logs en temps réel
docker-compose logs -f

# Voir les logs d'un service spécifique
docker-compose logs -f backend
docker-compose logs -f frontend

# Redémarrer un service
docker-compose restart backend

# Voir l'état de tous les services
docker-compose ps
```

### Backend Laravel

```bash
# Accéder au shell du conteneur backend
docker-compose exec backend bash

# Exécuter des commandes Artisan
docker-compose exec backend php artisan migrate
docker-compose exec backend php artisan db:seed
docker-compose exec backend php artisan cache:clear
docker-compose exec backend php artisan config:clear

# Créer un utilisateur admin Filament
docker-compose exec backend php artisan make:filament-user

# Lancer les tests
docker-compose exec backend php artisan test
```

### Frontend React

```bash
# Accéder au shell du conteneur frontend
docker-compose exec frontend sh

# Installer de nouvelles dépendances
docker-compose exec frontend npm install package-name

# Build production
docker-compose exec frontend npm run build

# Linter
docker-compose exec frontend npm run lint
```

### Database MySQL

```bash
# Accéder au client MySQL
docker-compose exec mysql mysql -u css_user -pcss_password css_db

# Exporter la base de données
docker-compose exec mysql mysqldump -u css_user -pcss_password css_db > backup.sql

# Importer une base de données
docker-compose exec -T mysql mysql -u css_user -pcss_password css_db < backup.sql
```

### Redis

```bash
# Accéder au CLI Redis
docker-compose exec redis redis-cli

# Vider le cache Redis
docker-compose exec redis redis-cli FLUSHALL

# Voir les clés Redis
docker-compose exec redis redis-cli KEYS '*'
```

## 🔧 Configuration avancée

### Variables d'environnement

Modifier les fichiers suivants selon vos besoins :

**Backend** (`backend/.env`) :
```env
DB_CONNECTION=mysql
DB_HOST=mysql
DB_PORT=3306
DB_DATABASE=css_db
DB_USERNAME=css_user
DB_PASSWORD=css_password

REDIS_HOST=redis
REDIS_PORT=6379

CACHE_DRIVER=redis
QUEUE_CONNECTION=redis
SESSION_DRIVER=redis

FRONTEND_URL=http://localhost:5173
```

**Frontend** (`frontend/src/services/api.js`) :
```javascript
const API_BASE_URL = 'http://localhost:8000/api/v1';
```

### Ports personnalisés

Si les ports par défaut sont occupés, modifiez `docker-compose.yml` :

```yaml
services:
  backend:
    ports:
      - "8001:8000"  # Backend sur port 8001

  frontend:
    ports:
      - "3000:5173"  # Frontend sur port 3000

  mysql:
    ports:
      - "3307:3306"  # MySQL sur port 3307
```

## 📊 Services inclus

### 1. Backend (Laravel 12)
- **Container**: `css_backend`
- **Port**: 8000
- **Commande**: `php artisan serve`
- **Volume**: `./backend` → `/var/www/html`

### 2. Frontend (React 19 + Vite)
- **Container**: `css_frontend`
- **Port**: 5173
- **Commande**: `npm run dev -- --host`
- **Volume**: `./frontend` → `/app`

### 3. MySQL 8.0
- **Container**: `css_mysql`
- **Port**: 3306
- **Database**: `css_db`
- **User**: `css_user`
- **Password**: `css_password`

### 4. Redis 7
- **Container**: `css_redis`
- **Port**: 6379
- **Utilisation**: Cache, sessions, queues

### 5. Queue Worker
- **Container**: `css_queue`
- **Commande**: `php artisan queue:work`
- **Tries**: 3
- **Timeout**: 90s

### 6. phpMyAdmin
- **Container**: `css_phpmyadmin`
- **Port**: 8080
- **Accès**: http://localhost:8080

### 7. Redis Commander
- **Container**: `css_redis_commander`
- **Port**: 8081
- **Accès**: http://localhost:8081

## 🐛 Dépannage

### Les services ne démarrent pas

```bash
# Voir les logs détaillés
docker-compose logs

# Reconstruire les images
docker-compose build --no-cache

# Supprimer et recréer les conteneurs
docker-compose down -v
docker-compose up -d --build
```

### Problème de permissions (Linux)

```bash
# Donner les permissions au dossier storage
docker-compose exec backend chmod -R 775 storage bootstrap/cache
docker-compose exec backend chown -R www-data:www-data storage bootstrap/cache
```

### Le frontend ne se connecte pas au backend

1. Vérifier que le backend est accessible : http://localhost:8000/api/v1
2. Vérifier la configuration CORS dans `backend/config/cors.php`
3. Vérifier l'URL de l'API dans `frontend/src/services/api.js`

### La base de données n'est pas accessible

```bash
# Vérifier l'état de MySQL
docker-compose ps mysql

# Voir les logs MySQL
docker-compose logs mysql

# Redémarrer MySQL
docker-compose restart mysql

# Attendre que MySQL soit prêt
docker-compose exec mysql mysqladmin ping -h localhost
```

### Queue worker ne traite pas les jobs

```bash
# Voir les logs du queue worker
docker-compose logs -f queue

# Redémarrer le queue worker
docker-compose restart queue

# Vérifier les jobs en attente dans Redis
docker-compose exec redis redis-cli LLEN queues:default
```

## 🧹 Nettoyage

```bash
# Arrêter et supprimer tous les conteneurs
docker-compose down

# Supprimer aussi les volumes (ATTENTION: supprime la DB)
docker-compose down -v

# Supprimer les images
docker-compose down --rmi all

# Nettoyage complet du système Docker
docker system prune -a --volumes
```

## 📝 Notes importantes

1. **Hot Reload** : Le hot reload fonctionne pour le frontend (Vite) et le backend (pas de rebuild nécessaire)

2. **Volumes** : Les dossiers `vendor` et `node_modules` utilisent des volumes nommés pour de meilleures performances

3. **Production** : Cette configuration est pour le **développement uniquement**. Voir [DEPLOYMENT.md](DEPLOYMENT.md) pour la production

4. **Base de données** : Les données MySQL sont persistées dans un volume Docker (`mysql_data`)

5. **Ports** : Assurez-vous que les ports 3306, 5173, 6379, 8000, 8080, 8081 sont disponibles

## 🔗 Liens utiles

- [Documentation Docker](https://docs.docker.com)
- [Documentation Docker Compose](https://docs.docker.com/compose)
- [Guide de déploiement production](DEPLOYMENT.md)
- [Documentation API](API_DOCUMENTATION.md)

---

**Développé avec ❤️ pour le Club Sportif Sfaxien**

⚽ يا CSS يا نجوم السما ⚽
