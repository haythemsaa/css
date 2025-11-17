import React from 'react';
import { Link } from 'react-router-dom';
import { Button, Card, Badge } from '../../components/common';

const Home = () => {
  const features = [
    {
      title: 'Freeoui - Réductions Exclusives',
      description: 'Accédez à plus de 60 offres chez 29 partenaires à Sfax. Bénéficiez de 10 à 25% de réduction selon votre abonnement.',
      icon: '🎁',
      color: 'bg-blue-50',
    },
    {
      title: 'Contenu Premium',
      description: 'Vidéos en HD/4K, articles exclusifs, podcasts, et galeries photos. Suivez toute l\'actualité du CSS.',
      icon: '📺',
      color: 'bg-purple-50',
    },
    {
      title: 'Programme de Fidélité',
      description: 'Gagnez des points sur chaque achat et débloquez des avantages exclusifs. 4 niveaux de récompenses.',
      icon: '⭐',
      color: 'bg-yellow-50',
    },
    {
      title: 'Gamification',
      description: 'Collectionnez des cartes, gagnez des badges, participez à la tombola Socios et bien plus encore.',
      icon: '🏆',
      color: 'bg-green-50',
    },
  ];

  const subscriptionPlans = [
    {
      name: 'Free',
      price: '0',
      period: 'Gratuit',
      features: [
        'Accès contenu public',
        'Navigation partenaires',
        'Calendrier des matchs',
        'Informations de l\'équipe',
      ],
      buttonText: 'Commencer',
      buttonVariant: 'outline',
      recommended: false,
    },
    {
      name: 'Premium',
      price: '15',
      period: '/ mois',
      features: [
        'Tous les avantages Free',
        'Contenu premium HD',
        'Réductions Freeoui 10-15%',
        'Génération codes QR/Promo',
        'Points de fidélité',
        'Support prioritaire',
      ],
      buttonText: 'Devenir Premium',
      buttonVariant: 'secondary',
      recommended: true,
    },
    {
      name: 'Socios',
      price: 'VIP',
      period: 'Membre officiel',
      features: [
        'Tous les avantages Premium',
        'Réductions Freeoui jusqu\'à 25%',
        'Accès VIP exclusif',
        'Tombola mensuelle',
        'Cadeaux personnalisés',
        'Badge Socios vérifié',
      ],
      buttonText: 'Devenir Socios',
      buttonVariant: 'primary',
      recommended: false,
    },
  ];

  return (
    <div className="animate-fadeIn">
      {/* Hero Section */}
      <section className="bg-black text-white py-20">
        <div className="container mx-auto px-4">
          <div className="max-w-4xl mx-auto text-center">
            <div className="mb-6">
              <Badge variant="secondary" size="lg">
                Plateforme Officielle du CSS
              </Badge>
            </div>

            <h1 className="text-5xl md:text-6xl font-bold mb-6">
              Rejoignez la <span className="text-gradient-gold">Famille CSS</span>
            </h1>

            <p className="text-xl text-gray-300 mb-8 max-w-2xl mx-auto">
              Accédez à des réductions exclusives chez nos partenaires avec Freeoui,
              suivez toute l'actualité du club, et profitez d'avantages uniques.
            </p>

            <div className="flex flex-col sm:flex-row gap-4 justify-center">
              <Link to="/register">
                <Button variant="secondary" size="lg">
                  Créer un compte gratuit
                </Button>
              </Link>
              <Link to="/partners">
                <Button variant="outline" size="lg">
                  Découvrir Freeoui
                </Button>
              </Link>
            </div>

            <div className="mt-12 grid grid-cols-3 gap-8 max-w-2xl mx-auto">
              <div>
                <div className="text-4xl font-bold text-css-gold">29</div>
                <div className="text-gray-400 text-sm">Partenaires</div>
              </div>
              <div>
                <div className="text-4xl font-bold text-css-gold">60+</div>
                <div className="text-gray-400 text-sm">Offres actives</div>
              </div>
              <div>
                <div className="text-4xl font-bold text-css-gold">25%</div>
                <div className="text-gray-400 text-sm">Réduction max</div>
              </div>
            </div>
          </div>
        </div>
      </section>

      {/* Features Section */}
      <section className="py-16 bg-gray-50">
        <div className="container mx-auto px-4">
          <div className="text-center mb-12">
            <h2 className="text-4xl font-bold mb-4">Pourquoi rejoindre la plateforme CSS ?</h2>
            <p className="text-gray-600 max-w-2xl mx-auto">
              Une plateforme complète dédiée aux supporters du Club Sportif Sfaxien
            </p>
          </div>

          <div className="grid md:grid-cols-2 lg:grid-cols-4 gap-6">
            {features.map((feature, index) => (
              <Card key={index} padding="lg" hover className="text-center">
                <div className={`w-16 h-16 ${feature.color} rounded-full flex items-center justify-center text-3xl mx-auto mb-4`}>
                  {feature.icon}
                </div>
                <h3 className="text-xl font-bold mb-2">{feature.title}</h3>
                <p className="text-gray-600 text-sm">{feature.description}</p>
              </Card>
            ))}
          </div>
        </div>
      </section>

      {/* Pricing Section */}
      <section className="py-16 bg-white">
        <div className="container mx-auto px-4">
          <div className="text-center mb-12">
            <h2 className="text-4xl font-bold mb-4">Choisissez votre abonnement</h2>
            <p className="text-gray-600 max-w-2xl mx-auto">
              Trois niveaux d'accès adaptés à vos besoins
            </p>
          </div>

          <div className="grid md:grid-cols-3 gap-8 max-w-6xl mx-auto">
            {subscriptionPlans.map((plan, index) => (
              <Card
                key={index}
                variant={plan.recommended ? 'gold' : 'default'}
                padding="lg"
                className="relative"
              >
                {plan.recommended && (
                  <div className="absolute -top-4 left-1/2 transform -translate-x-1/2">
                    <Badge variant="secondary">Recommandé</Badge>
                  </div>
                )}

                <div className="text-center mb-6">
                  <h3 className="text-2xl font-bold mb-2">{plan.name}</h3>
                  <div className="mb-4">
                    <span className="text-4xl font-bold">{plan.price}</span>
                    <span className="text-gray-600 ml-1">{plan.period}</span>
                  </div>
                </div>

                <ul className="space-y-3 mb-8">
                  {plan.features.map((feature, idx) => (
                    <li key={idx} className="flex items-start text-sm">
                      <span className="text-green-500 mr-2">✓</span>
                      <span>{feature}</span>
                    </li>
                  ))}
                </ul>

                <Link to="/register">
                  <Button variant={plan.buttonVariant} fullWidth>
                    {plan.buttonText}
                  </Button>
                </Link>
              </Card>
            ))}
          </div>
        </div>
      </section>

      {/* CTA Section */}
      <section className="py-16 bg-gradient-gold text-black">
        <div className="container mx-auto px-4 text-center">
          <h2 className="text-4xl font-bold mb-4">Prêt à commencer ?</h2>
          <p className="text-xl mb-8 max-w-2xl mx-auto">
            Rejoignez des milliers de supporters et profitez dès maintenant des avantages Freeoui
          </p>
          <Link to="/register">
            <Button variant="primary" size="xl">
              Créer mon compte gratuitement
            </Button>
          </Link>
          <p className="mt-4 text-sm opacity-80">
            يا CSS يا نجوم السما ⚽
          </p>
        </div>
      </section>
    </div>
  );
};

export default Home;
