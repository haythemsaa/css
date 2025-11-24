# API Documentation - CSS Socios Backend

## Base URL
```
http://your-domain.com/api/v1
```

## Authentication
All protected endpoints require Bearer token authentication using Laravel Sanctum.

```
Authorization: Bearer {token}
```

---

## Public Endpoints (No Authentication)

### Authentication
- `POST /auth/register` - Register new user
- `POST /auth/login` - Login user
- `POST /auth/verify-otp` - Verify OTP code
- `POST /auth/forgot-password` - Request password reset
- `POST /auth/reset-password` - Reset password

### Content
- `GET /contents` - List all content (articles, videos, podcasts)
- `GET /contents/{slug}` - Get single content by slug
- `GET /contents/featured` - Get featured content
- `GET /contents/trending` - Get trending content

### Matches
- `GET /matches` - List all matches
- `GET /matches/{id}` - Get match details
- `GET /matches/{id}/live` - Get live match updates
- `GET /standings` - Get league standings

### Players
- `GET /players` - List all players
- `GET /players/{id}` - Get player details

### Teams
- `GET /teams` - List all teams
- `GET /teams/{id}` - Get team details

### Campaigns (Donations)
- `GET /campaigns` - List donation campaigns
- `GET /campaigns/{slug}` - Get campaign details

### Partners (Freeoui)
- `GET /partners` - List all partners
- `GET /partners/{id}` - Get partner details
- `GET /partners/categories` - Get partner categories
- `GET /partners/nearby?lat={lat}&lng={lng}&radius={km}` - Find nearby partners

### Leaderboards
- `GET /leaderboards?type={type}&period={period}` - Get leaderboard rankings
  - Types: `points`, `donations`, `engagement`, `social`
  - Periods: `daily`, `weekly`, `monthly`, `all_time`
- `GET /leaderboards/stats` - Get leaderboard statistics

### Challenges
- `GET /challenges?type={type}&category={category}` - List challenges
  - Types: `daily`, `weekly`, `monthly`, `special`, `seasonal`
  - Categories: `engagement`, `social`, `donation`, `content`, `match`
- `GET /challenges/{id}` - Get challenge details

### Products (E-commerce)
- `GET /products?category={category}&sort={sort}` - List products
  - Categories: `jerseys`, `merchandise`, `accessories`, `collectibles`
  - Sort: `popular`, `newest`, `price_asc`, `price_desc`, `rating`
- `GET /products/{id}` - Get product details
- `GET /products/{id}/reviews` - Get product reviews

### Tickets
- `GET /matches/{matchId}/tickets?category={category}` - Get tickets for match
  - Categories: `vip`, `tribune`, `pelouse`, `family`

### Ticket Verification (Public for gate staff)
- `POST /tickets/verify-qr` - Verify ticket QR code
- `POST /tickets/mark-used` - Mark ticket as used

---

## Protected Endpoints (Authentication Required)

### User Profile
- `GET /user/profile` - Get current user profile
- `PUT /user/profile` - Update user profile
- `POST /user/change-password` - Change password
- `DELETE /user/account` - Delete account
- `POST /auth/logout` - Logout user

### Content Interactions
- `POST /contents/{id}/like` - Like content
- `POST /contents/{id}/unlike` - Unlike content
- `POST /contents/{id}/share` - Share content

### Donations
- `POST /donations` - Make a donation
- `GET /donations/history` - Get donation history
- `GET /donations/stats` - Get donation statistics
- `GET /donations/{id}/certificate` - Get donation certificate

### Partners
- `POST /partners/{id}/favorite` - Favorite partner
- `DELETE /partners/{id}/unfavorite` - Unfavorite partner
- `POST /partners/{id}/review` - Add partner review

### Socios (Premium Members)
- `GET /socios/benefits` - Get Socios benefits
- `POST /socios/benefits/{id}/redeem` - Redeem benefit
- `GET /socios/points-history` - Get points history

### Forum
- `GET /forum/topics` - List forum topics
- `GET /forum/topics/{id}` - Get topic details
- `POST /forum/topics` - Create new topic
- `POST /forum/topics/{id}/reply` - Reply to topic
- `DELETE /forum/topics/{id}` - Delete topic
- `DELETE /forum/replies/{id}` - Delete reply

### Polls
- `GET /polls` - List polls
- `GET /polls/{id}` - Get poll details
- `POST /polls/{id}/vote` - Vote on poll
- `GET /polls/{id}/results` - Get poll results

### Notifications
- `GET /notifications` - List user notifications
- `POST /notifications/{id}/read` - Mark notification as read
- `POST /notifications/read-all` - Mark all as read
- `POST /notifications/device-token` - Register device for push notifications

### Badges & Gamification
- `GET /badges/all` - List all badges
- `GET /badges/my-badges` - Get user's earned badges

### Lottery
- `GET /lottery/active` - Get active lottery draws
- `POST /lottery/{id}/buy-ticket` - Buy lottery ticket
- `GET /lottery/my-tickets` - Get user's lottery tickets
- `GET /lottery/{id}/winners` - Get lottery winners

### Gifts
- `GET /gifts/available` - Get available gifts
- `POST /gifts/{id}/claim` - Claim a gift
- `GET /gifts/my-gifts` - Get user's claimed gifts

### Collectible Cards
- `GET /cards/available` - Get available cards
- `GET /cards/my-collection` - Get user's card collection
- `POST /cards/{id}/acquire` - Acquire a card
- `POST /cards/trade` - Propose card trade
- `POST /cards/trade/{id}/accept` - Accept trade
- `POST /cards/trade/{id}/reject` - Reject trade

### Leaderboards (User Actions)
- `GET /leaderboards/my-rank?type={type}&period={period}` - Get user's rank
- `GET /leaderboards/compare/{userId}?type={type}` - Compare with another user

### Challenges (User Actions)
- `POST /challenges/{id}/enroll` - Enroll in challenge
- `GET /challenges/my-challenges?status={status}` - Get user's challenges
  - Status: `completed`, `in_progress`, `not_started`, `all`
- `PATCH /challenges/progress/{userChallengeId}` - Update challenge progress
- `POST /challenges/claim/{userChallengeId}` - Claim challenge reward

### E-commerce - Products
- `POST /products/{id}/reviews` - Add product review

### E-commerce - Cart
- `GET /cart` - Get user's cart
- `POST /cart/items` - Add item to cart
- `PATCH /cart/items/{productId}` - Update item quantity
- `DELETE /cart/items/{productId}` - Remove item from cart
- `DELETE /cart` - Clear cart

### E-commerce - Orders
- `GET /orders?status={status}` - List user orders
  - Status: `pending`, `confirmed`, `processing`, `shipped`, `delivered`, `cancelled`
- `POST /orders` - Create new order
- `GET /orders/{id}` - Get order details
- `DELETE /orders/{id}/cancel` - Cancel order

### Ticketing
- `POST /tickets/purchase` - Purchase tickets
- `GET /tickets/my-tickets?status={status}` - Get user's tickets
  - Status: `pending`, `confirmed`, `used`, `cancelled`
- `GET /tickets/purchases/{id}` - Get ticket purchase details
- `DELETE /tickets/purchases/{id}/cancel` - Cancel ticket purchase (max 24h before match)

---

## Response Format

### Success Response
```json
{
  "data": {
    // Resource data
  },
  "message": "Success message"
}
```

### Error Response
```json
{
  "message": "Error message",
  "errors": {
    "field": ["Validation error message"]
  }
}
```

### Pagination Response
```json
{
  "data": [],
  "links": {
    "first": "...",
    "last": "...",
    "prev": null,
    "next": "..."
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

---

## HTTP Status Codes

- `200` - OK
- `201` - Created
- `400` - Bad Request
- `401` - Unauthorized
- `403` - Forbidden
- `404` - Not Found
- `422` - Unprocessable Entity (Validation Error)
- `500` - Internal Server Error

---

## Rate Limiting

API requests are rate-limited to:
- **60 requests per minute** for authenticated users
- **30 requests per minute** for guest users

---

## Payment Methods Supported

E-commerce and Ticketing support the following payment gateways:
- **D17** (Tunisian payment gateway)
- **Konnect** (Mobile payments)
- **Paymee** (Digital wallet)
- **Sadad** (Online banking)
- **Stripe** (International cards)

---

## Health Check

```
GET /health
```

Response:
```json
{
  "status": "ok",
  "timestamp": "2024-11-24T10:30:00Z"
}
```

---

## Notes

1. All dates are in ISO 8601 format (YYYY-MM-DDTHH:MM:SSZ)
2. All amounts are in Tunisian Dinars (TND)
3. QR codes expire after 15 minutes for partner reductions
4. Ticket QR codes are permanent and unique per purchase
5. Users can only cancel orders/tickets in `pending` or `confirmed` status
6. Ticket cancellation allowed up to 24 hours before match start
7. Product stock is managed automatically on order creation/cancellation
8. Leaderboards update daily at midnight
9. Challenge progress can be updated by external events or user actions

---

## Support

For API support, contact: support@css-socios.tn
