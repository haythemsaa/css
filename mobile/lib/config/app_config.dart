class AppConfig {
  static const String appName = 'CSS Club';
  static const String apiBaseUrl = 'http://localhost:8000/api/v1';

  // API Endpoints
  static const String authEndpoint = '$apiBaseUrl/auth';
  static const String contentsEndpoint = '$apiBaseUrl/contents';
  static const String matchesEndpoint = '$apiBaseUrl/matches';
  static const String playersEndpoint = '$apiBaseUrl/players';
  static const String partnersEndpoint = '$apiBaseUrl/partners';
  static const String giftsEndpoint = '$apiBaseUrl/gifts';
  static const String lotteryEndpoint = '$apiBaseUrl/lottery';

  // App Settings
  static const int requestTimeout = 30; // seconds
  static const int cacheExpiration = 3600; // seconds

  // Pagination
  static const int itemsPerPage = 20;

  // Video Player
  static const int videoBufferDuration = 5; // seconds
  static const int maxVideoQuality = 1080; // pixels

  // Freeoui
  static const int codeExpirationMinutes = 15;
  static const double defaultGeofenceRadius = 0.5; // km

  // Social Media
  static const String facebookPageUrl = 'https://facebook.com/CSSofficiel';
  static const String twitterUrl = 'https://twitter.com/CSsfaxien';
  static const String instagramUrl = 'https://instagram.com/css_officiel';
  static const String youtubeUrl = 'https://youtube.com/CSSOfficiel';
}
