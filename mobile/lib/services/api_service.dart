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

  // Generic GET
  Future<Response> get(String path, {Map<String, dynamic>? queryParameters}) {
    return _dio.get(path, queryParameters: queryParameters);
  }

  // Generic POST
  Future<Response> post(String path, {dynamic data}) {
    return _dio.post(path, data: data);
  }
}
