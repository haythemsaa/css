import React, { useState, useEffect } from 'react';
import { useParams, useNavigate } from 'react-router-dom';
import { partnersService, codesService } from '../../services/api';
import OfferCard from '../../components/partners/OfferCard';
import { Button, Badge, Card } from '../../components/common';
import useAuthStore from '../../stores/authStore';

const PartnerDetail = () => {
  const { slug } = useParams();
  const navigate = useNavigate();
  const { user, isAuthenticated } = useAuthStore();

  const [partner, setPartner] = useState(null);
  const [loading, setLoading] = useState(true);
  const [error, setError] = useState(null);

  // Modal de génération de code
  const [showCodeModal, setShowCodeModal] = useState(false);
  const [selectedOffer, setSelectedOffer] = useState(null);
  const [generatingCode, setGeneratingCode] = useState(false);
  const [generatedCode, setGeneratedCode] = useState(null);
  const [codeType, setCodeType] = useState('qr');

  useEffect(() => {
    loadPartnerDetails();
  }, [slug]);

  const loadPartnerDetails = async () => {
    setLoading(true);
    setError(null);

    try {
      const response = await partnersService.getPartner(slug);

      if (response.success) {
        setPartner(response.data);
      }
    } catch (err) {
      setError(err.message || 'Erreur lors du chargement du partenaire');
    } finally {
      setLoading(false);
    }
  };

  const handleGenerateCode = async (offer) => {
    if (!isAuthenticated) {
      navigate('/login');
      return;
    }

    setSelectedOffer(offer);
    setShowCodeModal(true);
  };

  const confirmGenerateCode = async () => {
    setGeneratingCode(true);

    try {
      const response = await codesService.generateCode(selectedOffer.slug, codeType);

      if (response.success) {
        setGeneratedCode(response.data);
      }
    } catch (err) {
      alert(err.message || 'Erreur lors de la génération du code');
    } finally {
      setGeneratingCode(false);
    }
  };

  const closeModal = () => {
    setShowCodeModal(false);
    setGeneratedCode(null);
    setSelectedOffer(null);
    setCodeType('qr');
  };

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

  if (error || !partner) {
    return (
      <div className="min-h-screen flex items-center justify-center">
        <div className="text-center">
          <h2 className="text-2xl font-bold text-gray-900 mb-4">
            Partenaire non trouvé
          </h2>
          <p className="text-gray-600 mb-6">{error}</p>
          <Button variant="outline" onClick={() => navigate('/partners')}>
            Retour aux partenaires
          </Button>
        </div>
      </div>
    );
  }

  const discount = user?.user_type === 'socios'
    ? partner.reduction_value_socios
    : user?.user_type === 'premium'
    ? partner.reduction_value_premium
    : 0;

  return (
    <div className="bg-gray-50 min-h-screen">
      {/* Hero Section */}
      <div className="bg-black text-white py-12">
        <div className="container mx-auto px-4">
          <button
            onClick={() => navigate('/partners')}
            className="text-css-gold hover:underline mb-4 inline-flex items-center"
          >
            ← Retour aux partenaires
          </button>

          <div className="grid md:grid-cols-3 gap-8">
            {/* Logo */}
            <div className="md:col-span-1">
              <div className="bg-white rounded-lg p-8 flex items-center justify-center h-64">
                {partner.logo ? (
                  <img src={partner.logo} alt={partner.name} className="max-h-full max-w-full object-contain" />
                ) : (
                  <div className="text-8xl">🏪</div>
                )}
              </div>
            </div>

            {/* Informations */}
            <div className="md:col-span-2">
              <div className="flex items-start justify-between mb-4">
                <div>
                  <h1 className="text-4xl font-bold mb-2">{partner.name}</h1>
                  {partner.category && (
                    <Badge variant="secondary">{partner.category.name_fr}</Badge>
                  )}
                </div>
                {discount > 0 && (
                  <div className="bg-gradient-gold text-black px-6 py-3 rounded-lg font-bold text-2xl">
                    -{discount}%
                  </div>
                )}
              </div>

              {partner.description && (
                <p className="text-gray-300 text-lg mb-6">{partner.description}</p>
              )}

              {/* Informations de contact */}
              <div className="grid md:grid-cols-2 gap-4">
                {partner.address && (
                  <div className="flex items-start">
                    <svg className="w-5 h-5 mr-2 mt-1 text-css-gold" fill="none" strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" viewBox="0 0 24 24" stroke="currentColor">
                      <path d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                      <path d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                    </svg>
                    <div>
                      <p className="font-medium">Adresse</p>
                      <p className="text-gray-300">{partner.address}</p>
                      <p className="text-gray-300">{partner.city}, {partner.postal_code}</p>
                    </div>
                  </div>
                )}

                {partner.phone && (
                  <div className="flex items-start">
                    <svg className="w-5 h-5 mr-2 mt-1 text-css-gold" fill="none" strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" viewBox="0 0 24 24" stroke="currentColor">
                      <path d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
                    </svg>
                    <div>
                      <p className="font-medium">Téléphone</p>
                      <p className="text-gray-300">{partner.phone}</p>
                    </div>
                  </div>
                )}

                {partner.email && (
                  <div className="flex items-start">
                    <svg className="w-5 h-5 mr-2 mt-1 text-css-gold" fill="none" strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" viewBox="0 0 24 24" stroke="currentColor">
                      <path d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                    </svg>
                    <div>
                      <p className="font-medium">Email</p>
                      <p className="text-gray-300">{partner.email}</p>
                    </div>
                  </div>
                )}

                {partner.website && (
                  <div className="flex items-start">
                    <svg className="w-5 h-5 mr-2 mt-1 text-css-gold" fill="none" strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" viewBox="0 0 24 24" stroke="currentColor">
                      <path d="M21 12a9 9 0 01-9 9m9-9a9 9 0 00-9-9m9 9H3m9 9a9 9 0 01-9-9m9 9c1.657 0 3-4.03 3-9s-1.343-9-3-9m0 18c-1.657 0-3-4.03-3-9s1.343-9 3-9m-9 9a9 9 0 019-9" />
                    </svg>
                    <div>
                      <p className="font-medium">Site web</p>
                      <a href={partner.website} target="_blank" rel="noopener noreferrer" className="text-css-gold hover:underline">
                        Visiter le site
                      </a>
                    </div>
                  </div>
                )}
              </div>
            </div>
          </div>
        </div>
      </div>

      {/* Offres */}
      <div className="container mx-auto px-4 py-12">
        <h2 className="text-3xl font-bold text-gray-900 mb-8">
          Offres disponibles ({partner.offers?.length || 0})
        </h2>

        {!partner.offers || partner.offers.length === 0 ? (
          <Card padding="lg">
            <div className="text-center py-12">
              <div className="text-6xl mb-4">📭</div>
              <h3 className="text-xl font-bold text-gray-900 mb-2">
                Aucune offre disponible
              </h3>
              <p className="text-gray-600">
                Ce partenaire n'a pas d'offres actives pour le moment.
              </p>
            </div>
          </Card>
        ) : (
          <div className="grid md:grid-cols-2 lg:grid-cols-3 gap-6">
            {partner.offers.map((offer) => (
              <OfferCard
                key={offer.id}
                offer={offer}
                onGenerateCode={handleGenerateCode}
              />
            ))}
          </div>
        )}
      </div>

      {/* Modal de génération de code */}
      {showCodeModal && (
        <div className="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4">
          <Card padding="lg" className="max-w-md w-full">
            {!generatedCode ? (
              <>
                <h3 className="text-2xl font-bold mb-4">Générer un code</h3>
                <p className="text-gray-600 mb-6">
                  Offre: <strong>{selectedOffer?.title}</strong>
                </p>

                <div className="mb-6">
                  <label className="block text-sm font-medium text-gray-700 mb-2">
                    Type de code
                  </label>
                  <div className="grid grid-cols-3 gap-3">
                    {['qr', 'promo', 'nfc'].map((type) => (
                      <button
                        key={type}
                        onClick={() => setCodeType(type)}
                        className={`p-4 border-2 rounded-lg text-center transition-all ${
                          codeType === type
                            ? 'border-black bg-black text-white'
                            : 'border-gray-300 hover:border-gray-400'
                        }`}
                      >
                        <div className="text-2xl mb-1">
                          {type === 'qr' ? '📱' : type === 'promo' ? '🎫' : '📲'}
                        </div>
                        <div className="text-xs font-medium uppercase">{type}</div>
                      </button>
                    ))}
                  </div>
                </div>

                <div className="flex gap-3">
                  <Button variant="outline" fullWidth onClick={closeModal}>
                    Annuler
                  </Button>
                  <Button
                    variant="secondary"
                    fullWidth
                    onClick={confirmGenerateCode}
                    loading={generatingCode}
                  >
                    Générer
                  </Button>
                </div>
              </>
            ) : (
              <>
                <div className="text-center mb-6">
                  <div className="text-6xl mb-4">✅</div>
                  <h3 className="text-2xl font-bold mb-2">Code généré !</h3>
                  <p className="text-gray-600">
                    Présentez ce code chez le partenaire
                  </p>
                </div>

                <div className="bg-gradient-gold p-6 rounded-lg mb-6 text-center">
                  <div className="text-3xl font-bold text-black mb-2">
                    {generatedCode.code}
                  </div>
                  <div className="text-sm text-black opacity-80">
                    Type: {generatedCode.type.toUpperCase()}
                  </div>
                </div>

                <div className="bg-gray-50 p-4 rounded-lg mb-6 text-sm">
                  <p className="mb-2">
                    <strong>Valide jusqu'au:</strong>{' '}
                    {new Date(generatedCode.expires_at).toLocaleDateString('fr-FR')}
                  </p>
                  <p>
                    <strong>Statut:</strong>{' '}
                    <Badge variant="success">Actif</Badge>
                  </p>
                </div>

                <Button variant="primary" fullWidth onClick={closeModal}>
                  Fermer
                </Button>
              </>
            )}
          </Card>
        </div>
      )}
    </div>
  );
};

export default PartnerDetail;
