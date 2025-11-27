# Admin API Documentation

## Authentication

Toutes les routes admin nécessitent:
1. Authentication via Sanctum token
2. Role 'admin' dans la base de données

Header requis:
```
Authorization: Bearer {token}
```

## Routes Admin

### Dashboard Stats

**GET** `/api/v1/admin/dashboard/stats`

Retourne les statistiques globales du système.

**Response:**
```json
{
  "auctions": {
    "total": 25,
    "active": 10,
    "revenue": 45000.00
  },
  "donations": {
    "total_goals": 15,
    "active_goals": 8,
    "total_raised": 125000.00
  },
  "payments": {
    "total_methods": 12,
    "active_methods": 10,
    "total_transactions": 350
  }
}
```

---

## Auction Management

### Create Auction
**POST** `/api/v1/admin/auctions`

**Request Body:**
```json
{
  "title": "Maillot Historique CSS",
  "slug": "maillot-historique-css-2007",
  "description": "Maillot porté lors de la finale...",
  "images": ["/images/maillot1.jpg", "/images/maillot2.jpg"],
  "category": "signed_items",
  "starting_price": 500.00,
  "reserve_price": 800.00,
  "buy_now_price": 1500.00,
  "bid_increment": 50,
  "start_time": "2024-02-01 10:00:00",
  "end_time": "2024-02-10 22:00:00",
  "is_featured": true,
  "auto_extend": true,
  "auto_extend_minutes": 5,
  "terms_conditions": "Conditions...",
  "metadata": {
    "authenticity": "Certificat CSS officiel",
    "condition": "Excellent"
  }
}
```

### Update Auction
**PUT** `/api/v1/admin/auctions/{id}`

### Delete Auction
**DELETE** `/api/v1/admin/auctions/{id}`

### Auction Statistics
**GET** `/api/v1/admin/auctions/statistics`

**Response:**
```json
{
  "total_auctions": 25,
  "active_auctions": 10,
  "ended_auctions": 12,
  "total_bids": 458,
  "total_revenue": 45000.00,
  "featured_auctions": 5,
  "by_category": [
    {"category": "signed_items", "count": 10},
    {"category": "memorabilia", "count": 8}
  ]
}
```

---

## Donation Goals Management

### Create Donation Goal
**POST** `/api/v1/admin/donation-goals`

**Request Body:**
```json
{
  "title": "Paiement Litige FIFA",
  "slug": "litige-fifa-2024",
  "description": "Objectif pour payer le litige avec la FIFA...",
  "full_details": "Détails complets...",
  "category": "litigation",
  "target_amount": 250000.00,
  "min_donation": 10.00,
  "priority": "urgent",
  "start_date": "2024-02-01",
  "end_date": "2024-06-01",
  "is_featured": true,
  "featured_image": "/images/litige.jpg",
  "gallery_images": ["/images/doc1.jpg"],
  "impact_metrics": "Permettra de lever la sanction FIFA",
  "thank_you_message": "Merci pour votre soutien!",
  "show_donors": true,
  "allow_anonymous": true,
  "rewards": [
    {"amount": 100, "reward": "Badge Bronze"},
    {"amount": 500, "reward": "Badge Argent"}
  ]
}
```

### Update Donation Goal
**PUT** `/api/v1/admin/donation-goals/{id}`

### Delete Donation Goal
**DELETE** `/api/v1/admin/donation-goals/{id}`

### Donation Goals Statistics
**GET** `/api/v1/admin/donation-goals/statistics`

---

## Payment Methods Management

### Create Payment Method
**POST** `/api/v1/admin/payment-methods`

**Request Body:**
```json
{
  "name": "D17",
  "code": "d17",
  "type": "mobile_wallet",
  "description": "Paiement mobile D17",
  "logo_url": "/images/d17-logo.png",
  "provider": "Ooredoo Tunisie",
  "is_active": true,
  "is_default": false,
  "supported_currencies": ["TND"],
  "min_amount": 5.00,
  "max_amount": 5000.00,
  "transaction_fee": 0.00,
  "transaction_fee_percentage": 1.5,
  "config": {
    "api_key": "...",
    "merchant_id": "..."
  },
  "processing_time": "Instantané",
  "supports_refund": true,
  "instructions": "Composez *155# pour payer",
  "display_order": 1,
  "available_for": ["donations", "products", "tickets", "auctions"]
}
```

### Update Payment Method
**PUT** `/api/v1/admin/payment-methods/{id}`

### Delete Payment Method
**DELETE** `/api/v1/admin/payment-methods/{id}`

### Payment Methods Statistics
**GET** `/api/v1/admin/payment-methods/statistics`

---

## Categories & Enums

### Auction Categories
- `collectibles` - Objets de Collection
- `memorabilia` - Souvenirs
- `experiences` - Expériences
- `signed_items` - Articles Dédicacés

### Auction Status
- `scheduled` - Programmé
- `active` - Actif
- `ended` - Terminé
- `sold` - Vendu
- `cancelled` - Annulé

### Donation Goal Categories
- `litigation` - Paiement de Litiges
- `player_transfer` - Achat de Joueurs
- `stadium_renovation` - Rénovation du Stade
- `youth_academy` - Académie des Jeunes
- `equipment` - Équipements
- `debt_payment` - Remboursement de Dettes
- `other` - Autre

### Priority Levels
- `low` - Basse
- `medium` - Moyenne
- `high` - Haute
- `urgent` - Urgent

### Payment Method Types
- `mobile_wallet` - Portefeuille Mobile
- `bank_card` - Carte Bancaire
- `bank_transfer` - Virement Bancaire
- `cash` - Espèces
- `international` - International

---

## Error Responses

### 401 Unauthorized
```json
{
  "success": false,
  "message": "Non authentifié"
}
```

### 403 Forbidden
```json
{
  "success": false,
  "message": "Accès non autorisé. Privilèges administrateur requis."
}
```

### 422 Validation Error
```json
{
  "success": false,
  "message": "Les données fournies sont invalides",
  "errors": {
    "title": ["Le champ title est obligatoire"],
    "starting_price": ["Le prix de départ doit être supérieur à 0"]
  }
}
```

---

## Comment créer un utilisateur admin

```php
// Via Tinker
php artisan tinker

$user = User::find(1); // ou User::where('email', 'admin@css.tn')->first();
$user->role = 'admin';
$user->save();
```

Ou via migration/seeder:

```php
User::create([
    'first_name' => 'Admin',
    'last_name' => 'CSS',
    'email' => 'admin@css.tn',
    'password' => Hash::make('password'),
    'role' => 'admin',
    'user_type' => 'socios',
]);
```
