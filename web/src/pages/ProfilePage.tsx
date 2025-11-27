import { useEffect, useState } from 'react'
import { DashboardLayout } from '../layouts/DashboardLayout'
import { Card, Badge, Button, Input, Spinner } from '../components'
import { useAuthStore } from '../store/authStore'
import { api } from '../services/api'
import toast from 'react-hot-toast'

export const ProfilePage = () => {
  const { user, fetchProfile } = useAuthStore()
  const [isLoading, setIsLoading] = useState(true)
  const [isEditing, setIsEditing] = useState(false)
  const [badges, setBadges] = useState([])
  const [stats, setStats] = useState({
    totalDonations: 0,
    totalSpent: 0,
    loyaltyPoints: 0,
    badgesCount: 0
  })

  useEffect(() => {
    const fetchData = async () => {
      try {
        await fetchProfile()
        const badgesResponse = await api.getUserBadges()
        setBadges(badgesResponse.data || [])
        // Fetch stats would go here
      } catch (error) {
        console.error('Error fetching profile data:', error)
      } finally {
        setIsLoading(false)
      }
    }

    fetchData()
  }, [])

  if (isLoading) {
    return (
      <DashboardLayout>
        <Spinner size="xl" className="h-screen" />
      </DashboardLayout>
    )
  }

  return (
    <DashboardLayout>
      <div className="max-w-6xl mx-auto">
        {/* Profile Header */}
        <Card variant="elevated" className="mb-8">
          <div className="flex flex-col md:flex-row items-center md:items-start gap-6">
            {/* Avatar */}
            <div className="w-32 h-32 rounded-full bg-css-gold flex items-center justify-center text-6xl">
              {user?.user_type === 'socios' ? '⭐' : user?.user_type === 'premium' ? '💎' : '👤'}
            </div>

            {/* User Info */}
            <div className="flex-1 text-center md:text-left">
              <h1 className="text-3xl font-bold text-black mb-2">
                {user?.name}
              </h1>
              <p className="text-gray-600 mb-3">{user?.email}</p>
              <div className="flex flex-wrap gap-2 justify-center md:justify-start">
                <Badge
                  variant={user?.user_type === 'socios' ? 'gold' : 'default'}
                  size="lg"
                >
                  {user?.user_type.toUpperCase()}
                </Badge>
                <Badge variant="info">
                  Membre depuis {new Date().getFullYear()}
                </Badge>
              </div>
            </div>

            {/* Actions */}
            <div>
              <Button
                variant={isEditing ? 'primary' : 'outline'}
                onClick={() => setIsEditing(!isEditing)}
              >
                {isEditing ? 'Enregistrer' : 'Modifier le profil'}
              </Button>
            </div>
          </div>
        </Card>

        {/* Stats Grid */}
        <div className="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
          <Card variant="elevated">
            <div className="text-center">
              <div className="text-4xl mb-2">💰</div>
              <div className="text-2xl font-bold text-black">
                {stats.totalDonations} TND
              </div>
              <div className="text-sm text-gray-600">Total des dons</div>
            </div>
          </Card>

          <Card variant="elevated">
            <div className="text-center">
              <div className="text-4xl mb-2">🛍️</div>
              <div className="text-2xl font-bold text-black">
                {stats.totalSpent} TND
              </div>
              <div className="text-sm text-gray-600">Achats boutique</div>
            </div>
          </Card>

          <Card variant="elevated">
            <div className="text-center">
              <div className="text-4xl mb-2">⭐</div>
              <div className="text-2xl font-bold text-black">
                {stats.loyaltyPoints}
              </div>
              <div className="text-sm text-gray-600">Points fidélité</div>
            </div>
          </Card>

          <Card variant="elevated">
            <div className="text-center">
              <div className="text-4xl mb-2">🏆</div>
              <div className="text-2xl font-bold text-black">
                {badges.length}
              </div>
              <div className="text-sm text-gray-600">Badges</div>
            </div>
          </Card>
        </div>

        <div className="grid grid-cols-1 lg:grid-cols-3 gap-8">
          {/* Main Content */}
          <div className="lg:col-span-2 space-y-6">
            {/* Edit Profile Form */}
            {isEditing && (
              <Card variant="elevated">
                <h2 className="text-2xl font-bold mb-4">
                  Informations personnelles
                </h2>
                <div className="space-y-4">
                  <Input label="Nom complet" defaultValue={user?.name} />
                  <Input label="Email" type="email" defaultValue={user?.email} />
                  <Input label="Téléphone" type="tel" defaultValue={user?.phone} />
                  <Input label="Adresse" defaultValue="" />

                  <div className="flex gap-3">
                    <Button variant="primary" className="flex-1">
                      Enregistrer les modifications
                    </Button>
                    <Button
                      variant="outline"
                      onClick={() => setIsEditing(false)}
                    >
                      Annuler
                    </Button>
                  </div>
                </div>
              </Card>
            )}

            {/* Badges Section */}
            <Card variant="elevated">
              <h2 className="text-2xl font-bold mb-4">Mes Badges 🏆</h2>

              {badges.length > 0 ? (
                <div className="grid grid-cols-2 md:grid-cols-3 gap-4">
                  {badges.map((badge: any) => (
                    <div
                      key={badge.id}
                      className="text-center p-4 border-2 border-css-gold/20 rounded-xl hover:bg-css-gold/5 transition"
                    >
                      <div className="text-4xl mb-2">{badge.icon || '🏆'}</div>
                      <div className="font-semibold text-sm mb-1">
                        {badge.name}
                      </div>
                      <div className="text-xs text-gray-500">
                        {badge.description}
                      </div>
                      <div className="text-xs text-css-gold mt-2">
                        {new Date(badge.earned_at).toLocaleDateString('fr-FR')}
                      </div>
                    </div>
                  ))}
                </div>
              ) : (
                <p className="text-center text-gray-500 py-8">
                  Vous n'avez pas encore de badges. Participez aux activités pour
                  en gagner!
                </p>
              )}
            </Card>

            {/* Recent Activity */}
            <Card variant="elevated">
              <h2 className="text-2xl font-bold mb-4">Activité Récente</h2>
              <div className="space-y-3">
                <div className="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                  <div>
                    <div className="font-semibold">Don à la campagne infrastructures</div>
                    <div className="text-sm text-gray-600">Il y a 2 jours</div>
                  </div>
                  <div className="font-bold text-css-gold">50 TND</div>
                </div>

                <div className="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                  <div>
                    <div className="font-semibold">Achat Maillot Domicile 2024</div>
                    <div className="text-sm text-gray-600">Il y a 5 jours</div>
                  </div>
                  <div className="font-bold text-black">120 TND</div>
                </div>

                <div className="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                  <div>
                    <div className="font-semibold">Code réduction Restaurant Le Sfaxien</div>
                    <div className="text-sm text-gray-600">Il y a 1 semaine</div>
                  </div>
                  <Badge variant="success" size="sm">
                    Utilisé
                  </Badge>
                </div>
              </div>
            </Card>
          </div>

          {/* Sidebar */}
          <div className="space-y-6">
            {/* Membership Card */}
            <Card variant="elevated" className="bg-gradient-to-br from-black to-gray-800 text-white">
              <div className="text-center">
                <div className="text-4xl mb-3">
                  {user?.user_type === 'socios' ? '⭐' : user?.user_type === 'premium' ? '💎' : '🎫'}
                </div>
                <h3 className="text-2xl font-bold mb-2">
                  {user?.user_type.toUpperCase()}
                </h3>
                <p className="text-gray-300 text-sm mb-4">
                  Membre depuis {new Date().getFullYear()}
                </p>

                {user?.user_type === 'free' && (
                  <Button variant="secondary" className="w-full">
                    Passer au Premium
                  </Button>
                )}
              </div>
            </Card>

            {/* Quick Actions */}
            <Card variant="elevated">
              <h3 className="font-bold text-lg mb-4">Actions Rapides</h3>
              <div className="space-y-2">
                <Button variant="outline" size="sm" className="w-full">
                  🔒 Changer le mot de passe
                </Button>
                <Button variant="outline" size="sm" className="w-full">
                  📧 Gérer les notifications
                </Button>
                <Button variant="outline" size="sm" className="w-full">
                  📱 Télécharger l'app mobile
                </Button>
                <Button variant="outline" size="sm" className="w-full">
                  📜 Historique des commandes
                </Button>
              </div>
            </Card>

            {/* Support */}
            <Card variant="bordered">
              <div className="text-center">
                <div className="text-3xl mb-2">💬</div>
                <h3 className="font-bold mb-2">Besoin d'aide?</h3>
                <p className="text-sm text-gray-600 mb-4">
                  Notre équipe est là pour vous aider
                </p>
                <Button variant="outline" size="sm" className="w-full">
                  Contacter le support
                </Button>
              </div>
            </Card>
          </div>
        </div>
      </div>
    </DashboardLayout>
  )
}

export default ProfilePage
