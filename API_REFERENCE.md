# CSS Socios - API Reference Guide

> **Version:** 1.0.0
> **Base URL:** `http://localhost:8000/api/v1`
> **Authentication:** Bearer Token (Laravel Sanctum)

---

## 📋 Table of Contents

1. [Authentication](#authentication)
2. [Admin Endpoints](#admin-endpoints)
3. [Public Endpoints](#public-endpoints)
4. [Error Handling](#error-handling)
5. [Response Format](#response-format)

---

## 🔐 Authentication

### Register
```http
POST /auth/register
```

**Body:**
```json
{
  "first_name": "Ahmed",
  "last_name": "Ben Salem",
  "email": "ahmed@example.com",
  "phone": "+216 20 123 456",
  "password": "secure_password",
  "password_confirmation": "secure_password",
  "date_of_birth": "1990-01-15"
}
```

**Response (201):**
```json
{
  "user": {
    "id": 1,
    "first_name": "Ahmed",
    "last_name": "Ben Salem",
    "email": "ahmed@example.com",
    "user_type": "free"
  },
  "token": "1|abc123...",
  "message": "Registration successful"
}
```

### Login
```http
POST /auth/login
```

**Body:**
```json
{
  "email": "ahmed@example.com",
  "password": "secure_password"
}
```

**Response (200):**
```json
{
  "user": { ... },
  "token": "2|def456...",
  "message": "Login successful"
}
```

### Logout
```http
POST /auth/logout
Authorization: Bearer {token}
```

---

## 🔧 Admin Endpoints

> **Note:** All admin endpoints require authentication AND admin privileges.

### Content Management

#### List All Content
```http
GET /admin/content
Authorization: Bearer {token}
```

**Query Parameters:**
- `type` - Filter by type (article, video, podcast)
- `category_id` - Filter by category
- `access_level` - Filter by access (free, premium, socios)
- `status` - Filter by status (published, draft)
- `search` - Search in title and excerpt
- `sort_by` - Sort field (default: created_at)
- `sort_order` - Sort direction (asc, desc)
- `per_page` - Items per page (default: 20)
- `page` - Page number

**Example:**
```http
GET /admin/content?type=article&status=published&per_page=10
```

**Response (200):**
```json
{
  "data": [
    {
      "id": 1,
      "title": "CSS Victory Against EST",
      "slug": "css-victory-est",
      "type": "article",
      "category": {
        "id": 1,
        "name": "Match Reports"
      },
      "author": {
        "id": 1,
        "name": "Admin CSS"
      },
      "access_level": "free",
      "is_featured": true,
      "published_at": "2025-11-27T10:00:00Z",
      "views_count": 1250,
      "likes_count": 85
    }
  ],
  "links": { ... },
  "meta": { ... }
}
```

#### Create Content
```http
POST /admin/content
Authorization: Bearer {token}
```

**Body:**
```json
{
  "title": "CSS New Signing Announcement",
  "slug": "css-new-signing-2025",
  "type": "article",
  "category_id": 2,
  "excerpt": "Club announces exciting new player",
  "body": "Full article content here...",
  "featured_image": "https://cdn.css.tn/images/signing.jpg",
  "access_level": "free",
  "is_featured": true,
  "published_at": "2025-11-28T15:00:00Z",
  "tags": [1, 2, 5]
}
```

**Response (201):**
```json
{
  "success": true,
  "message": "Contenu créé avec succès",
  "data": { ... }
}
```

#### Update Content
```http
PUT /admin/content/{id}
Authorization: Bearer {token}
```

**Body (partial update):**
```json
{
  "title": "Updated Title",
  "is_featured": false
}
```

**Response (200):**
```json
{
  "success": true,
  "message": "Contenu mis à jour avec succès",
  "data": { ... }
}
```

#### Delete Content
```http
DELETE /admin/content/{id}
Authorization: Bearer {token}
```

**Response (200):**
```json
{
  "success": true,
  "message": "Contenu supprimé avec succès"
}
```

---

### Match Management

#### List All Matches
```http
GET /admin/matches
Authorization: Bearer {token}
```

**Query Parameters:**
- `status` - Filter by status (scheduled, live, halftime, finished, cancelled)
- `competition` - Filter by competition
- `season` - Filter by season
- `search` - Search in teams and competition
- `sort_by` - Sort field (default: match_date)
- `sort_order` - Sort direction (default: desc)

**Example:**
```http
GET /admin/matches?status=scheduled&competition=Ligue 1&season=2024/2025
```

#### Create Match
```http
POST /admin/matches
Authorization: Bearer {token}
```

**Body:**
```json
{
  "home_team": "CSS",
  "away_team": "EST",
  "competition": "Ligue 1",
  "season": "2024/2025",
  "match_date": "2025-12-15 19:00:00",
  "stadium": "Stade Taïeb Mhiri",
  "status": "scheduled"
}
```

**Response (201):**
```json
{
  "message": "Match créé avec succès",
  "match": {
    "id": 1,
    "home_team": "CSS",
    "away_team": "EST",
    "match_date": "2025-12-15T19:00:00Z",
    "status": "scheduled"
  }
}
```

#### Update Match (Live Score Update)
```http
PUT /admin/matches/{id}
Authorization: Bearer {token}
```

**Body:**
```json
{
  "home_score": 2,
  "away_score": 1,
  "status": "finished",
  "home_possession": 55,
  "away_possession": 45,
  "home_shots": 12,
  "away_shots": 8,
  "home_shots_on_target": 6,
  "away_shots_on_target": 3
}
```

---

### Player Management

#### List All Players
```http
GET /admin/players
Authorization: Bearer {token}
```

**Query Parameters:**
- `position` - Filter by position (goalkeeper, defender, midfielder, forward)
- `is_active` - Filter by active status (true/false)
- `search` - Search in name and jersey number

#### Create Player
```http
POST /admin/players
Authorization: Bearer {token}
```

**Body:**
```json
{
  "first_name": "Hamza",
  "last_name": "Rafia",
  "jersey_number": 10,
  "position": "midfielder",
  "date_of_birth": "1999-06-22",
  "nationality": "Tunisia",
  "height": 175,
  "weight": 70,
  "photo_url": "https://cdn.css.tn/players/rafia.jpg",
  "bio": "Talented midfielder from CSS youth academy",
  "is_active": true,
  "goals": 0,
  "assists": 0,
  "matches_played": 0
}
```

**Response (201):**
```json
{
  "message": "Joueur créé avec succès",
  "player": { ... }
}
```

---

### Product Management

#### List All Products
```http
GET /admin/products
Authorization: Bearer {token}
```

**Query Parameters:**
- `category` - Filter by category (jerseys, merchandise, accessories, collectibles)
- `is_available` - Filter by availability
- `is_featured` - Filter by featured status
- `stock_status` - Filter by stock (out_of_stock, low_stock)

#### Create Product
```http
POST /admin/products
Authorization: Bearer {token}
```

**Body:**
```json
{
  "name": "CSS Home Jersey 2024/2025",
  "slug": "css-home-jersey-2024-2025",
  "description": "Official home jersey for the 2024/2025 season",
  "category": "jerseys",
  "price": 89.99,
  "sale_price": 69.99,
  "stock_quantity": 500,
  "sku": "CSS-JER-HOME-2425",
  "images": ["https://cdn.css.tn/products/jersey1.jpg"],
  "is_featured": true,
  "is_available": true,
  "sizes": ["S", "M", "L", "XL", "XXL"],
  "colors": ["Black", "White", "Gold"]
}
```

---

### Event Management

#### List All Events
```http
GET /admin/events
Authorization: Bearer {token}
```

**Query Parameters:**
- `event_type` - Filter by type (match, meet_greet, training, conference, ceremony)
- `status` - Filter by status (upcoming, ongoing, completed, cancelled)
- `is_featured` - Filter by featured status

#### Create Event
```http
POST /admin/events
Authorization: Bearer {token}
```

**Body:**
```json
{
  "title": "Meet & Greet with Players",
  "slug": "meet-greet-december-2025",
  "description": "Exclusive fan event to meet CSS players",
  "event_type": "meet_greet",
  "start_datetime": "2025-12-20 16:00:00",
  "end_datetime": "2025-12-20 18:00:00",
  "location": "Club Headquarters",
  "venue": "CSS Training Center",
  "max_attendees": 100,
  "requires_registration": true,
  "is_featured": true,
  "status": "upcoming"
}
```

---

### Partner Management

#### List All Partners
```http
GET /admin/partners
Authorization: Bearer {token}
```

**Query Parameters:**
- `category_id` - Filter by category
- `is_active` - Filter by active status
- `is_featured` - Filter by featured status
- `city` - Filter by city

#### Create Partner
```http
POST /admin/partners
Authorization: Bearer {token}
```

**Body:**
```json
{
  "name": "Restaurant Le Phare",
  "category_id": 1,
  "description": "Premium seafood restaurant",
  "discount_description": "20% off for CSS Socios members",
  "discount_percentage": 20,
  "address": "Avenue Habib Bourguiba",
  "city": "Sfax",
  "phone": "+216 74 123 456",
  "email": "contact@lephare.tn",
  "website": "https://lephare.tn",
  "logo_url": "https://cdn.partners.tn/lephare.jpg",
  "latitude": 34.7406,
  "longitude": 10.7603,
  "is_active": true,
  "is_featured": false,
  "priority": 5
}
```

---

### Poll Management

#### List All Polls
```http
GET /admin/polls
Authorization: Bearer {token}
```

**Query Parameters:**
- `status` - Filter by status (draft, active, closed)
- `type` - Filter by type (single, multiple, rating, text)
- `visibility` - Filter by visibility (public, premium, socios_only)
- `category` - Filter by category (match, player, transfer, general, club)
- `is_featured` - Filter by featured status

#### Create Poll
```http
POST /admin/polls
Authorization: Bearer {token}
```

**Body:**
```json
{
  "question": "Who was the Man of the Match?",
  "description": "Vote for the best player in CSS vs EST match",
  "type": "single",
  "category": "match",
  "visibility": "public",
  "status": "active",
  "is_featured": true,
  "allow_anonymous": false,
  "show_results_before_vote": false,
  "start_date": "2025-12-01 00:00:00",
  "end_date": "2025-12-03 23:59:59",
  "options": [
    {"text": "Hamza Rafia"},
    {"text": "Kingsley Eduwo"},
    {"text": "Firas Chaouat"}
  ]
}
```

**Response (201):**
```json
{
  "success": true,
  "message": "Sondage créé avec succès",
  "data": {
    "id": 1,
    "question": "Who was the Man of the Match?",
    "options": [...]
  }
}
```

---

### Lottery Management

#### List All Lotteries
```http
GET /admin/lottery
Authorization: Bearer {token}
```

#### Create Lottery Draw
```http
POST /admin/lottery
Authorization: Bearer {token}
```

**Body:**
```json
{
  "name": "Signed Jersey Lottery",
  "description": "Win a jersey signed by the entire team",
  "prize_description": "CSS Home Jersey 2024/2025 - Signed by all players",
  "ticket_price": 5.00,
  "max_tickets": 1000,
  "start_date": "2025-12-01",
  "end_date": "2025-12-15",
  "draw_date": "2025-12-16",
  "is_active": true,
  "image_url": "https://cdn.css.tn/lottery/jersey.jpg"
}
```

---

## 🌐 Public Endpoints

### Content

#### Get Public Content
```http
GET /contents
```

**Query Parameters:**
- `type` - Filter by type
- `category_id` - Filter by category
- `search` - Search query

**Response:**
```json
{
  "data": [
    {
      "id": 1,
      "title": "CSS Match Report",
      "slug": "css-match-report",
      "excerpt": "Complete match analysis...",
      "featured_image": "...",
      "published_at": "2025-11-27T10:00:00Z"
    }
  ]
}
```

#### Get Single Content
```http
GET /contents/{slug}
```

### Matches

#### Get Matches
```http
GET /matches
```

**Query Parameters:**
- `status` - Filter by status
- `competition` - Filter by competition

#### Get Live Match Data
```http
GET /matches/{id}/live
```

**Response:**
```json
{
  "match": { ... },
  "home_score": 2,
  "away_score": 1,
  "status": "live",
  "statistics": {
    "possession": {
      "home": 58,
      "away": 42
    },
    "shots": {
      "home": 14,
      "away": 8
    }
  },
  "events": [
    {
      "minute": 23,
      "type": "goal",
      "team": "home",
      "player": "Hamza Rafia"
    }
  ]
}
```

### Players

#### Get Players
```http
GET /players
```

#### Get Player Details
```http
GET /players/{id}
```

#### Get Player Statistics
```http
GET /players/{id}/statistics
```

**Response:**
```json
{
  "player": { ... },
  "stats": {
    "goals": 12,
    "assists": 8,
    "matches_played": 25,
    "yellow_cards": 3,
    "red_cards": 0,
    "goals_per_match": 0.48
  }
}
```

### Shop

#### Get Products
```http
GET /products
```

**Query Parameters:**
- `category` - Filter by category
- `featured` - Get featured products only
- `sort` - Sort by (price_asc, price_desc, newest, popular)

#### Get Product Details
```http
GET /products/{slug}
```

---

## 🛒 Cart & Orders

### Cart Management

#### Get Cart
```http
GET /cart
Authorization: Bearer {token}
```

#### Add to Cart
```http
POST /cart/add
Authorization: Bearer {token}
```

**Body:**
```json
{
  "product_id": 1,
  "quantity": 2,
  "variant_id": 5
}
```

#### Update Cart Item
```http
PUT /cart/items/{id}
Authorization: Bearer {token}
```

**Body:**
```json
{
  "quantity": 3
}
```

#### Remove from Cart
```http
DELETE /cart/items/{id}
Authorization: Bearer {token}
```

### Order Management

#### Create Order
```http
POST /orders
Authorization: Bearer {token}
```

**Body:**
```json
{
  "shipping_address": {
    "address": "123 Main Street",
    "city": "Sfax",
    "postal_code": "3000",
    "phone": "+216 20 123 456"
  },
  "payment_method": "d17",
  "notes": "Please call before delivery"
}
```

**Response (201):**
```json
{
  "order": {
    "id": 1,
    "order_number": "CSS-2025-00001",
    "status": "pending",
    "total_amount": 159.98,
    "items": [...]
  },
  "payment_url": "https://payment.d17.tn/..."
}
```

---

## 💰 Donations & Auctions

### Donation Goals

#### Get Active Donation Goals
```http
GET /donation-goals
```

#### Donate to Goal
```http
POST /donation-goals/{id}/donate
Authorization: Bearer {token}
```

**Body:**
```json
{
  "amount": 50.00,
  "is_anonymous": false,
  "message": "Kol âam w 'antouma bekhir!",
  "payment_method": "d17"
}
```

### Auctions

#### Get Active Auctions
```http
GET /auctions
```

#### Place Bid
```http
POST /auctions/{id}/bid
Authorization: Bearer {token}
```

**Body:**
```json
{
  "amount": 150.00
}
```

**Response:**
```json
{
  "message": "Bid placed successfully",
  "auction": {
    "id": 1,
    "current_bid": 150.00,
    "highest_bidder": "You"
  }
}
```

---

## 🗳️ Polls & Events

### Polls

#### Get Active Polls
```http
GET /polls
```

#### Vote on Poll
```http
POST /polls/{id}/vote
Authorization: Bearer {token}
```

**Body (Single Choice):**
```json
{
  "option_id": 1
}
```

**Body (Multiple Choice):**
```json
{
  "option_ids": [1, 3, 5]
}
```

**Body (Rating):**
```json
{
  "rating": 8
}
```

### Events

#### Get Upcoming Events
```http
GET /events
```

#### Register for Event
```http
POST /events/{id}/register
Authorization: Bearer {token}
```

**Body:**
```json
{
  "number_of_guests": 2,
  "special_requirements": "Wheelchair accessible"
}
```

---

## ⚠️ Error Handling

### Error Response Format

All errors follow this structure:

```json
{
  "message": "Error description",
  "errors": {
    "field_name": [
      "Validation error message"
    ]
  }
}
```

### HTTP Status Codes

- `200` - Success
- `201` - Created
- `400` - Bad Request
- `401` - Unauthorized
- `403` - Forbidden
- `404` - Not Found
- `422` - Validation Error
- `500` - Server Error

### Common Errors

**Unauthorized:**
```json
{
  "message": "Unauthenticated."
}
```

**Forbidden:**
```json
{
  "message": "This action is unauthorized."
}
```

**Validation Error:**
```json
{
  "message": "The given data was invalid.",
  "errors": {
    "email": [
      "The email field is required."
    ],
    "password": [
      "The password must be at least 8 characters."
    ]
  }
}
```

---

## 📝 Response Format

### Pagination

All list endpoints return paginated results:

```json
{
  "data": [...],
  "links": {
    "first": "http://api.css.tn/v1/resource?page=1",
    "last": "http://api.css.tn/v1/resource?page=10",
    "prev": null,
    "next": "http://api.css.tn/v1/resource?page=2"
  },
  "meta": {
    "current_page": 1,
    "from": 1,
    "last_page": 10,
    "per_page": 20,
    "to": 20,
    "total": 200
  }
}
```

### Success Response

```json
{
  "success": true,
  "message": "Operation successful",
  "data": { ... }
}
```

---

## 🔑 Rate Limiting

- **Public endpoints:** 60 requests per minute
- **Authenticated endpoints:** 120 requests per minute
- **Admin endpoints:** 180 requests per minute

Rate limit headers:
```
X-RateLimit-Limit: 60
X-RateLimit-Remaining: 58
X-RateLimit-Reset: 1701360000
```

---

## 📞 Support

For API support and questions:
- **Email:** dev@css.tn
- **Documentation:** https://docs.css.tn
- **GitHub:** https://github.com/css/api

---

**Last Updated:** 2025-11-27
**API Version:** 1.0.0
