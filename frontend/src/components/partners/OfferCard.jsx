import React from 'react';
import { Card, Badge, Button } from '../common';
import useAuthStore from '../../stores/authStore';

const OfferCard = ({ offer, onGenerateCode }) => {
  const { user, isAuthenticated, isPremium } = useAuthStore();

  // Calculer si l'offre est valide
  const isValid = offer.status === 'active' &&
                  new Date(offer.valid_from) <= new Date() &&
                  new Date(offer.valid_until) >= new Date();

  // Calculer le stock restant
  const stockRemaining = offer.stock_available - offer.stock_used;
  const stockPercentage = (stockRemaining / offer.stock_available) * 100;
  const isLowStock = stockPercentage < 20 && stockPercentage > 0;
  const isOutOfStock = stockRemaining <= 0;

  // Vérifier si l'offre expire bientôt (dans les 7 jours)
  const daysUntilExpiry = Math.ceil((new Date(offer.valid_until) - new Date()) / (1000 * 60 * 60 * 24));
  const isExpiringSoon = daysUntilExpiry <= 7 && daysUntilExpiry > 0;

  // Déterminer la réduction selon le type d'utilisateur
  const getDiscountValue = () => {
    if (!user) return 0;

    if (offer.reduction_type === 'percentage') {
      return user.user_type === 'socios'
        ? offer.reduction_value_socios
        : offer.reduction_value_premium;
    }
    return offer.reduction_value;
  };

  const discountValue = getDiscountValue();

  const getOfferTypeColor = () => {
    const types = {
      'standard': 'default',
      'flash': 'error',
      'seasonal': 'info',
      'exclusive': 'secondary',
    };
    return types[offer.type] || 'default';
  };

  const getOfferTypeLabel = () => {
    const labels = {
      'standard': 'Standard',
      'flash': '⚡ Flash',
      'seasonal': '🎄 Saisonnier',
      'exclusive': '👑 Exclusif',
    };
    return labels[offer.type] || offer.type;
  };

  return (
    <Card padding="lg" className={!isValid || isOutOfStock ? 'opacity-60' : ''}>
      <div className="flex flex-col h-full">
        {/* Header avec badges */}
        <div className="flex items-start justify-between mb-3">
          <div className="flex-1">
            <h3 className="text-lg font-bold text-gray-900 mb-2">
              {offer.title}
            </h3>
          </div>
          <div className="flex flex-col gap-1 ml-2">
            <Badge variant={getOfferTypeColor()} size="sm">
              {getOfferTypeLabel()}
            </Badge>
            {!isValid && (
              <Badge variant="error" size="sm">Expirée</Badge>
            )}
            {isExpiringSoon && isValid && (
              <Badge variant="warning" size="sm">Expire bientôt</Badge>
            )}
          </div>
        </div>

        {/* Description */}
        {offer.description && (
          <p className="text-gray-600 text-sm mb-4">
            {offer.description}
          </p>
        )}

        {/* Réduction */}
        <div className="bg-gradient-gold p-3 rounded-lg mb-4 text-center">
          <div className="text-2xl font-bold text-black">
            {offer.reduction_type === 'percentage'
              ? `-${discountValue}%`
              : `-${discountValue} TND`
            }
          </div>
          <div className="text-xs text-black opacity-80">
            {user?.user_type === 'socios' ? 'Réduction Socios' : 'Réduction Premium'}
          </div>
        </div>

        {/* Informations */}
        <div className="space-y-2 mb-4 text-sm">
          {/* Validité */}
          <div className="flex items-center text-gray-600">
            <svg className="w-4 h-4 mr-2" fill="none" strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" viewBox="0 0 24 24" stroke="currentColor">
              <path d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
            </svg>
            <span>Valide jusqu'au {new Date(offer.valid_until).toLocaleDateString('fr-FR')}</span>
          </div>

          {/* Stock */}
          {offer.stock_available > 0 && (
            <div className="flex items-center">
              <svg className="w-4 h-4 mr-2" fill="none" strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" viewBox="0 0 24 24" stroke="currentColor">
                <path d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
              </svg>
              <span className={isLowStock ? 'text-orange-600 font-medium' : 'text-gray-600'}>
                {isOutOfStock ? 'Stock épuisé' : `${stockRemaining} restant${stockRemaining > 1 ? 's' : ''}`}
              </span>
            </div>
          )}

          {/* Conditions */}
          {offer.conditions && (
            <div className="text-xs text-gray-500 italic">
              {offer.conditions}
            </div>
          )}
        </div>

        {/* Actions */}
        <div className="mt-auto">
          {!isAuthenticated ? (
            <Button variant="outline" fullWidth disabled>
              Connectez-vous pour générer un code
            </Button>
          ) : !isPremium() ? (
            <Button variant="outline" fullWidth disabled>
              Réservé Premium/Socios
            </Button>
          ) : isOutOfStock ? (
            <Button variant="outline" fullWidth disabled>
              Stock épuisé
            </Button>
          ) : !isValid ? (
            <Button variant="outline" fullWidth disabled>
              Offre expirée
            </Button>
          ) : (
            <Button
              variant="secondary"
              fullWidth
              onClick={() => onGenerateCode(offer)}
            >
              Générer un code
            </Button>
          )}
        </div>
      </div>
    </Card>
  );
};

export default OfferCard;
