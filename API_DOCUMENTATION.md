# API Documentation - CSS Platform

## Base URL
```
http://votre-domaine/api/v1
```

## Authentication

L'API utilise **Laravel Sanctum** pour l'authentification basée sur tokens.

### Headers requis pour les routes protégées
```
Authorization: Bearer {token}
Content-Type: application/json
Accept: application/json
```

---

## Authentication Endpoints

### 1. Register (Inscription)

**POST** `/auth/register`

Crée un nouveau compte utilisateur (type Free par défaut).

**Body:**
```json
{
  "name": "Ahmed Ben Ali",
  "email": "ahmed@example.com",
  "password": "motdepasse123",
  "password_confirmation": "motdepasse123",
  "phone": "+216 20 123 456",
  "city": "Sfax",
  "governorate": "Sfax",
  "birth_date": "1995-05-15"
}
```

**Response 201:**
```json
{
  "success": true,
  "message": "Inscription réussie. Bienvenue au Club Sportif Sfaxien!",
  "data": {
    "user": {
      "id": 103,
      "name": "Ahmed Ben Ali",
      "email": "ahmed@example.com",
      "user_type": "free",
      "loyalty_points": 0,
      "loyalty_level": "bronze"
    },
    "token": "1|abc123..."
  }
}
```

### 2. Login (Connexion)

**POST** `/auth/login`

Authentifie un utilisateur existant.

**Body:**
```json
{
  "email": "admin@css.tn",
  "password": "password"
}
```

**Response 200:**
```json
{
  "success": true,
  "message": "Connexion réussie",
  "data": {
    "user": {
      "id": 1,
      "name": "Admin CSS",
      "email": "admin@css.tn",
      "user_type": "socios",
      "socios_verified": true,
      "loyalty_points": 5000,
      "loyalty_level": "platinum",
      "is_premium_active": false
    },
    "token": "2|xyz789..."
  }
}
```

### 3. Logout (Déconnexion)

**POST** `/auth/logout` 🔒

Révoque le token actuel de l'utilisateur.

**Response 200:**
```json
{
  "success": true,
  "message": "Déconnexion réussie"
}
```

### 4. Get Profile (Profil utilisateur)

**GET** `/auth/profile` 🔒

Récupère le profil de l'utilisateur authentifié.

**Response 200:**
```json
{
  "success": true,
  "data": {
    "id": 1,
    "name": "Admin CSS",
    "email": "admin@css.tn",
    "phone": "+216 74 123 456",
    "user_type": "socios",
    "socios_number": "CSS-100001",
    "socios_verified": true,
    "subscription_status": "active",
    "loyalty_points": 5000,
    "loyalty_level": "platinum"
  }
}
```

### 5. Update Profile

**PUT** `/auth/profile` 🔒

Met à jour le profil utilisateur.

**Body:**
```json
{
  "name": "Ahmed Ben Ali",
  "phone": "+216 20 123 456",
  "city": "Sfax"
}
```

### 6. Change Password

**POST** `/auth/change-password` 🔒

Modifie le mot de passe de l'utilisateur.

**Body:**
```json
{
  "current_password": "ancien_mot_de_passe",
  "new_password": "nouveau_mot_de_passe",
  "new_password_confirmation": "nouveau_mot_de_passe"
}
```

---

## Partners (CSS Privilèges) Endpoints

### 1. Get Partner Categories

**GET** `/partners/categories`

Récupère toutes les catégories de partenaires.

**Response 200:**
```json
{
  "success": true,
  "data": [
    {
      "id": 1,
      "name_fr": "Restauration",
      "name_ar": "مطاعم",
      "slug": "restauration",
      "icon": "🍽️",
      "color": "#FF6B6B",
      "partners_count": 8
    },
    {
      "id": 2,
      "name_fr": "Shopping",
      "name_ar": "تسوق",
      "slug": "shopping",
      "icon": "🛍️",
      "color": "#4ECDC4",
      "partners_count": 6
    }
  ]
}
```

### 2. Get All Partners

**GET** `/partners`

Liste tous les partenaires avec filtres optionnels.

**Query Parameters:**
- `category` (string): Slug de la catégorie
- `city` (string): Filtrer par ville
- `governorate` (string): Filtrer par gouvernorat
- `search` (string): Recherche dans nom/description
- `latitude` (float): Latitude pour recherche géographique
- `longitude` (float): Longitude pour recherche géographique
- `radius` (int): Rayon de recherche en km (défaut: 10)
- `per_page` (int): Nombre de résultats par page (défaut: 15)

**Example:** `/partners?category=restauration&city=Sfax&per_page=20`

**Response 200:**
```json
{
  "success": true,
  "data": [
    {
      "id": 1,
      "name": "Restaurant Le Corail",
      "slug": "restaurant-le-corail",
      "description": "Restaurant de fruits de mer",
      "logo": "/storage/partners/logos/corail.jpg",
      "category": {
        "id": 1,
        "name_fr": "Restauration",
        "slug": "restauration"
      },
      "city": "Sfax",
      "address": "Avenue Habib Bourguiba, Sfax",
      "phone": "+216 74 123 456",
      "latitude": 34.7449,
      "longitude": 10.7594,
      "distance": "2.5 km",
      "has_discount": true,
      "reduction_type": "percentage",
      "reduction_value": 15,
      "discount_label": "-15%",
      "status": "active",
      "is_featured": true,
      "offers_count": 3
    }
  ],
  "meta": {
    "current_page": 1,
    "last_page": 2,
    "per_page": 15,
    "total": 29
  }
}
```

### 3. Get Featured Partners

**GET** `/partners/featured`

Récupère les 10 partenaires mis en vedette.

### 4. Get Partner Details

**GET** `/partners/{slug}`

Récupère les détails d'un partenaire spécifique.

**Example:** `/partners/restaurant-le-corail`

**Response 200:**
```json
{
  "success": true,
  "data": {
    "id": 1,
    "name": "Restaurant Le Corail",
    "slug": "restaurant-le-corail",
    "description": "Restaurant spécialisé en fruits de mer...",
    "logo": "/storage/partners/logos/corail.jpg",
    "cover_image": "/storage/partners/covers/corail.jpg",
    "category": {
      "id": 1,
      "name_fr": "Restauration"
    },
    "phone": "+216 74 123 456",
    "email": "contact@lecorail.tn",
    "website": "https://lecorail.tn",
    "address": "Avenue Habib Bourguiba, Sfax",
    "city": "Sfax",
    "governorate": "Sfax",
    "opening_hours": {
      "lundi": "9h-22h",
      "mardi": "9h-22h",
      "mercredi": "9h-22h",
      "jeudi": "9h-22h",
      "vendredi": "9h-23h",
      "samedi": "9h-23h",
      "dimanche": "Fermé"
    },
    "has_discount": true,
    "reduction_type": "percentage",
    "reduction_value": 15,
    "offers": [
      {
        "id": 1,
        "title": "Menu du jour -20%",
        "slug": "menu-du-jour-20",
        "reduction_value": 20,
        "valid_until": "2025-12-31 23:59:59",
        "is_valid": true,
        "stock_remaining": 45
      }
    ]
  }
}
```

---

## Offers Endpoints

### 1. Get All Offers

**GET** `/offers`

Liste toutes les offres partenaires.

**Query Parameters:**
- `type` (string): Type d'offre (standard, flash, seasonal, exclusive)
- `category` (string): Slug de la catégorie partenaire
- `min_reduction` (int): Réduction minimum
- `featured` (boolean): Offres à la une uniquement
- `order_by` (string): Tri (created_at, reduction_value, valid_until)
- `order_direction` (string): asc ou desc

**Example:** `/offers?type=flash&min_reduction=15&featured=1`

**Response 200:**
```json
{
  "success": true,
  "data": [
    {
      "id": 1,
      "title": "Menu du jour -20%",
      "slug": "menu-du-jour-20",
      "description": "Menu complet avec entrée, plat et dessert",
      "offer_type": "flash",
      "offer_type_label": "Offre Flash",
      "reduction_type": "percentage",
      "reduction_value": 20,
      "discount_label": "-20%",
      "valid_from": "2025-11-01 00:00:00",
      "valid_until": "2025-11-30 23:59:59",
      "is_valid": true,
      "is_expiring_soon": false,
      "days_remaining": 13,
      "stock_available": 100,
      "stock_used": 55,
      "stock_remaining": 45,
      "stock_percentage": 45.0,
      "is_low_stock": false,
      "partner": {
        "id": 1,
        "name": "Restaurant Le Corail",
        "city": "Sfax"
      }
    }
  ],
  "meta": {
    "current_page": 1,
    "last_page": 5,
    "per_page": 15,
    "total": 64
  }
}
```

### 2. Get Offer Details

**GET** `/offers/{slug}`

Récupère les détails d'une offre spécifique.

---

## Reduction Codes Endpoints

### 1. Generate Code 🔒

**POST** `/codes/generate/{offerSlug}`

Génère un code de réduction pour une offre (Premium/Socios uniquement).

**Body:**
```json
{
  "type": "qr"  // qr, promo, or nfc
}
```

**Response 201:**
```json
{
  "success": true,
  "message": "Code de réduction généré avec succès",
  "data": {
    "id": 1,
    "code": "QR-A8F3K9L2",
    "type": "qr",
    "type_label": "QR Code",
    "status": "active",
    "is_active": true,
    "uses_count": 0,
    "max_uses": 1,
    "remaining_uses": 1,
    "expires_at": "2025-11-30 23:59:59",
    "days_until_expiry": 13,
    "qr_data": "{\"offer_id\":1,\"user_id\":50,...}",
    "offer": {
      "id": 1,
      "title": "Menu du jour -20%",
      "reduction_value": 20
    }
  }
}
```

**Error 403 (Free user):**
```json
{
  "success": false,
  "message": "Vous devez être membre Premium ou Socios pour générer des codes de réduction.",
  "upgrade_required": true
}
```

### 2. Get My Codes 🔒

**GET** `/codes/my-codes`

Liste tous les codes de l'utilisateur connecté.

**Query Parameters:**
- `status` (string): active, used, expired
- `type` (string): qr, promo, nfc
- `active_only` (boolean): Codes actifs uniquement
- `per_page` (int): Résultats par page

### 3. Get Code Details 🔒

**GET** `/codes/{code}`

Récupère les détails d'un code spécifique.

### 4. Validate Code (Public)

**POST** `/codes/validate`

Valide un code de réduction (pour utilisation par les partenaires).

**Body:**
```json
{
  "code": "QR-A8F3K9L2"
}
```

**Response 200:**
```json
{
  "success": true,
  "valid": true,
  "message": "Code valide",
  "data": {
    "code": "QR-A8F3K9L2",
    "status": "active",
    "offer": {
      "title": "Menu du jour -20%",
      "reduction_value": 20,
      "reduction_type": "percentage"
    },
    "user": {
      "name": "Ahmed Ben Ali",
      "user_type": "premium"
    }
  }
}
```

### 5. Use Code (Public)

**POST** `/codes/{code}/use`

Marque un code comme utilisé et enregistre la transaction.

**Body:**
```json
{
  "amount": 50.00  // Montant original en TND
}
```

**Response 200:**
```json
{
  "success": true,
  "message": "Code utilisé avec succès",
  "data": {
    "original_amount": 50.00,
    "discount_amount": 10.00,
    "final_amount": 40.00,
    "loyalty_points_earned": 4
  }
}
```

---

## Content Endpoints

### 1. Get All Content

**GET** `/content`

Liste tous les contenus (articles, vidéos, galeries, podcasts).

**Query Parameters:**
- `type` (string): article, video, gallery, podcast
- `featured` (boolean): Contenus à la une
- `search` (string): Recherche dans titre/description
- `order_by` (string): published_at, views_count, likes_count
- `per_page` (int): Résultats par page

**Response 200:**
```json
{
  "success": true,
  "data": [
    {
      "id": 1,
      "title": "CSS remporte le derby contre l'ESS",
      "slug": "css-remporte-derby-ess",
      "excerpt": "Victoire historique 2-1...",
      "type": "article",
      "type_label": "Article",
      "is_premium": false,
      "is_featured": true,
      "author": {
        "id": 1,
        "name": "Rédaction CSS",
        "photo": null
      },
      "views_count": 1250,
      "likes_count": 89,
      "is_liked": false,
      "published_at": "2025-11-16 10:30:00",
      "published_at_human": "il y a 1 heure"
    }
  ],
  "meta": {
    "current_page": 1,
    "total": 40
  }
}
```

### 2. Get Featured Content

**GET** `/content/featured`

Récupère les 10 contenus à la une.

### 3. Get Content Details

**GET** `/content/{slug}`

Récupère un contenu spécifique (incrémente les vues).

**Error 403 (Premium content for Free user):**
```json
{
  "success": false,
  "message": "Ce contenu est réservé aux membres Premium et Socios",
  "upgrade_required": true
}
```

### 4. Like/Unlike Content 🔒

**POST** `/content/{slug}/like`

Ajoute ou retire un like sur un contenu.

**Response 200:**
```json
{
  "success": true,
  "data": {
    "liked": true,
    "likes_count": 90
  }
}
```

---

## Players Endpoints

### 1. Get All Players

**GET** `/players`

Liste tous les joueurs de l'effectif.

**Query Parameters:**
- `position` (string): goalkeeper, defender, midfielder, forward
- `nationality` (string): Filtrer par nationalité
- `search` (string): Recherche dans nom/bio
- `order_by` (string): jersey_number, name
- `per_page` (int): Résultats par page

**Response 200:**
```json
{
  "success": true,
  "data": [
    {
      "id": 1,
      "name": "Ali Jemal",
      "slug": "ali-jemal",
      "jersey_number": 1,
      "position": "goalkeeper",
      "position_label": "Gardien",
      "photo": "/storage/players/ali-jemal.jpg",
      "birth_date": "1995-03-15",
      "age": 30,
      "nationality": "Tunisie",
      "height": 188,
      "weight": 82,
      "goals": 0,
      "assists": 2,
      "yellow_cards": 3,
      "red_cards": 0,
      "matches_played": 28,
      "is_active": true
    }
  ],
  "meta": {
    "total": 23
  }
}
```

### 2. Get Player Details

**GET** `/players/{slug}`

Récupère les détails d'un joueur.

### 3. Get Players by Position

**GET** `/players/position/{position}`

Filtre les joueurs par position.

---

## Matches Endpoints

### 1. Get All Matches

**GET** `/matches`

Liste tous les matchs.

**Query Parameters:**
- `status` (string): scheduled, live, finished, completed, postponed, cancelled
- `competition` (string): ligue1, cup, champions_league, confederation_cup
- `season` (string): 2024-2025
- `upcoming` (boolean): Matchs à venir uniquement
- `past` (boolean): Matchs passés uniquement
- `per_page` (int): Résultats par page

**Response 200:**
```json
{
  "success": true,
  "data": [
    {
      "id": 1,
      "opponent": "Espérance Sportive de Tunis",
      "opponent_logo": "/storage/teams/est.png",
      "match_date": "2025-11-20 15:00:00",
      "match_date_human": "dans 3 jours",
      "stadium": "Stade Taieb Mhiri",
      "home_away": "home",
      "is_home": true,
      "competition": "ligue1",
      "competition_label": "Ligue 1 Professionnelle",
      "season": "2024-2025",
      "matchday": 10,
      "css_score": null,
      "opponent_score": null,
      "score_display": "vs",
      "status": "scheduled",
      "status_label": "Programmé",
      "is_upcoming": true,
      "is_live": false,
      "is_finished": false
    }
  ],
  "meta": {
    "total": 20
  }
}
```

### 2. Get Upcoming Matches

**GET** `/matches/upcoming`

Récupère les 5 prochains matchs.

### 3. Get Latest Results

**GET** `/matches/results`

Récupère les 5 derniers résultats.

### 4. Get Match Details

**GET** `/matches/{id}`

Récupère les détails d'un match spécifique.

---

## Error Responses

### Format standard des erreurs

```json
{
  "success": false,
  "message": "Description de l'erreur"
}
```

### Codes HTTP

- **200 OK**: Requête réussie
- **201 Created**: Ressource créée avec succès
- **400 Bad Request**: Données invalides
- **401 Unauthorized**: Token manquant ou invalide
- **403 Forbidden**: Accès refusé (ex: contenu premium)
- **404 Not Found**: Ressource non trouvée
- **422 Unprocessable Entity**: Erreur de validation
- **500 Internal Server Error**: Erreur serveur

### Exemples d'erreurs de validation

```json
{
  "message": "Les données fournies sont invalides.",
  "errors": {
    "email": [
      "Cette adresse email est déjà utilisée"
    ],
    "password": [
      "Le mot de passe doit contenir au moins 8 caractères"
    ]
  }
}
```

---

## Rate Limiting

L'API applique des limites de taux :
- **60 requêtes/minute** pour les routes publiques
- **120 requêtes/minute** pour les utilisateurs authentifiés

Headers de réponse :
```
X-RateLimit-Limit: 60
X-RateLimit-Remaining: 58
X-RateLimit-Reset: 1637251200
```

---

## Pagination

Les endpoints de liste supportent la pagination avec les paramètres :
- `page` (int): Numéro de page
- `per_page` (int): Résultats par page (max: 100, défaut: 15)

Métadonnées retournées :
```json
{
  "meta": {
    "current_page": 1,
    "last_page": 5,
    "per_page": 15,
    "total": 64
  }
}
```

---

## Support

Pour toute question ou problème avec l'API :
- Documentation technique : `/backend/README.md`
- Email support : support@css.tn
- Issues GitHub : https://github.com/haythemsaa/css/issues

---

**Version**: 1.0
**Dernière mise à jour**: Novembre 2025
**Framework**: Laravel 12 + Sanctum
