import axios from 'axios';

// Configuration de base de l'API
const API_BASE_URL = import.meta.env.VITE_API_URL || 'http://localhost:8000/api/v1';

// Créer une instance Axios avec configuration
const api = axios.create({
  baseURL: API_BASE_URL,
  headers: {
    'Content-Type': 'application/json',
    'Accept': 'application/json',
  },
  withCredentials: true, // Pour les cookies CSRF
});

// Intercepteur de requête - Ajouter le token d'authentification
api.interceptors.request.use(
  (config) => {
    const token = localStorage.getItem('auth_token');
    if (token) {
      config.headers.Authorization = `Bearer ${token}`;
    }
    return config;
  },
  (error) => {
    return Promise.reject(error);
  }
);

// Intercepteur de réponse - Gérer les erreurs globalement
api.interceptors.response.use(
  (response) => {
    return response.data;
  },
  (error) => {
    // Si erreur 401, déconnecter l'utilisateur
    if (error.response?.status === 401) {
      localStorage.removeItem('auth_token');
      localStorage.removeItem('user');
      window.location.href = '/login';
    }

    // Si erreur 403, rediriger vers upgrade si nécessaire
    if (error.response?.status === 403 && error.response?.data?.upgrade_required) {
      // Gérer le cas où l'utilisateur doit upgrader son compte
      console.warn('Upgrade required:', error.response.data.message);
    }

    return Promise.reject(error.response?.data || error);
  }
);

// ====================
// AUTH SERVICES
// ====================
export const authService = {
  // Inscription
  register: (data) => api.post('/auth/register', data),

  // Connexion
  login: (credentials) => api.post('/auth/login', credentials),

  // Déconnexion
  logout: () => api.post('/auth/logout'),

  // Profil utilisateur
  getProfile: () => api.get('/auth/profile'),

  // Mise à jour du profil
  updateProfile: (data) => api.put('/auth/profile', data),

  // Changement de mot de passe
  changePassword: (data) => api.post('/auth/change-password', data),

  // Vérification Socios
  verifySocios: (data) => api.post('/auth/verify-socios', data),
};

// ====================
// PARTNERS SERVICES
// ====================
export const partnersService = {
  // Liste des catégories
  getCategories: () => api.get('/partners/categories'),

  // Liste des partenaires (avec filtres)
  getPartners: (params = {}) => api.get('/partners', { params }),

  // Détails d'un partenaire
  getPartner: (slug) => api.get(`/partners/${slug}`),

  // Partenaires en vedette
  getFeatured: () => api.get('/partners/featured'),

  // Partenaires à proximité
  getNearby: (latitude, longitude, radius = 10) =>
    api.get('/partners/nearby', { params: { latitude, longitude, radius } }),
};

// ====================
// OFFERS SERVICES
// ====================
export const offersService = {
  // Liste des offres d'un partenaire
  getPartnerOffers: (partnerSlug) => api.get(`/partners/${partnerSlug}/offers`),

  // Détails d'une offre
  getOffer: (offerSlug) => api.get(`/offers/${offerSlug}`),

  // Offres actives
  getActiveOffers: () => api.get('/offers/active'),
};

// ====================
// REDUCTION CODES SERVICES
// ====================
export const codesService = {
  // Générer un code
  generateCode: (offerSlug, type = 'qr') =>
    api.post(`/codes/generate/${offerSlug}`, { type }),

  // Mes codes
  getMyCodes: (params = {}) => api.get('/codes/my-codes', { params }),

  // Valider un code
  validateCode: (code) => api.post('/codes/validate', { code }),

  // Utiliser un code
  useCode: (code, amount) => api.post(`/codes/${code}/use`, { amount }),
};

// ====================
// CONTENT SERVICES
// ====================
export const contentService = {
  // Liste des contenus
  getContent: (params = {}) => api.get('/content', { params }),

  // Contenus en vedette
  getFeatured: () => api.get('/content/featured'),

  // Détails d'un contenu
  getContentDetail: (slug) => api.get(`/content/${slug}`),

  // Liker un contenu
  likeContent: (slug) => api.post(`/content/${slug}/like`),

  // Disliker un contenu
  unlikeContent: (slug) => api.post(`/content/${slug}/unlike`),
};

// ====================
// PLAYERS SERVICES
// ====================
export const playersService = {
  // Liste des joueurs
  getPlayers: (params = {}) => api.get('/players', { params }),

  // Détails d'un joueur
  getPlayer: (slug) => api.get(`/players/${slug}`),

  // Joueurs actifs
  getActivePlayers: () => api.get('/players/active'),
};

// ====================
// MATCHES SERVICES
// ====================
export const matchesService = {
  // Liste des matchs
  getMatches: (params = {}) => api.get('/matches', { params }),

  // Prochains matchs
  getUpcoming: () => api.get('/matches/upcoming'),

  // Résultats récents
  getResults: () => api.get('/matches/results'),

  // Détails d'un match
  getMatch: (id) => api.get(`/matches/${id}`),
};

export default api;
