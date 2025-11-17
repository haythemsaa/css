// Configuration constants

// API Configuration
// Change this to your production API URL when deploying
export const API_BASE_URL = 'http://localhost:8000/api/v1';

// For testing with physical device on same network:
// export const API_BASE_URL = 'http://192.168.1.X:8000/api/v1';

// For production:
// export const API_BASE_URL = 'https://api.css.tn/api/v1';

export const API_TIMEOUT = 30000; // 30 seconds

// Storage Keys
export const STORAGE_KEYS = {
  AUTH_TOKEN: '@css_auth_token',
  USER_DATA: '@css_user_data',
  LANGUAGE: '@css_language',
  THEME: '@css_theme',
};

// App Configuration
export const APP_CONFIG = {
  name: 'CSS Platform',
  version: '1.0.0',
  defaultLanguage: 'fr',
  supportEmail: 'support@css.tn',
};

// Pagination
export const PAGINATION = {
  defaultPageSize: 20,
  maxPageSize: 100,
};

// QR Code Scanner
export const QR_SCANNER = {
  aspectRatio: 16 / 9,
  barCodeTypes: ['qr', 'code128', 'code39'],
};

// Geolocation
export const GEOLOCATION = {
  accuracy: 6, // Best accuracy
  distanceFilter: 100, // meters
  defaultRadius: 10, // km
  maxRadius: 50, // km
};

export default {
  API_BASE_URL,
  API_TIMEOUT,
  STORAGE_KEYS,
  APP_CONFIG,
  PAGINATION,
  QR_SCANNER,
  GEOLOCATION,
};
