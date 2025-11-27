import 'package:dio/dio.dart';
import '../config/app_config.dart';

class ApiService {
  static final ApiService _instance = ApiService._internal();
  factory ApiService() => _instance;

  late Dio _dio;
  String? _token;

  ApiService._internal() {
    _dio = Dio(BaseOptions(
      baseUrl: AppConfig.apiBaseUrl,
      connectTimeout: Duration(seconds: AppConfig.requestTimeout),
      receiveTimeout: Duration(seconds: AppConfig.requestTimeout),
      headers: {
        'Content-Type': 'application/json',
        'Accept': 'application/json',
      },
    ));

    // Interceptors
    _dio.interceptors.add(InterceptorsWrapper(
      onRequest: (options, handler) {
        if (_token != null) {
          options.headers['Authorization'] = 'Bearer $_token';
        }
        return handler.next(options);
      },
      onError: (error, handler) {
        // Handle errors globally
        print('API Error: ${error.message}');
        return handler.next(error);
      },
    ));
  }

  void setToken(String token) {
    _token = token;
  }

  void clearToken() {
    _token = null;
  }

  // Auth endpoints
  Future<Response> login(String email, String password) {
    return _dio.post('/auth/login', data: {
      'email': email,
      'password': password,
    });
  }

  Future<Response> register(Map<String, dynamic> data) {
    return _dio.post('/auth/register', data: data);
  }

  Future<Response> logout() {
    return _dio.post('/auth/logout');
  }

  // Content endpoints
  Future<Response> getContents({
    String? type,
    int? categoryId,
    int page = 1,
  }) {
    return _dio.get('/contents', queryParameters: {
      if (type != null) 'type': type,
      if (categoryId != null) 'category_id': categoryId,
      'page': page,
    });
  }

  Future<Response> getContent(String slug) {
    return _dio.get('/contents/$slug');
  }

  // Partners endpoints
  Future<Response> getPartners({
    int? categoryId,
    String? city,
    int page = 1,
  }) {
    return _dio.get('/partners', queryParameters: {
      if (categoryId != null) 'category_id': categoryId,
      if (city != null) 'city': city,
      'page': page,
    });
  }

  Future<Response> getNearbyPartners(double lat, double lng, {double radius = 5}) {
    return _dio.get('/partners/nearby', queryParameters: {
      'latitude': lat,
      'longitude': lng,
      'radius': radius,
    });
  }

  // Reduction codes
  Future<Response> generateReductionCode(int partnerId, {int? offerId}) {
    return _dio.post('/reductions/generate', data: {
      'partner_id': partnerId,
      if (offerId != null) 'offer_id': offerId,
    });
  }

  Future<Response> getActiveReductionCodes() {
    return _dio.get('/reductions/active');
  }

  // Matches
  Future<Response> getMatches({String? status}) {
    return _dio.get('/matches', queryParameters: {
      if (status != null) 'status': status,
    });
  }

  Future<Response> getLiveMatch(int matchId) {
    return _dio.get('/matches/$matchId/live');
  }

  // Donations
  Future<Response> makeDonation(Map<String, dynamic> data) {
    return _dio.post('/donations', data: data);
  }

  // =======================
  // AUCTION ENDPOINTS
  // =======================
  Future<Response> getAuctions({
    String? status,
    String? category,
    bool? featured,
    int page = 1,
  }) {
    return _dio.get('/auctions', queryParameters: {
      if (status != null) 'status': status,
      if (category != null) 'category': category,
      if (featured != null) 'featured': featured,
      'page': page,
    });
  }

  Future<Response> getAuctionDetails(int id) {
    return _dio.get('/auctions/$id');
  }

  Future<Response> placeBid(int auctionId, Map<String, dynamic> data) {
    return _dio.post('/auctions/$auctionId/bid', data: data);
  }

  Future<Response> buyNow(int auctionId) {
    return _dio.post('/auctions/$auctionId/buy-now');
  }

  Future<Response> getMyBids({int page = 1}) {
    return _dio.get('/auctions/my-bids', queryParameters: {'page': page});
  }

  Future<Response> getMyWins({int page = 1}) {
    return _dio.get('/auctions/my-wins', queryParameters: {'page': page});
  }

  // =======================
  // DONATION GOAL ENDPOINTS
  // =======================
  Future<Response> getDonationGoals({
    String? status,
    String? category,
    String? priority,
    bool? featured,
    int page = 1,
  }) {
    return _dio.get('/donation-goals', queryParameters: {
      if (status != null) 'status': status,
      if (category != null) 'category': category,
      if (priority != null) 'priority': priority,
      if (featured != null) 'featured': featured,
      'page': page,
    });
  }

  Future<Response> getDonationGoalDetails(String slug) {
    return _dio.get('/donation-goals/$slug');
  }

  Future<Response> donateToGoal(int goalId, Map<String, dynamic> data) {
    return _dio.post('/donation-goals/$goalId/donate', data: data);
  }

  Future<Response> getMyDonations({int page = 1}) {
    return _dio.get('/donation-goals/my-donations', queryParameters: {'page': page});
  }

  Future<Response> getDonationCategories() {
    return _dio.get('/donation-goals/categories');
  }

  // =======================
  // PAYMENT METHOD ENDPOINTS
  // =======================
  Future<Response> getPaymentMethods({String? context, String? type}) {
    return _dio.get('/payment-methods', queryParameters: {
      if (context != null) 'context': context,
      if (type != null) 'type': type,
    });
  }

  Future<Response> getPaymentMethodDetails(int id) {
    return _dio.get('/payment-methods/$id');
  }

  Future<Response> calculatePaymentFees(int methodId, double amount) {
    return _dio.post('/payment-methods/$methodId/calculate-fees', data: {
      'amount': amount,
    });
  }

  // =======================
  // CART ENDPOINTS
  // =======================
  Future<Response> getCart() {
    return _dio.get('/cart');
  }

  Future<Response> addToCart(int productId, int quantity) {
    return _dio.post('/cart/items', data: {
      'product_id': productId,
      'quantity': quantity,
    });
  }

  Future<Response> updateCartItem(int productId, int quantity) {
    return _dio.put('/cart/items/$productId', data: {
      'quantity': quantity,
    });
  }

  Future<Response> removeCartItem(int productId) {
    return _dio.delete('/cart/items/$productId');
  }

  Future<Response> clearCart() {
    return _dio.post('/cart/clear');
  }

  // =======================
  // WISHLIST ENDPOINTS
  // =======================
  Future<Response> getWishlist() {
    return _dio.get('/wishlist');
  }

  Future<Response> addToWishlist(int productId) {
    return _dio.post('/wishlist', data: {
      'product_id': productId,
    });
  }

  Future<Response> removeFromWishlist(int productId) {
    return _dio.delete('/wishlist/$productId');
  }

  Future<Response> toggleWishlist(int productId) {
    return _dio.post('/wishlist/toggle', data: {
      'product_id': productId,
    });
  }

  // =======================
  // POLL ENDPOINTS
  // =======================
  Future<Response> getPolls({
    String? status,
    String? category,
    bool? featured,
    int page = 1,
  }) {
    return _dio.get('/polls', queryParameters: {
      if (status != null) 'status': status,
      if (category != null) 'category': category,
      if (featured != null) 'featured': featured,
      'page': page,
    });
  }

  Future<Response> getPollDetails(int id) {
    return _dio.get('/polls/$id');
  }

  Future<Response> votePoll(int pollId, Map<String, dynamic> voteData) {
    return _dio.post('/polls/$pollId/vote', data: voteData);
  }

  Future<Response> getPollResults(int pollId) {
    return _dio.get('/polls/$pollId/results');
  }

  // =======================
  // TICKET MARKETPLACE ENDPOINTS
  // =======================
  Future<Response> getMarketplaceListings({
    int? matchId,
    String? category,
    double? minPrice,
    double? maxPrice,
    bool? featured,
    String sortBy = 'listed_at',
    String sortOrder = 'desc',
    int page = 1,
  }) {
    return _dio.get('/marketplace/listings', queryParameters: {
      if (matchId != null) 'match_id': matchId,
      if (category != null) 'category': category,
      if (minPrice != null) 'min_price': minPrice,
      if (maxPrice != null) 'max_price': maxPrice,
      if (featured != null) 'featured': featured,
      'sort_by': sortBy,
      'sort_order': sortOrder,
      'page': page,
    });
  }

  Future<Response> getListingDetails(int id) {
    return _dio.get('/marketplace/listings/$id');
  }

  Future<Response> createListing(Map<String, dynamic> data) {
    return _dio.post('/marketplace/listings', data: data);
  }

  Future<Response> updateListing(int id, Map<String, dynamic> data) {
    return _dio.put('/marketplace/listings/$id', data: data);
  }

  Future<Response> reserveListing(int id) {
    return _dio.post('/marketplace/listings/$id/reserve');
  }

  Future<Response> purchaseListing(int id, Map<String, dynamic> data) {
    return _dio.post('/marketplace/listings/$id/purchase', data: data);
  }

  Future<Response> cancelListing(int id) {
    return _dio.delete('/marketplace/listings/$id/cancel');
  }

  Future<Response> getMyListings({int page = 1}) {
    return _dio.get('/marketplace/my-listings', queryParameters: {'page': page});
  }

  Future<Response> getMyPurchases({int page = 1}) {
    return _dio.get('/marketplace/my-purchases', queryParameters: {'page': page});
  }

  Future<Response> submitMarketplaceReview(int transactionId, Map<String, dynamic> data) {
    return _dio.post('/marketplace/transactions/$transactionId/review', data: data);
  }

  Future<Response> getUserReviews(int userId, {int page = 1}) {
    return _dio.get('/marketplace/users/$userId/reviews', queryParameters: {'page': page});
  }

  // =======================
  // FAN TOKENS & REWARDS ENDPOINTS
  // =======================
  Future<Response> getTokenWallet() {
    return _dio.get('/tokens/wallet');
  }

  Future<Response> getTokenTransactions({int page = 1}) {
    return _dio.get('/tokens/transactions', queryParameters: {'page': page});
  }

  Future<Response> claimDailyBonus() {
    return _dio.post('/tokens/daily-bonus');
  }

  Future<Response> getTokenLeaderboard({int? year, int? month}) {
    return _dio.get('/tokens/leaderboard', queryParameters: {
      if (year != null) 'year': year,
      if (month != null) 'month': month,
    });
  }

  Future<Response> getRewards({
    String? category,
    bool? featured,
    double? minCost,
    double? maxCost,
    bool? affordable,
    String sortBy = 'popularity_score',
    String sortOrder = 'desc',
    int page = 1,
  }) {
    return _dio.get('/rewards', queryParameters: {
      if (category != null) 'category': category,
      if (featured != null) 'featured': featured,
      if (minCost != null) 'min_cost': minCost,
      if (maxCost != null) 'max_cost': maxCost,
      if (affordable != null) 'affordable': affordable,
      'sort_by': sortBy,
      'sort_order': sortOrder,
      'page': page,
    });
  }

  Future<Response> getRewardDetails(int id) {
    return _dio.get('/rewards/$id');
  }

  Future<Response> redeemReward(int id, Map<String, dynamic> data) {
    return _dio.post('/rewards/$id/redeem', data: data);
  }

  Future<Response> getMyRedemptions({int page = 1}) {
    return _dio.get('/redemptions', queryParameters: {'page': page});
  }

  Future<Response> getRedemptionDetails(int id) {
    return _dio.get('/redemptions/$id');
  }

  Future<Response> cancelRedemption(int id) {
    return _dio.delete('/redemptions/$id');
  }

  // ===================================
  // Badges & Achievements
  // ===================================

  Future<Response> getBadges({
    String? category,
    String? rarity,
    bool? unlockedOnly,
    bool? lockedOnly,
  }) {
    return _dio.get('/badges', queryParameters: {
      if (category != null) 'category': category,
      if (rarity != null) 'rarity': rarity,
      if (unlockedOnly != null) 'unlocked_only': unlockedOnly,
      if (lockedOnly != null) 'locked_only': lockedOnly,
    });
  }

  Future<Response> getBadgeDetails(int id) {
    return _dio.get('/badges/$id');
  }

  Future<Response> getBadgeSummary() {
    return _dio.get('/badges/summary');
  }

  Future<Response> getBadgesByCategory() {
    return _dio.get('/badges/categories');
  }

  Future<Response> getBadgeLeaderboard({int limit = 50}) {
    return _dio.get('/badges/leaderboard', queryParameters: {'limit': limit});
  }

  Future<Response> unlockBadge(int id) {
    return _dio.post('/badges/$id/unlock');
  }

  Future<Response> updateBadgeProgress(int id, int progress) {
    return _dio.post('/badges/$id/progress', data: {'progress': progress});
  }

  // ===================================
  // Predictions
  // ===================================

  Future<Response> getAvailableMatchesForPrediction() {
    return _dio.get('/predictions/matches');
  }

  Future<Response> submitPrediction(int matchId, int homeScore, int awayScore, {int? firstScorerId}) {
    return _dio.post('/predictions/matches/$matchId', data: {
      'predicted_home_score': homeScore,
      'predicted_away_score': awayScore,
      if (firstScorerId != null) 'predicted_first_scorer_id': firstScorerId,
    });
  }

  Future<Response> getMyPredictions({int page = 1}) {
    return _dio.get('/predictions/my-predictions', queryParameters: {'page': page});
  }

  Future<Response> getMyPredictionStats() {
    return _dio.get('/predictions/my-stats');
  }

  Future<Response> getPredictionLeaderboard({int limit = 50}) {
    return _dio.get('/predictions/leaderboard', queryParameters: {'limit': limit});
  }

  // Generic GET
  Future<Response> get(String path, {Map<String, dynamic>? queryParameters}) {
    return _dio.get(path, queryParameters: queryParameters);
  }

  // Generic POST
  Future<Response> post(String path, {dynamic data}) {
    return _dio.post(path, data: data);
  }
}
