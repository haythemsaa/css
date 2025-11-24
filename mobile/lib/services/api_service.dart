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

  // Generic GET
  Future<Response> get(String path, {Map<String, dynamic>? queryParameters}) {
    return _dio.get(path, queryParameters: queryParameters);
  }

  // Generic POST
  Future<Response> post(String path, {dynamic data}) {
    return _dio.post(path, data: data);
  }
}
