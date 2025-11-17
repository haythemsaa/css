import React from 'react';
import { useNavigate } from 'react-router-dom';
import { Card, Button, Badge } from '../../components/common';
import useAuthStore from '../../stores/authStore';

const Upgrade = () => {
  const navigate = useNavigate();
  const { user, isAuthenticated } = useAuthStore();

  const plans = [
    {
      name: 'Free',
      price: '0',
      period: 'Gratuit',
      description: 'Pour découvrir la plateforme CSS',
      features: [
        { text: 'Accès au contenu public', included: true },
        { text: 'Navigation des partenaires', included: true },
        { text: 'Calendrier des matchs', included: true },
        { text: 'Informations de l\'équipe', included: true },
        { text: 'Contenu premium HD', included: false },
        { text: 'Réductions CSS Privilèges', included: false },
        { text: 'Génération de codes', included: false },
        { text: 'Points de fidélité', included: false },
      ],
      buttonText: 'Compte actuel',
      buttonVariant: 'outline',
      highlighted: false,
      icon: '👤',
      color: 'default',
    },
    {
      name: 'Premium',
      price: '15',
      period: '/ mois',
      description: 'Pour les vrais supporters du CSS',
      features: [
        { text: 'Tous les avantages Free', included: true },
        { text: 'Contenu premium HD/4K', included: true },
        { text: 'Réductions CSS Privilèges 10-15%', included: true },
        { text: 'Génération codes QR/Promo/NFC', included: true },
        { text: 'Points de fidélité', included: true },
        { text: 'Support prioritaire', included: true },
        { text: 'Accès VIP exclusif', included: false },
        { text: 'Tombola mensuelle', included: false },
      ],
      buttonText: 'S\'abonner',
      buttonVariant: 'secondary',
      highlighted: true,
      icon: '⭐',
      color: 'warning',
      savings: 'Le plus populaire',
    },
    {
      name: 'Socios',
      price: 'VIP',
      period: 'Membre officiel',
      description: 'Le statut ultime du supporter CSS',
      features: [
        { text: 'Tous les avantages Premium', included: true },
        { text: 'Réductions CSS Privilèges jusqu\'à 25%', included: true },
        { text: 'Accès VIP exclusif', included: true },
        { text: 'Tombola mensuelle', included: true },
        { text: 'Cadeaux personnalisés', included: true },
        { text: 'Badge Socios vérifié', included: true },
        { text: 'Rencontres avec les joueurs', included: true },
        { text: 'Priorité billetterie', included: true },
      ],
      buttonText: 'Devenir Socios',
      buttonVariant: 'primary',
      highlighted: false,
      icon: '👑',
      color: 'secondary',
      savings: 'Accès exclusif',
    },
  ];

  const freeBenefits = [
    {
      icon: '🏪',
      title: '29 Partenaires',
      description: 'Restaurants, commerces, services à Sfax',
    },
    {
      icon: '🎁',
      title: '60+ Offres',
      description: 'Offres exclusives renouvelées régulièrement',
    },
    {
      icon: '💳',
      title: '3 Types de codes',
      description: 'QR Code, Code Promo, ou NFC selon vos préférences',
    },
    {
      icon: '📍',
      title: 'Géolocalisation',
      description: 'Trouvez les partenaires près de vous',
    },
  ];

  const handleSubscribe = (planName) => {
    if (!isAuthenticated) {
      navigate('/register');
      return;
    }

    if (planName === 'Premium') {
      // TODO: Redirect to payment page
      alert('Redirection vers la page de paiement Premium (à implémenter)');
    } else if (planName === 'Socios') {
      // TODO: Redirect to Socios registration
      alert('Formulaire d\'inscription Socios (à implémenter)');
    }
  };

  const currentPlan = user?.user_type || 'free';

  return (
    <div className="bg-gray-50 min-h-screen">
      {/* Hero Section */}
      <section className="bg-black text-white py-20">
        <div className="container mx-auto px-4 text-center">
          <Badge variant="secondary" size="lg" className="mb-6">
            Rejoignez la famille CSS
          </Badge>
          <h1 className="text-5xl md:text-6xl font-bold mb-6">
            Choisissez votre <span className="text-gradient-gold">Abonnement</span>
          </h1>
          <p className="text-xl text-gray-300 mb-8 max-w-3xl mx-auto">
            Profitez de réductions exclusives chez nos partenaires CSS Privilèges,
            accédez au contenu premium et soutenez le CSS
          </p>
        </div>
      </section>

      {/* Pricing Plans */}
      <section className="py-16 -mt-20">
        <div className="container mx-auto px-4">
          <div className="grid md:grid-cols-3 gap-8 max-w-7xl mx-auto">
            {plans.map((plan, index) => {
              const isCurrentPlan = plan.name.toLowerCase() === currentPlan;

              return (
                <Card
                  key={index}
                  variant={plan.highlighted ? 'gold' : 'default'}
                  padding="lg"
                  className={`relative ${plan.highlighted ? 'scale-105 shadow-2xl' : ''}`}
                >
                  {plan.savings && (
                    <div className="absolute -top-4 left-1/2 transform -translate-x-1/2">
                      <Badge variant={plan.color}>{plan.savings}</Badge>
                    </div>
                  )}

                  <div className="text-center mb-6">
                    <div className="text-5xl mb-3">{plan.icon}</div>
                    <h3 className="text-2xl font-bold mb-2">{plan.name}</h3>
                    <p className="text-gray-600 text-sm mb-4">{plan.description}</p>
                    <div className="mb-2">
                      <span className="text-5xl font-bold">{plan.price}</span>
                      {plan.period !== 'Gratuit' && plan.period !== 'Membre officiel' && (
                        <span className="text-gray-600 ml-1">{plan.period}</span>
                      )}
                    </div>
                    {plan.period !== 'Gratuit' && plan.period !== 'Membre officiel' && (
                      <p className="text-sm text-gray-500">{plan.period}</p>
                    )}
                  </div>

                  <ul className="space-y-3 mb-8">
                    {plan.features.map((feature, idx) => (
                      <li key={idx} className="flex items-start text-sm">
                        <span className={`mr-2 ${feature.included ? 'text-green-500' : 'text-gray-300'}`}>
                          {feature.included ? '✓' : '×'}
                        </span>
                        <span className={feature.included ? 'text-gray-700' : 'text-gray-400'}>
                          {feature.text}
                        </span>
                      </li>
                    ))}
                  </ul>

                  <Button
                    variant={plan.buttonVariant}
                    fullWidth
                    disabled={isCurrentPlan}
                    onClick={() => handleSubscribe(plan.name)}
                  >
                    {isCurrentPlan ? 'Compte actuel' : plan.buttonText}
                  </Button>

                  {isCurrentPlan && (
                    <div className="mt-3 text-center">
                      <Badge variant="success" size="sm">
                        ✓ Abonnement actif
                      </Badge>
                    </div>
                  )}
                </Card>
              );
            })}
          </div>
        </div>
      </section>

      {/* CSS Privilèges Benefits */}
      <section className="py-16 bg-white">
        <div className="container mx-auto px-4">
          <div className="text-center mb-12">
            <h2 className="text-4xl font-bold mb-4">
              Pourquoi <span className="text-gradient-gold">CSS Privilèges</span> ?
            </h2>
            <p className="text-gray-600 max-w-2xl mx-auto">
              Le système de réductions exclusives pour les supporters du CSS
            </p>
          </div>

          <div className="grid md:grid-cols-2 lg:grid-cols-4 gap-6 max-w-6xl mx-auto">
            {freeBenefits.map((benefit, index) => (
              <Card key={index} padding="lg" className="text-center">
                <div className="text-5xl mb-4">{benefit.icon}</div>
                <h3 className="text-xl font-bold mb-2">{benefit.title}</h3>
                <p className="text-gray-600 text-sm">{benefit.description}</p>
              </Card>
            ))}
          </div>
        </div>
      </section>

      {/* Comparison Table */}
      <section className="py-16 bg-gray-50">
        <div className="container mx-auto px-4">
          <div className="text-center mb-12">
            <h2 className="text-4xl font-bold mb-4">Comparaison détaillée</h2>
          </div>

          <div className="max-w-5xl mx-auto overflow-x-auto">
            <table className="w-full bg-white rounded-lg shadow-md">
              <thead>
                <tr className="border-b border-gray-200">
                  <th className="text-left p-4 font-bold">Fonctionnalités</th>
                  <th className="text-center p-4 font-bold">Free</th>
                  <th className="text-center p-4 font-bold bg-yellow-50">Premium</th>
                  <th className="text-center p-4 font-bold">Socios</th>
                </tr>
              </thead>
              <tbody>
                <tr className="border-b border-gray-200">
                  <td className="p-4">Contenu public</td>
                  <td className="text-center p-4">✓</td>
                  <td className="text-center p-4 bg-yellow-50">✓</td>
                  <td className="text-center p-4">✓</td>
                </tr>
                <tr className="border-b border-gray-200">
                  <td className="p-4">Contenu Premium HD/4K</td>
                  <td className="text-center p-4">×</td>
                  <td className="text-center p-4 bg-yellow-50">✓</td>
                  <td className="text-center p-4">✓</td>
                </tr>
                <tr className="border-b border-gray-200">
                  <td className="p-4">Réductions CSS Privilèges</td>
                  <td className="text-center p-4">×</td>
                  <td className="text-center p-4 bg-yellow-50">10-15%</td>
                  <td className="text-center p-4">Jusqu'à 25%</td>
                </tr>
                <tr className="border-b border-gray-200">
                  <td className="p-4">Points de fidélité</td>
                  <td className="text-center p-4">×</td>
                  <td className="text-center p-4 bg-yellow-50">✓</td>
                  <td className="text-center p-4">✓</td>
                </tr>
                <tr className="border-b border-gray-200">
                  <td className="p-4">Tombola mensuelle</td>
                  <td className="text-center p-4">×</td>
                  <td className="text-center p-4 bg-yellow-50">×</td>
                  <td className="text-center p-4">✓</td>
                </tr>
                <tr>
                  <td className="p-4">Accès VIP</td>
                  <td className="text-center p-4">×</td>
                  <td className="text-center p-4 bg-yellow-50">×</td>
                  <td className="text-center p-4">✓</td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>
      </section>

      {/* CTA Section */}
      <section className="py-16 bg-gradient-gold text-black">
        <div className="container mx-auto px-4 text-center">
          <h2 className="text-4xl font-bold mb-4">Prêt à nous rejoindre ?</h2>
          <p className="text-xl mb-8 max-w-2xl mx-auto">
            Commencez dès maintenant et profitez des avantages exclusifs réservés aux supporters du CSS
          </p>
          {!isAuthenticated ? (
            <Button variant="primary" size="xl" onClick={() => navigate('/register')}>
              Créer mon compte gratuitement
            </Button>
          ) : currentPlan === 'free' ? (
            <Button variant="primary" size="xl" onClick={() => handleSubscribe('Premium')}>
              Passer à Premium
            </Button>
          ) : (
            <Button variant="primary" size="xl" onClick={() => navigate('/dashboard')}>
              Accéder à mon dashboard
            </Button>
          )}
          <p className="mt-4 opacity-80">
            يا CSS يا نجوم السما ⚽
          </p>
        </div>
      </section>

      {/* FAQ */}
      <section className="py-16 bg-white">
        <div className="container mx-auto px-4">
          <div className="text-center mb-12">
            <h2 className="text-4xl font-bold mb-4">Questions fréquentes</h2>
          </div>

          <div className="max-w-3xl mx-auto space-y-4">
            <Card padding="lg">
              <h4 className="font-bold text-lg mb-2">Comment fonctionne CSS Privilèges ?</h4>
              <p className="text-gray-600">
                CSS Privilèges vous permet de générer des codes de réduction chez nos 29 partenaires à Sfax.
                Il vous suffit de choisir une offre, générer votre code (QR, Promo ou NFC) et le présenter chez le partenaire.
              </p>
            </Card>

            <Card padding="lg">
              <h4 className="font-bold text-lg mb-2">Puis-je annuler mon abonnement ?</h4>
              <p className="text-gray-600">
                Oui, vous pouvez annuler votre abonnement à tout moment depuis votre dashboard.
                Aucun frais d'annulation n'est appliqué.
              </p>
            </Card>

            <Card padding="lg">
              <h4 className="font-bold text-lg mb-2">Comment devenir Socios ?</h4>
              <p className="text-gray-600">
                Le statut Socios est réservé aux membres officiels du club. Contactez-nous pour plus d'informations
                sur les modalités d'adhésion et les avantages exclusifs.
              </p>
            </Card>

            <Card padding="lg">
              <h4 className="font-bold text-lg mb-2">Les points de fidélité, comment ça marche ?</h4>
              <p className="text-gray-600">
                Vous gagnez 10% de points sur chaque achat effectué avec un code CSS Privilèges.
                Ces points vous permettent de débloquer des niveaux (Bronze, Silver, Gold, Platinum) et des récompenses.
              </p>
            </Card>
          </div>
        </div>
      </section>
    </div>
  );
};

export default Upgrade;
