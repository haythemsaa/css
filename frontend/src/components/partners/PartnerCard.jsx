import React from 'react';
import { Link } from 'react-router-dom';
import { Card, Badge } from '../common';
import useAuthStore from '../../stores/authStore';

const PartnerCard = ({ partner }) => {
  const { user, isAuthenticated } = useAuthStore();

  // Déterminer la réduction à afficher selon le type d'utilisateur
  const getDiscount = () => {
    if (!isAuthenticated || !user) {
      return { value: 0, canGenerate: false };
    }

    switch (user.user_type) {
      case 'socios':
        return { value: partner.reduction_value_socios, canGenerate: true };
      case 'premium':
        return { value: partner.reduction_value_premium, canGenerate: true };
      default:
        return { value: 0, canGenerate: false };
    }
  };

  const discount = getDiscount();

  const getCategoryColor = (category) => {
    const colors = {
      'Restauration': 'bg-orange-100 text-orange-800',
      'Shopping': 'bg-pink-100 text-pink-800',
      'Sport': 'bg-blue-100 text-blue-800',
      'Santé': 'bg-green-100 text-green-800',
      'Loisirs': 'bg-purple-100 text-purple-800',
      'Voyages': 'bg-cyan-100 text-cyan-800',
      'Services': 'bg-gray-100 text-gray-800',
      'Éducation': 'bg-indigo-100 text-indigo-800',
    };
    return colors[category] || 'bg-gray-100 text-gray-800';
  };

  return (
    <Link to={`/partners/${partner.slug}`}>
      <Card hover padding="none" className="overflow-hidden h-full">
        {/* Image/Logo */}
        <div className="relative h-48 bg-gray-200 flex items-center justify-center">
          {partner.logo ? (
            <img
              src={partner.logo}
              alt={partner.name}
              className="w-full h-full object-cover"
            />
          ) : (
            <div className="text-6xl">🏪</div>
          )}

          {/* Badge réduction */}
          {discount.value > 0 && (
            <div className="absolute top-3 right-3">
              <div className="bg-gradient-gold text-black px-3 py-1 rounded-full font-bold text-lg shadow-lg">
                -{discount.value}%
              </div>
            </div>
          )}

          {/* Badge featured */}
          {partner.featured_order && partner.featured_order > 0 && (
            <div className="absolute top-3 left-3">
              <Badge variant="secondary">⭐ En vedette</Badge>
            </div>
          )}
        </div>

        {/* Contenu */}
        <div className="p-4">
          {/* Catégorie */}
          {partner.category && (
            <div className="mb-2">
              <span className={`inline-block px-2 py-1 rounded-full text-xs font-medium ${getCategoryColor(partner.category.name_fr)}`}>
                {partner.category.name_fr}
              </span>
            </div>
          )}

          {/* Nom */}
          <h3 className="text-xl font-bold text-gray-900 mb-2 line-clamp-1">
            {partner.name}
          </h3>

          {/* Description */}
          {partner.description && (
            <p className="text-gray-600 text-sm mb-3 line-clamp-2">
              {partner.description}
            </p>
          )}

          {/* Localisation */}
          {partner.city && (
            <div className="flex items-center text-gray-500 text-sm mb-2">
              <svg className="w-4 h-4 mr-1" fill="none" strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" viewBox="0 0 24 24" stroke="currentColor">
                <path d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                <path d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
              </svg>
              <span>{partner.city}</span>
              {partner.distance && (
                <span className="ml-2 text-css-gold font-medium">
                  • {partner.distance.toFixed(1)} km
                </span>
              )}
            </div>
          )}

          {/* Offres actives */}
          {partner.active_offers_count > 0 && (
            <div className="flex items-center text-green-600 text-sm font-medium">
              <svg className="w-4 h-4 mr-1" fill="none" strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" viewBox="0 0 24 24" stroke="currentColor">
                <path d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z" />
              </svg>
              <span>{partner.active_offers_count} offre{partner.active_offers_count > 1 ? 's' : ''} active{partner.active_offers_count > 1 ? 's' : ''}</span>
            </div>
          )}

          {/* Message pour utilisateurs Free */}
          {!discount.canGenerate && (
            <div className="mt-3 pt-3 border-t border-gray-200">
              <p className="text-xs text-gray-500 text-center">
                <Link to="/register" className="text-css-gold hover:underline font-medium">
                  Devenez Premium
                </Link>
                {' '}pour profiter des réductions
              </p>
            </div>
          )}
        </div>
      </Card>
    </Link>
  );
};

export default PartnerCard;
