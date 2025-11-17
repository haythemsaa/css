import React, { useState, useEffect } from 'react';
import { partnersService } from '../../services/api';
import PartnerCard from '../../components/partners/PartnerCard';
import { Button, Badge } from '../../components/common';
import useAuthStore from '../../stores/authStore';

const Partners = () => {
  const { user, isAuthenticated } = useAuthStore();
  const [partners, setPartners] = useState([]);
  const [categories, setCategories] = useState([]);
  const [loading, setLoading] = useState(true);
  const [error, setError] = useState(null);

  // Filtres
  const [filters, setFilters] = useState({
    category: '',
    city: '',
    search: '',
    featured: false,
    nearby: false,
  });

  // Géolocalisation
  const [location, setLocation] = useState(null);
  const [gettingLocation, setGettingLocation] = useState(false);

  // Pagination
  const [pagination, setPagination] = useState({
    currentPage: 1,
    totalPages: 1,
    perPage: 12,
    total: 0,
  });

  // Charger les catégories au montage
  useEffect(() => {
    loadCategories();
  }, []);

  // Charger les partenaires quand les filtres changent
  useEffect(() => {
    loadPartners();
  }, [filters, pagination.currentPage]);

  const loadCategories = async () => {
    try {
      const response = await partnersService.getCategories();
      if (response.success) {
        setCategories(response.data);
      }
    } catch (err) {
      console.error('Erreur chargement catégories:', err);
    }
  };

  const loadPartners = async () => {
    setLoading(true);
    setError(null);

    try {
      const params = {
        page: pagination.currentPage,
        per_page: pagination.perPage,
      };

      // Ajouter les filtres
      if (filters.category) params.category = filters.category;
      if (filters.city) params.city = filters.city;
      if (filters.search) params.search = filters.search;
      if (filters.featured) params.featured = true;

      // Si recherche à proximité et localisation disponible
      if (filters.nearby && location) {
        params.latitude = location.latitude;
        params.longitude = location.longitude;
        params.radius = 10; // 10 km
      }

      const response = await partnersService.getPartners(params);

      if (response.success) {
        setPartners(response.data);
        setPagination((prev) => ({
          ...prev,
          currentPage: response.meta.current_page,
          totalPages: response.meta.last_page,
          total: response.meta.total,
        }));
      }
    } catch (err) {
      setError(err.message || 'Erreur lors du chargement des partenaires');
    } finally {
      setLoading(false);
    }
  };

  const handleFilterChange = (key, value) => {
    setFilters((prev) => ({ ...prev, [key]: value }));
    setPagination((prev) => ({ ...prev, currentPage: 1 })); // Reset à la page 1
  };

  const resetFilters = () => {
    setFilters({
      category: '',
      city: '',
      search: '',
      featured: false,
      nearby: false,
    });
  };

  const getLocation = () => {
    if (!navigator.geolocation) {
      alert('La géolocalisation n\'est pas supportée par votre navigateur');
      return;
    }

    setGettingLocation(true);

    navigator.geolocation.getCurrentPosition(
      (position) => {
        setLocation({
          latitude: position.coords.latitude,
          longitude: position.coords.longitude,
        });
        setGettingLocation(false);
        handleFilterChange('nearby', true);
      },
      (error) => {
        console.error('Erreur géolocalisation:', error);
        alert('Impossible d\'obtenir votre position');
        setGettingLocation(false);
      }
    );
  };

  const cities = ['Sfax', 'Tunis', 'Sousse', 'Monastir', 'Mahdia', 'Gabès'];

  return (
    <div className="bg-gray-50 min-h-screen py-8">
      <div className="container mx-auto px-4">
        {/* Header */}
        <div className="mb-8">
          <div className="flex items-center justify-between mb-4">
            <div>
              <h1 className="text-4xl font-bold text-gray-900 mb-2">
                Partenaires <span className="text-gradient-gold">Freeoui</span>
              </h1>
              <p className="text-gray-600">
                {pagination.total} partenaire{pagination.total > 1 ? 's' : ''} • Jusqu'à 25% de réduction
              </p>
            </div>

            {/* Badge utilisateur */}
            {isAuthenticated && user && (
              <div className="text-right">
                <p className="text-sm text-gray-600 mb-1">Votre réduction</p>
                <Badge variant={user.user_type === 'socios' ? 'secondary' : 'warning'} size="lg">
                  {user.user_type === 'socios' ? 'Jusqu\'à 25%' : user.user_type === 'premium' ? 'Jusqu\'à 15%' : 'Aucune'}
                </Badge>
              </div>
            )}
          </div>

          {/* Message pour utilisateurs Free */}
          {(!isAuthenticated || user?.user_type === 'free') && (
            <div className="bg-gradient-gold p-4 rounded-lg text-center">
              <p className="text-black font-medium mb-2">
                🎁 Devenez Premium pour profiter de réductions exclusives !
              </p>
              <Button variant="primary" size="sm">
                Voir les offres Premium
              </Button>
            </div>
          )}
        </div>

        {/* Filtres */}
        <div className="bg-white rounded-lg shadow-md p-6 mb-8">
          <div className="grid md:grid-cols-4 gap-4">
            {/* Recherche */}
            <div>
              <label className="block text-sm font-medium text-gray-700 mb-2">
                Rechercher
              </label>
              <input
                type="text"
                value={filters.search}
                onChange={(e) => handleFilterChange('search', e.target.value)}
                placeholder="Nom du partenaire..."
                className="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-black"
              />
            </div>

            {/* Catégorie */}
            <div>
              <label className="block text-sm font-medium text-gray-700 mb-2">
                Catégorie
              </label>
              <select
                value={filters.category}
                onChange={(e) => handleFilterChange('category', e.target.value)}
                className="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-black"
              >
                <option value="">Toutes les catégories</option>
                {categories.map((cat) => (
                  <option key={cat.slug} value={cat.slug}>
                    {cat.name_fr}
                  </option>
                ))}
              </select>
            </div>

            {/* Ville */}
            <div>
              <label className="block text-sm font-medium text-gray-700 mb-2">
                Ville
              </label>
              <select
                value={filters.city}
                onChange={(e) => handleFilterChange('city', e.target.value)}
                className="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-black"
              >
                <option value="">Toutes les villes</option>
                {cities.map((city) => (
                  <option key={city} value={city}>
                    {city}
                  </option>
                ))}
              </select>
            </div>

            {/* Actions */}
            <div className="flex flex-col gap-2">
              <label className="block text-sm font-medium text-gray-700 mb-2">
                Options
              </label>
              <Button
                variant="outline"
                size="sm"
                onClick={getLocation}
                loading={gettingLocation}
                disabled={gettingLocation || filters.nearby}
              >
                📍 À proximité
              </Button>
            </div>
          </div>

          {/* Filtres actifs */}
          <div className="flex items-center gap-2 mt-4 flex-wrap">
            <span className="text-sm text-gray-600">Filtres actifs:</span>

            {filters.featured && (
              <Badge variant="secondary">
                En vedette
                <button
                  onClick={() => handleFilterChange('featured', false)}
                  className="ml-2"
                >
                  ×
                </button>
              </Badge>
            )}

            {filters.nearby && (
              <Badge variant="info">
                À proximité
                <button
                  onClick={() => handleFilterChange('nearby', false)}
                  className="ml-2"
                >
                  ×
                </button>
              </Badge>
            )}

            {(filters.category || filters.city || filters.search || filters.featured || filters.nearby) && (
              <button
                onClick={resetFilters}
                className="text-sm text-gray-600 hover:text-black underline ml-2"
              >
                Réinitialiser tous les filtres
              </button>
            )}
          </div>
        </div>

        {/* Liste des partenaires */}
        {loading ? (
          <div className="text-center py-12">
            <div className="inline-block animate-spin rounded-full h-12 w-12 border-b-2 border-black"></div>
            <p className="text-gray-600 mt-4">Chargement des partenaires...</p>
          </div>
        ) : error ? (
          <div className="text-center py-12">
            <p className="text-red-600 mb-4">{error}</p>
            <Button variant="outline" onClick={loadPartners}>
              Réessayer
            </Button>
          </div>
        ) : partners.length === 0 ? (
          <div className="text-center py-12">
            <div className="text-6xl mb-4">🔍</div>
            <h3 className="text-2xl font-bold text-gray-900 mb-2">
              Aucun partenaire trouvé
            </h3>
            <p className="text-gray-600 mb-4">
              Essayez de modifier vos critères de recherche
            </p>
            <Button variant="outline" onClick={resetFilters}>
              Réinitialiser les filtres
            </Button>
          </div>
        ) : (
          <>
            <div className="grid md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6 mb-8">
              {partners.map((partner) => (
                <PartnerCard key={partner.id} partner={partner} />
              ))}
            </div>

            {/* Pagination */}
            {pagination.totalPages > 1 && (
              <div className="flex justify-center items-center gap-2">
                <Button
                  variant="outline"
                  size="sm"
                  onClick={() => setPagination((prev) => ({ ...prev, currentPage: prev.currentPage - 1 }))}
                  disabled={pagination.currentPage === 1}
                >
                  ← Précédent
                </Button>

                <span className="px-4 py-2 text-sm text-gray-600">
                  Page {pagination.currentPage} sur {pagination.totalPages}
                </span>

                <Button
                  variant="outline"
                  size="sm"
                  onClick={() => setPagination((prev) => ({ ...prev, currentPage: prev.currentPage + 1 }))}
                  disabled={pagination.currentPage === pagination.totalPages}
                >
                  Suivant →
                </Button>
              </div>
            )}
          </>
        )}
      </div>
    </div>
  );
};

export default Partners;
