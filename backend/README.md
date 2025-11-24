# CSS Club Sportif Sfaxien - Backend API

Backend API Laravel pour l'application CSS Club Sportif Sfaxien.

## 🚀 Technologies

- **Framework:** Laravel 11
- **PHP:** 8.2+
- **Base de données:** MySQL 8.0+ / PostgreSQL
- **Cache & Queue:** Redis
- **Authentification:** Laravel Sanctum
- **Stockage:** AWS S3 / Cloudflare R2
- **Streaming:** Cloudflare Stream
- **Recherche:** Meilisearch

## 📦 Installation

### Prérequis

- PHP 8.2 ou supérieur
- Composer
- MySQL 8.0+ ou PostgreSQL
- Redis
- Node.js 18+ (pour les assets)

### Étapes d'installation

1. **Cloner le repository**
```bash
git clone <repo-url>
cd backend
```

2. **Installer les dépendances**
```bash
composer install
```

3. **Configuration de l'environnement**
```bash
cp .env.example .env
php artisan key:generate
```

4. **Configurer la base de données**
Éditer `.env` et configurer vos paramètres de base de données:
```
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=css_database
DB_USERNAME=root
DB_PASSWORD=
```

5. **Exécuter les migrations**
```bash
php artisan migrate
```

6. **Seed la base de données (optionnel)**
```bash
php artisan db:seed
```

7. **Configurer le stockage**
```bash
php artisan storage:link
```

8. **Installer Passport/Sanctum**
```bash
php artisan sanctum:install
```

9. **Démarrer le serveur de développement**
```bash
php artisan serve
```

Le serveur sera accessible à `http://localhost:8000`

## 🗂️ Structure du Projet

```
backend/
├── app/
│   ├── Http/
│   │   ├── Controllers/    # Contrôleurs API
│   │   ├── Middleware/     # Middlewares personnalisés
│   │   └── Requests/       # Form Request Validation
│   ├── Models/             # Modèles Eloquent
│   ├── Services/           # Logique métier
│   ├── Events/             # Events
│   ├── Listeners/          # Event Listeners
│   └── Policies/           # Authorization Policies
├── config/                 # Fichiers de configuration
├── database/
│   ├── migrations/         # Migrations de base de données
│   ├── seeders/            # Seeders
│   └── factories/          # Model Factories
├── routes/
│   ├── api.php            # Routes API
│   ├── web.php            # Routes Web
│   └── console.php        # Routes Console
├── storage/               # Fichiers générés
└── tests/                 # Tests unitaires et fonctionnels
```

## 🔐 Authentification

L'API utilise Laravel Sanctum pour l'authentification via tokens.

### Obtenir un token

```http
POST /api/login
Content-Type: application/json

{
  "email": "user@example.com",
  "password": "password"
}
```

### Utiliser le token

```http
GET /api/user
Authorization: Bearer {token}
```

## 📚 Endpoints API

### Authentification
- `POST /api/register` - Inscription
- `POST /api/login` - Connexion
- `POST /api/logout` - Déconnexion
- `POST /api/verify-otp` - Vérification OTP
- `GET /api/user/profile` - Profil utilisateur

### Contenu
- `GET /api/contents` - Liste des contenus
- `GET /api/contents/{slug}` - Détail d'un contenu
- `GET /api/videos/{id}/stream` - Streaming vidéo

### Matchs
- `GET /api/matches` - Liste des matchs
- `GET /api/matches/{id}` - Détail d'un match
- `GET /api/matches/{id}/live` - Données live

### Joueurs
- `GET /api/players` - Liste des joueurs
- `GET /api/players/{id}` - Détail d'un joueur
- `GET /api/players/{id}/statistics` - Statistiques

### Dons
- `GET /api/campaigns` - Campagnes de crowdfunding
- `POST /api/donations` - Faire un don

### Socios
- `POST /api/socios/verify` - Vérifier membership
- `GET /api/socios/benefits` - Liste des avantages
- `GET /api/socios/events` - Événements exclusifs

### Freeoui (Partenaires)
- `GET /api/partners` - Liste des partenaires
- `GET /api/partners/{id}` - Détail partenaire
- `POST /api/reductions/generate` - Générer code QR
- `GET /api/offers` - Offres disponibles

### Cadeaux & Loterie
- `GET /api/gifts/available` - Cadeaux disponibles
- `GET /api/lottery/active` - Loteries actives
- `POST /api/lottery/{id}/buy-ticket` - Acheter ticket

Pour la documentation complète, voir `/docs/api/swagger.yaml`

## 🧪 Tests

```bash
# Exécuter tous les tests
php artisan test

# Exécuter les tests unitaires
php artisan test --testsuite=Unit

# Exécuter les tests fonctionnels
php artisan test --testsuite=Feature

# Avec coverage
php artisan test --coverage
```

## 🔧 Commandes Artisan Personnalisées

```bash
# Générer un code de réduction
php artisan freeoui:generate-code {user_id} {partner_id}

# Traiter les loteries expirées
php artisan lottery:process-draws

# Distribuer les cadeaux mensuels
php artisan gifts:distribute-monthly

# Nettoyer les codes expirés
php artisan codes:cleanup

# Synchroniser les statistiques
php artisan stats:sync
```

## 📊 Queues & Jobs

Les tâches asynchrones sont gérées par Redis Queue:

```bash
# Démarrer le worker
php artisan queue:work

# Démarrer Horizon (recommandé)
php artisan horizon
```

Jobs principaux:
- Envoi d'emails
- Notifications push
- Traitement de vidéos
- Génération de rapports
- Synchronisation statistiques

## 🔍 Monitoring

### Laravel Telescope
Accessible à `/telescope` (en développement uniquement)

### Laravel Horizon
Accessible à `/horizon` pour monitorer les queues

## 🚀 Déploiement

### Production

1. **Optimiser l'application**
```bash
composer install --optimize-autoloader --no-dev
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

2. **Configurer les variables d'environnement**
```bash
APP_ENV=production
APP_DEBUG=false
```

3. **Configurer le serveur web**
- Pointer le document root vers `/public`
- Configurer les permissions pour `/storage` et `/bootstrap/cache`

4. **Configurer les workers**
```bash
# Supervisor configuration pour queue workers
sudo cp deployment/supervisor-css-worker.conf /etc/supervisor/conf.d/
sudo supervisorctl reread
sudo supervisorctl update
sudo supervisorctl start css-worker:*
```

5. **Configurer les cron jobs**
```bash
* * * * * cd /path-to-project && php artisan schedule:run >> /dev/null 2>&1
```

## 🔒 Sécurité

- Toutes les routes API nécessitent une authentification sauf endpoints publics
- Rate limiting: 60 requêtes/minute (120 pour Premium)
- CORS configuré pour les domaines autorisés
- CSRF protection activée
- SQL injection prevention via Eloquent ORM
- XSS protection via validation des entrées

## 📝 Conventions de Code

- PSR-12 coding style
- Utiliser Laravel Pint pour le formatting: `./vendor/bin/pint`
- Commenter les fonctions complexes
- Tests obligatoires pour nouvelles fonctionnalités

## 🤝 Contribution

1. Créer une branche depuis `develop`
2. Faire vos modifications
3. Ajouter des tests
4. Commit avec message descriptif
5. Push et créer une Pull Request

## 📄 License

Propriétaire - Club Sportif Sfaxien

## 📧 Support

Pour toute question technique, contactez l'équipe de développement.
