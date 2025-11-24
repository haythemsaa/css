import { Link } from 'react-router-dom'

const HomePage = () => {
  return (
    <div className="min-h-screen bg-gradient-to-br from-black via-gray-900 to-black">
      {/* Header */}
      <header className="bg-black/50 backdrop-blur-md border-b border-css-gold/20">
        <div className="container mx-auto px-4 py-4">
          <div className="flex items-center justify-between">
            <div className="flex items-center space-x-4">
              <div className="w-12 h-12 rounded-full bg-css-gold flex items-center justify-center">
                <span className="text-black font-bold text-xl">CSS</span>
              </div>
              <h1 className="text-2xl font-bold text-css-gold">
                Club Sportif Sfaxien
              </h1>
            </div>
            <nav className="hidden md:flex space-x-6">
              <a href="#" className="text-white hover:text-css-gold transition">
                Actualités
              </a>
              <a href="#" className="text-white hover:text-css-gold transition">
                Matchs
              </a>
              <a href="#" className="text-white hover:text-css-gold transition">
                Joueurs
              </a>
              <a href="#" className="text-white hover:text-css-gold transition">
                Freeoui
              </a>
              <Link
                to="/login"
                className="bg-css-gold text-black px-6 py-2 rounded-lg font-semibold hover:bg-yellow-500 transition"
              >
                Connexion
              </Link>
            </nav>
          </div>
        </div>
      </header>

      {/* Hero Section */}
      <main className="container mx-auto px-4 py-16">
        <div className="text-center space-y-8">
          <div className="w-32 h-32 mx-auto rounded-full bg-black border-4 border-css-gold flex items-center justify-center mb-8">
            <span className="text-6xl font-bold text-css-gold">CSS</span>
          </div>

          <h2 className="text-5xl md:text-7xl font-bold text-white">
            Club Sportif Sfaxien
          </h2>

          <p className="text-xl md:text-2xl text-gray-300 max-w-2xl mx-auto">
            Application Officielle - Contenu exclusif, Réductions partenaires,
            Cadeaux et bien plus encore !
          </p>

          <div className="flex flex-col md:flex-row items-center justify-center gap-4 mt-12">
            <Link
              to="/login"
              className="bg-css-gold text-black px-8 py-4 rounded-lg font-bold text-lg hover:bg-yellow-500 transition transform hover:scale-105"
            >
              Commencer Maintenant
            </Link>
            <a
              href="#features"
              className="bg-white/10 backdrop-blur-md text-white px-8 py-4 rounded-lg font-bold text-lg hover:bg-white/20 transition border border-css-gold/30"
            >
              Découvrir
            </a>
          </div>
        </div>

        {/* Features Grid */}
        <div id="features" className="grid grid-cols-1 md:grid-cols-3 gap-8 mt-24">
          <div className="bg-white/5 backdrop-blur-md rounded-xl p-8 border border-css-gold/20 hover:border-css-gold/50 transition">
            <div className="text-4xl mb-4">📰</div>
            <h3 className="text-2xl font-bold text-css-gold mb-4">
              Contenu Exclusif
            </h3>
            <p className="text-gray-300">
              Articles, vidéos, podcasts et stories exclusives. Accès aux coulisses
              du club et interviews des joueurs.
            </p>
          </div>

          <div className="bg-white/5 backdrop-blur-md rounded-xl p-8 border border-css-gold/20 hover:border-css-gold/50 transition">
            <div className="text-4xl mb-4">🎁</div>
            <h3 className="text-2xl font-bold text-css-gold mb-4">
              Système Freeoui
            </h3>
            <p className="text-gray-300">
              Réductions chez 50+ partenaires. QR codes, offres flash et tracking
              d'économies réalisées.
            </p>
          </div>

          <div className="bg-white/5 backdrop-blur-md rounded-xl p-8 border border-css-gold/20 hover:border-css-gold/50 transition">
            <div className="text-4xl mb-4">⚽</div>
            <h3 className="text-2xl font-bold text-css-gold mb-4">
              Matchs en Direct
            </h3>
            <p className="text-gray-300">
              Suivi live des matchs avec statistiques temps réel, compositions
              d'équipe et commentaires audio.
            </p>
          </div>

          <div className="bg-white/5 backdrop-blur-md rounded-xl p-8 border border-css-gold/20 hover:border-css-gold/50 transition">
            <div className="text-4xl mb-4">🎟️</div>
            <h3 className="text-2xl font-bold text-css-gold mb-4">
              Cadeaux & Loteries
            </h3>
            <p className="text-gray-300">
              Programme de fidélité, cadeaux mensuels, loteries et cartes à
              collectionner échangeables.
            </p>
          </div>

          <div className="bg-white/5 backdrop-blur-md rounded-xl p-8 border border-css-gold/20 hover:border-css-gold/50 transition">
            <div className="text-4xl mb-4">👥</div>
            <h3 className="text-2xl font-bold text-css-gold mb-4">
              Espace Socios
            </h3>
            <p className="text-gray-300">
              Accès Premium à vie, événements exclusifs, réductions majorées et
              participation aux décisions du club.
            </p>
          </div>

          <div className="bg-white/5 backdrop-blur-md rounded-xl p-8 border border-css-gold/20 hover:border-css-gold/50 transition">
            <div className="text-4xl mb-4">💰</div>
            <h3 className="text-2xl font-bold text-css-gold mb-4">
              Dons & Crowdfunding
            </h3>
            <p className="text-gray-300">
              Soutenez votre club avec des dons ciblés, crowdfunding transparent et
              rapports trimestriels.
            </p>
          </div>
        </div>
      </main>

      {/* Footer */}
      <footer className="bg-black border-t border-css-gold/20 mt-24 py-12">
        <div className="container mx-auto px-4 text-center">
          <p className="text-gray-400">
            © 2024 Club Sportif Sfaxien. Tous droits réservés.
          </p>
          <div className="flex justify-center space-x-6 mt-6">
            <a href="#" className="text-gray-400 hover:text-css-gold transition">
              Facebook
            </a>
            <a href="#" className="text-gray-400 hover:text-css-gold transition">
              Twitter
            </a>
            <a href="#" className="text-gray-400 hover:text-css-gold transition">
              Instagram
            </a>
            <a href="#" className="text-gray-400 hover:text-css-gold transition">
              YouTube
            </a>
          </div>
        </div>
      </footer>
    </div>
  )
}

export default HomePage
