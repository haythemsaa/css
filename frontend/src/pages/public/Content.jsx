import React, { useState, useEffect } from 'react';
import { contentService } from '../../services/api';
import ContentCard from '../../components/content/ContentCard';
import { Button, Badge } from '../../components/common';
import useAuthStore from '../../stores/authStore';

const Content = () => {
  const { user, isAuthenticated } = useAuthStore();
  const [contents, setContents] = useState([]);
  const [loading, setLoading] = useState(true);
  const [error, setError] = useState(null);

  // Filtres
  const [filters, setFilters] = useState({
    type: '',
    is_premium: '',
    is_featured: false,
    search: '',
  });

  // Pagination
  const [pagination, setPagination] = useState({
    currentPage: 1,
    totalPages: 1,
    perPage: 12,
    total: 0,
  });

  useEffect(() => {
    loadContents();
  }, [filters, pagination.currentPage]);

  const loadContents = async () => {
    setLoading(true);
    setError(null);

    try {
      const params = {
        page: pagination.currentPage,
        per_page: pagination.perPage,
      };

      if (filters.type) params.type = filters.type;
      if (filters.is_premium !== '') params.is_premium = filters.is_premium;
      if (filters.is_featured) params.is_featured = true;
      if (filters.search) params.search = filters.search;

      const response = await contentService.getContent(params);

      if (response.success) {
        setContents(response.data);
        setPagination((prev) => ({
          ...prev,
          currentPage: response.meta.current_page,
          totalPages: response.meta.last_page,
          total: response.meta.total,
        }));
      }
    } catch (err) {
      setError(err.message || 'Erreur lors du chargement des contenus');
    } finally {
      setLoading(false);
    }
  };

  const handleFilterChange = (key, value) => {
    setFilters((prev) => ({ ...prev, [key]: value }));
    setPagination((prev) => ({ ...prev, currentPage: 1 }));
  };

  const resetFilters = () => {
    setFilters({
      type: '',
      is_premium: '',
      is_featured: false,
      search: '',
    });
  };

  const contentTypes = [
    { value: 'article', label: '📰 Articles', icon: '📰' },
    { value: 'video', label: '🎥 Vidéos', icon: '🎥' },
    { value: 'gallery', label: '📷 Galeries', icon: '📷' },
    { value: 'podcast', label: '🎙️ Podcasts', icon: '🎙️' },
  ];

  return (
    <div className="bg-gray-50 min-h-screen py-8">
      <div className="container mx-auto px-4">
        {/* Header */}
        <div className="mb-8">
          <h1 className="text-4xl font-bold text-gray-900 mb-2">
            Actualités <span className="text-gradient-gold">CSS</span>
          </h1>
          <p className="text-gray-600">
            {pagination.total} contenu{pagination.total > 1 ? 's' : ''} disponible{pagination.total > 1 ? 's' : ''}
          </p>
        </div>

        {/* Quick filters - Type buttons */}
        <div className="mb-6">
          <div className="flex flex-wrap gap-3">
            <button
              onClick={() => handleFilterChange('type', '')}
              className={`px-4 py-2 rounded-lg font-medium transition-all ${
                filters.type === ''
                  ? 'bg-black text-white'
                  : 'bg-white text-gray-700 hover:bg-gray-100 border border-gray-300'
              }`}
            >
              Tous
            </button>
            {contentTypes.map((type) => (
              <button
                key={type.value}
                onClick={() => handleFilterChange('type', type.value)}
                className={`px-4 py-2 rounded-lg font-medium transition-all ${
                  filters.type === type.value
                    ? 'bg-black text-white'
                    : 'bg-white text-gray-700 hover:bg-gray-100 border border-gray-300'
                }`}
              >
                {type.label}
              </button>
            ))}
          </div>
        </div>

        {/* Advanced Filters */}
        <div className="bg-white rounded-lg shadow-md p-6 mb-8">
          <div className="grid md:grid-cols-3 gap-4">
            {/* Recherche */}
            <div>
              <label className="block text-sm font-medium text-gray-700 mb-2">
                Rechercher
              </label>
              <input
                type="text"
                value={filters.search}
                onChange={(e) => handleFilterChange('search', e.target.value)}
                placeholder="Titre, description..."
                className="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-black"
              />
            </div>

            {/* Accès */}
            <div>
              <label className="block text-sm font-medium text-gray-700 mb-2">
                Accès
              </label>
              <select
                value={filters.is_premium}
                onChange={(e) => handleFilterChange('is_premium', e.target.value)}
                className="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-black"
              >
                <option value="">Tous</option>
                <option value="0">Gratuit</option>
                <option value="1">Premium</option>
              </select>
            </div>

            {/* Options */}
            <div className="flex items-end">
              <label className="flex items-center cursor-pointer">
                <input
                  type="checkbox"
                  checked={filters.is_featured}
                  onChange={(e) => handleFilterChange('is_featured', e.target.checked)}
                  className="h-4 w-4 text-black focus:ring-black border-gray-300 rounded"
                />
                <span className="ml-2 text-sm font-medium text-gray-700">
                  🔥 En vedette uniquement
                </span>
              </label>
            </div>
          </div>

          {/* Active filters */}
          {(filters.type || filters.is_premium !== '' || filters.is_featured || filters.search) && (
            <div className="flex items-center gap-2 mt-4 pt-4 border-t border-gray-200">
              <span className="text-sm text-gray-600">Filtres actifs:</span>
              {filters.type && (
                <Badge variant="info">
                  {contentTypes.find((t) => t.value === filters.type)?.label}
                  <button onClick={() => handleFilterChange('type', '')} className="ml-2">×</button>
                </Badge>
              )}
              {filters.is_premium === '1' && (
                <Badge variant="secondary">
                  Premium
                  <button onClick={() => handleFilterChange('is_premium', '')} className="ml-2">×</button>
                </Badge>
              )}
              {filters.is_premium === '0' && (
                <Badge variant="success">
                  Gratuit
                  <button onClick={() => handleFilterChange('is_premium', '')} className="ml-2">×</button>
                </Badge>
              )}
              {filters.is_featured && (
                <Badge variant="warning">
                  En vedette
                  <button onClick={() => handleFilterChange('is_featured', false)} className="ml-2">×</button>
                </Badge>
              )}
              <button
                onClick={resetFilters}
                className="text-sm text-gray-600 hover:text-black underline ml-2"
              >
                Réinitialiser
              </button>
            </div>
          )}
        </div>

        {/* Liste des contenus */}
        {loading ? (
          <div className="text-center py-12">
            <div className="inline-block animate-spin rounded-full h-12 w-12 border-b-2 border-black"></div>
            <p className="text-gray-600 mt-4">Chargement...</p>
          </div>
        ) : error ? (
          <div className="text-center py-12">
            <p className="text-red-600 mb-4">{error}</p>
            <Button variant="outline" onClick={loadContents}>
              Réessayer
            </Button>
          </div>
        ) : contents.length === 0 ? (
          <div className="text-center py-12">
            <div className="text-6xl mb-4">📭</div>
            <h3 className="text-2xl font-bold text-gray-900 mb-2">
              Aucun contenu trouvé
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
            <div className="grid md:grid-cols-2 lg:grid-cols-3 gap-6 mb-8">
              {contents.map((content) => (
                <ContentCard key={content.id} content={content} />
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

export default Content;
