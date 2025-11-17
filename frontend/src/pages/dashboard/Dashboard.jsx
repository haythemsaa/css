import React, { useState, useEffect } from 'react';
import { Link } from 'react-router-dom';
import { Card, Badge, Button } from '../../components/common';
import useAuthStore from '../../stores/authStore';
import { codesService } from '../../services/api';

const Dashboard = () => {
  const { user, refreshProfile } = useAuthStore();
  const [myCodes, setMyCodes] = useState([]);
  const [loading, setLoading] = useState(true);
  const [activeTab, setActiveTab] = useState('overview');

  useEffect(() => {
    loadDashboardData();
  }, []);

  const loadDashboardData = async () => {
    setLoading(true);
    try {
      // Rafraîchir le profil
      await refreshProfile();

      // Charger les codes
      const response = await codesService.getMyCodes({ status: 'active' });
      if (response.success) {
        setMyCodes(response.data);
      }
    } catch (err) {
      console.error('Erreur chargement dashboard:', err);
    } finally {
      setLoading(false);
    }
  };

  const getUserTypeInfo = () => {
    const types = {
      free: {
        name: 'Free',
        color: 'default',
        icon: '👤',
        benefits: [
          'Accès au contenu public',
          'Navigation des partenaires',
          'Calendrier des matchs',
        ],
      },
      premium: {
        name: 'Premium',
        color: 'warning',
        icon: '⭐',
        benefits: [
          'Contenu premium HD',
          'Réductions Freeoui 10-15%',
          'Génération codes QR/Promo',
          'Points de fidélité',
        ],
      },
      socios: {
        name: 'Socios',
        color: 'secondary',
        icon: '👑',
        benefits: [
          'Tous les avantages Premium',
          'Réductions Freeoui jusqu\'à 25%',
          'Accès VIP exclusif',
          'Tombola mensuelle',
          'Badge vérifié',
        ],
      },
    };

    return types[user?.user_type] || types.free;
  };

  const getLoyaltyLevel = () => {
    const points = user?.loyalty_points || 0;
    if (points >= 5000) return { name: 'Platinum', color: 'bg-purple-100 text-purple-800', icon: '💎' };
    if (points >= 2000) return { name: 'Gold', color: 'bg-yellow-100 text-yellow-800', icon: '🥇' };
    if (points >= 500) return { name: 'Silver', color: 'bg-gray-200 text-gray-800', icon: '🥈' };
    return { name: 'Bronze', color: 'bg-orange-100 text-orange-800', icon: '🥉' };
  };

  const userTypeInfo = getUserTypeInfo();
  const loyaltyLevel = getLoyaltyLevel();

  if (loading) {
    return (
      <div className="min-h-screen flex items-center justify-center">
        <div className="text-center">
          <div className="inline-block animate-spin rounded-full h-12 w-12 border-b-2 border-black mb-4"></div>
          <p className="text-gray-600">Chargement...</p>
        </div>
      </div>
    );
  }

  return (
    <div className="bg-gray-50 min-h-screen py-8">
      <div className="container mx-auto px-4">
        {/* Header */}
        <div className="mb-8">
          <h1 className="text-4xl font-bold text-gray-900 mb-2">
            Bienvenue, {user?.name} {userTypeInfo.icon}
          </h1>
          <p className="text-gray-600">
            Gérez votre compte et profitez de vos avantages Freeoui
          </p>
        </div>

        {/* Stats Cards */}
        <div className="grid md:grid-cols-4 gap-6 mb-8">
          {/* Type d'utilisateur */}
          <Card padding="lg" variant="gold">
            <div className="text-center">
              <div className="text-4xl mb-2">{userTypeInfo.icon}</div>
              <div className="text-2xl font-bold text-gray-900 mb-1">
                {userTypeInfo.name}
              </div>
              <p className="text-sm text-gray-600">Type de compte</p>
            </div>
          </Card>

          {/* Points de fidélité */}
          <Card padding="lg">
            <div className="text-center">
              <div className="text-4xl mb-2">⭐</div>
              <div className="text-2xl font-bold text-gray-900 mb-1">
                {user?.loyalty_points || 0}
              </div>
              <p className="text-sm text-gray-600">Points de fidélité</p>
            </div>
          </Card>

          {/* Niveau de fidélité */}
          <Card padding="lg">
            <div className="text-center">
              <div className="text-4xl mb-2">{loyaltyLevel.icon}</div>
              <div className="text-2xl font-bold text-gray-900 mb-1">
                {loyaltyLevel.name}
              </div>
              <p className="text-sm text-gray-600">Niveau de fidélité</p>
            </div>
          </Card>

          {/* Codes actifs */}
          <Card padding="lg">
            <div className="text-center">
              <div className="text-4xl mb-2">🎫</div>
              <div className="text-2xl font-bold text-gray-900 mb-1">
                {myCodes.length}
              </div>
              <p className="text-sm text-gray-600">Codes actifs</p>
            </div>
          </Card>
        </div>

        {/* Tabs */}
        <div className="mb-6">
          <div className="border-b border-gray-200">
            <div className="flex space-x-8">
              <button
                onClick={() => setActiveTab('overview')}
                className={`py-4 px-2 border-b-2 font-medium text-sm transition-colors ${
                  activeTab === 'overview'
                    ? 'border-black text-black'
                    : 'border-transparent text-gray-500 hover:text-gray-700'
                }`}
              >
                Vue d'ensemble
              </button>
              <button
                onClick={() => setActiveTab('codes')}
                className={`py-4 px-2 border-b-2 font-medium text-sm transition-colors ${
                  activeTab === 'codes'
                    ? 'border-black text-black'
                    : 'border-transparent text-gray-500 hover:text-gray-700'
                }`}
              >
                Mes codes ({myCodes.length})
              </button>
              <button
                onClick={() => setActiveTab('profile')}
                className={`py-4 px-2 border-b-2 font-medium text-sm transition-colors ${
                  activeTab === 'profile'
                    ? 'border-black text-black'
                    : 'border-transparent text-gray-500 hover:text-gray-700'
                }`}
              >
                Mon profil
              </button>
            </div>
          </div>
        </div>

        {/* Tab Content */}
        {activeTab === 'overview' && (
          <div className="grid md:grid-cols-2 gap-6">
            {/* Avantages */}
            <Card padding="lg">
              <h3 className="text-xl font-bold mb-4">Vos avantages</h3>
              <ul className="space-y-2">
                {userTypeInfo.benefits.map((benefit, index) => (
                  <li key={index} className="flex items-start">
                    <span className="text-green-500 mr-2">✓</span>
                    <span className="text-gray-700">{benefit}</span>
                  </li>
                ))}
              </ul>

              {user?.user_type === 'free' && (
                <div className="mt-6 pt-6 border-t border-gray-200">
                  <Link to="/register">
                    <Button variant="secondary" fullWidth>
                      Passer à Premium
                    </Button>
                  </Link>
                </div>
              )}
            </Card>

            {/* Actions rapides */}
            <Card padding="lg">
              <h3 className="text-xl font-bold mb-4">Actions rapides</h3>
              <div className="space-y-3">
                <Link to="/partners">
                  <Button variant="outline" fullWidth>
                    🏪 Parcourir les partenaires
                  </Button>
                </Link>
                <Link to="/content">
                  <Button variant="outline" fullWidth>
                    📰 Voir les actualités
                  </Button>
                </Link>
                <Link to="/matches">
                  <Button variant="outline" fullWidth>
                    ⚽ Calendrier des matchs
                  </Button>
                </Link>
                <Link to="/players">
                  <Button variant="outline" fullWidth>
                    👥 Effectif de l'équipe
                  </Button>
                </Link>
              </div>
            </Card>
          </div>
        )}

        {activeTab === 'codes' && (
          <div>
            {myCodes.length === 0 ? (
              <Card padding="lg">
                <div className="text-center py-12">
                  <div className="text-6xl mb-4">🎫</div>
                  <h3 className="text-xl font-bold text-gray-900 mb-2">
                    Aucun code actif
                  </h3>
                  <p className="text-gray-600 mb-6">
                    Parcourez nos partenaires et générez des codes de réduction
                  </p>
                  <Link to="/partners">
                    <Button variant="secondary">
                      Découvrir les partenaires
                    </Button>
                  </Link>
                </div>
              </Card>
            ) : (
              <div className="grid md:grid-cols-2 lg:grid-cols-3 gap-6">
                {myCodes.map((code) => (
                  <Card key={code.id} padding="lg">
                    <div className="flex items-start justify-between mb-3">
                      <Badge variant="success" size="sm">
                        {code.type.toUpperCase()}
                      </Badge>
                      <Badge variant="default" size="sm">
                        {code.status}
                      </Badge>
                    </div>

                    <div className="bg-gradient-gold p-4 rounded-lg mb-4 text-center">
                      <div className="text-2xl font-bold text-black">
                        {code.code}
                      </div>
                    </div>

                    <div className="space-y-2 text-sm">
                      <div>
                        <strong>Offre:</strong>{' '}
                        {code.offer?.title || 'N/A'}
                      </div>
                      <div>
                        <strong>Partenaire:</strong>{' '}
                        {code.offer?.partner?.name || 'N/A'}
                      </div>
                      <div>
                        <strong>Expire le:</strong>{' '}
                        {new Date(code.expires_at).toLocaleDateString('fr-FR')}
                      </div>
                    </div>
                  </Card>
                ))}
              </div>
            )}
          </div>
        )}

        {activeTab === 'profile' && (
          <Card padding="lg" className="max-w-2xl">
            <h3 className="text-xl font-bold mb-6">Informations personnelles</h3>

            <div className="space-y-4">
              <div className="grid md:grid-cols-2 gap-4">
                <div>
                  <label className="block text-sm font-medium text-gray-700 mb-1">
                    Nom complet
                  </label>
                  <div className="px-4 py-2 bg-gray-50 rounded-lg">
                    {user?.name}
                  </div>
                </div>

                <div>
                  <label className="block text-sm font-medium text-gray-700 mb-1">
                    Email
                  </label>
                  <div className="px-4 py-2 bg-gray-50 rounded-lg">
                    {user?.email}
                  </div>
                </div>
              </div>

              {user?.phone && (
                <div>
                  <label className="block text-sm font-medium text-gray-700 mb-1">
                    Téléphone
                  </label>
                  <div className="px-4 py-2 bg-gray-50 rounded-lg">
                    {user.phone}
                  </div>
                </div>
              )}

              {user?.city && (
                <div>
                  <label className="block text-sm font-medium text-gray-700 mb-1">
                    Ville
                  </label>
                  <div className="px-4 py-2 bg-gray-50 rounded-lg">
                    {user.city}
                  </div>
                </div>
              )}

              {user?.user_type === 'socios' && user?.socios_number && (
                <div>
                  <label className="block text-sm font-medium text-gray-700 mb-1">
                    Numéro Socios
                  </label>
                  <div className="px-4 py-2 bg-gradient-gold rounded-lg font-bold">
                    {user.socios_number}
                  </div>
                </div>
              )}

              <div className="pt-6 border-t border-gray-200">
                <Button variant="outline" fullWidth>
                  Modifier mon profil
                </Button>
              </div>
            </div>
          </Card>
        )}
      </div>
    </div>
  );
};

export default Dashboard;
