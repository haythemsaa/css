import { Link } from 'react-router-dom'

const LoginPage = () => {
  return (
    <div className="min-h-screen bg-gradient-to-br from-black via-gray-900 to-black flex items-center justify-center px-4">
      <div className="max-w-md w-full">
        <div className="bg-white/10 backdrop-blur-md rounded-2xl p-8 border border-css-gold/30">
          {/* Logo */}
          <div className="text-center mb-8">
            <div className="w-20 h-20 mx-auto rounded-full bg-css-gold flex items-center justify-center mb-4">
              <span className="text-4xl font-bold text-black">CSS</span>
            </div>
            <h2 className="text-3xl font-bold text-white mb-2">Connexion</h2>
            <p className="text-gray-400">
              Accédez à votre espace membre
            </p>
          </div>

          {/* Form */}
          <form className="space-y-6">
            <div>
              <label className="block text-white mb-2 font-medium">
                Email ou Téléphone
              </label>
              <input
                type="text"
                className="w-full px-4 py-3 rounded-lg bg-white/5 border border-css-gold/30 text-white placeholder-gray-500 focus:outline-none focus:border-css-gold transition"
                placeholder="exemple@email.com"
              />
            </div>

            <div>
              <label className="block text-white mb-2 font-medium">
                Mot de passe
              </label>
              <input
                type="password"
                className="w-full px-4 py-3 rounded-lg bg-white/5 border border-css-gold/30 text-white placeholder-gray-500 focus:outline-none focus:border-css-gold transition"
                placeholder="••••••••"
              />
            </div>

            <div className="flex items-center justify-between">
              <label className="flex items-center text-gray-400">
                <input
                  type="checkbox"
                  className="mr-2 accent-css-gold"
                />
                Se souvenir de moi
              </label>
              <a href="#" className="text-css-gold hover:underline">
                Mot de passe oublié ?
              </a>
            </div>

            <button
              type="submit"
              className="w-full bg-css-gold text-black py-3 rounded-lg font-bold text-lg hover:bg-yellow-500 transition transform hover:scale-105"
            >
              Se connecter
            </button>
          </form>

          {/* Divider */}
          <div className="relative my-8">
            <div className="absolute inset-0 flex items-center">
              <div className="w-full border-t border-gray-700"></div>
            </div>
            <div className="relative flex justify-center text-sm">
              <span className="px-4 bg-transparent text-gray-400">
                Ou continuer avec
              </span>
            </div>
          </div>

          {/* Social Login */}
          <div className="grid grid-cols-2 gap-4">
            <button className="flex items-center justify-center px-4 py-3 rounded-lg bg-white/5 border border-css-gold/30 text-white hover:bg-white/10 transition">
              <span className="mr-2">📘</span>
              Facebook
            </button>
            <button className="flex items-center justify-center px-4 py-3 rounded-lg bg-white/5 border border-css-gold/30 text-white hover:bg-white/10 transition">
              <span className="mr-2">🔍</span>
              Google
            </button>
          </div>

          {/* Sign Up Link */}
          <p className="text-center text-gray-400 mt-8">
            Pas encore de compte ?{' '}
            <Link to="/register" className="text-css-gold hover:underline font-semibold">
              Créer un compte
            </Link>
          </p>
        </div>

        {/* Back to Home */}
        <div className="text-center mt-6">
          <Link to="/" className="text-gray-400 hover:text-css-gold transition">
            ← Retour à l'accueil
          </Link>
        </div>
      </div>
    </div>
  )
}

export default LoginPage
