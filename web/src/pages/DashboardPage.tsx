import { useEffect, useState } from 'react'
import { Link } from 'react-router-dom'
import { useAuthStore } from '../store/authStore'
import { Card, Badge, Button, Spinner } from '../components'
import { DashboardLayout } from '../layouts/DashboardLayout'
import { api } from '../services/api'

export const DashboardPage = () => {
  const { user } = useAuthStore()
  const [isLoading, setIsLoading] = useState(true)
  const [featuredContent, setFeaturedContent] = useState([])
  const [upcomingMatches, setUpcomingMatches] = useState([])
  const [activeCampaigns, setActiveCampaigns] = useState([])

  useEffect(() => {
    const fetchData = async () => {
      try {
        const [content, matches, campaigns] = await Promise.all([
          api.getFeaturedContents(),
          api.getMatches({ status: 'upcoming' }),
          api.getCampaigns({ status: 'active' })
        ])
        setFeaturedContent(content.data?.slice(0, 3) || [])
        setUpcomingMatches(matches.data?.slice(0, 3) || [])
        setActiveCampaigns(campaigns.data?.slice(0, 2) || [])
      } catch (error) {
        console.error('Error fetching dashboard data:', error)
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
      {/* Welcome Section */}
      <div className="mb-8">
        <h1 className="text-4xl font-bold text-black mb-2">
          Bienvenue, {user?.name}! 👋
        </h1>
        <p className="text-gray-600">
          Voici un aperçu de ce qui se passe au CSS
        </p>
      </div>

      {/* Quick Stats */}
      <div className="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
        <Card variant="elevated" hover>
          <div className="text-center">
            <div className="text-4xl mb-2">⚽</div>
            <div className="text-2xl font-bold text-black">15</div>
            <div className="text-sm text-gray-600">Matchs à venir</div>
          </div>
        </Card>

        <Card variant="elevated" hover>
          <div className="text-center">
            <div className="text-4xl mb-2">🎁</div>
            <div className="text-2xl font-bold text-black">50+</div>
            <div className="text-sm text-gray-600">Offres Freeoui</div>
          </div>
        </Card>

        <Card variant="elevated" hover>
          <div className="text-center">
            <div className="text-4xl mb-2">🏆</div>
            <div className="text-2xl font-bold text-black">8</div>
            <div className="text-sm text-gray-600">Badges gagnés</div>
          </div>
        </Card>

        <Card variant="elevated" hover>
          <div className="text-center">
            <div className="text-4xl mb-2">⭐</div>
            <div className="text-2xl font-bold text-black">1,250</div>
            <div className="text-sm text-gray-600">Points fidélité</div>
          </div>
        </Card>
      </div>

      <div className="grid grid-cols-1 lg:grid-cols-3 gap-8">
        {/* Main Content */}
        <div className="lg:col-span-2 space-y-8">
          {/* Upcoming Matches */}
          <section>
            <div className="flex items-center justify-between mb-4">
              <h2 className="text-2xl font-bold text-black">Prochains Matchs</h2>
              <Link to="/matches">
                <Button variant="ghost" size="sm">
                  Voir tout →
                </Button>
              </Link>
            </div>

            <div className="space-y-4">
              {upcomingMatches.length > 0 ? (
                upcomingMatches.map((match: any) => (
                  <Card key={match.id} variant="bordered" hover>
                    <div className="flex items-center justify-between">
                      <div className="flex items-center space-x-4">
                        <div className="text-center">
                          <div className="font-bold">{match.home_team}</div>
                          <div className="text-sm text-gray-500">vs</div>
                          <div className="font-bold">{match.away_team}</div>
                        </div>
                      </div>
                      <div className="text-right">
                        <div className="font-semibold">
                          {new Date(match.date).toLocaleDateString('fr-FR')}
                        </div>
                        <div className="text-sm text-gray-600">{match.time}</div>
                        <Badge variant="info" size="sm" className="mt-2">
                          {match.competition}
                        </Badge>
                      </div>
                    </div>
                  </Card>
                ))
              ) : (
                <Card>
                  <p className="text-center text-gray-500">
                    Aucun match à venir pour le moment
                  </p>
                </Card>
              )}
            </div>
          </section>

          {/* Featured Content */}
          <section>
            <div className="flex items-center justify-between mb-4">
              <h2 className="text-2xl font-bold text-black">À la Une</h2>
              <Link to="/content">
                <Button variant="ghost" size="sm">
                  Voir tout →
                </Button>
              </Link>
            </div>

            <div className="grid grid-cols-1 md:grid-cols-2 gap-4">
              {featuredContent.length > 0 ? (
                featuredContent.map((content: any) => (
                  <Card key={content.id} variant="elevated" hover padding="none">
                    <img
                      src={content.image || '/placeholder-image.jpg'}
                      alt={content.title}
                      className="w-full h-48 object-cover rounded-t-xl"
                    />
                    <div className="p-4">
                      <Badge variant="gold" size="sm" className="mb-2">
                        {content.type}
                      </Badge>
                      <h3 className="font-bold text-lg mb-2">{content.title}</h3>
                      <p className="text-sm text-gray-600 line-clamp-2">
                        {content.excerpt}
                      </p>
                    </div>
                  </Card>
                ))
              ) : (
                <Card className="col-span-2">
                  <p className="text-center text-gray-500">
                    Aucun contenu en vedette
                  </p>
                </Card>
              )}
            </div>
          </section>
        </div>

        {/* Sidebar */}
        <div className="space-y-8">
          {/* User Status Card */}
          <Card variant="elevated">
            <div className="text-center mb-4">
              <div className="w-20 h-20 rounded-full bg-css-gold mx-auto flex items-center justify-center mb-3">
                <span className="text-3xl">
                  {user?.user_type === 'socios' ? '⭐' : user?.user_type === 'premium' ? '💎' : '👤'}
                </span>
              </div>
              <h3 className="font-bold text-xl">{user?.name}</h3>
              <Badge
                variant={user?.user_type === 'socios' ? 'gold' : 'default'}
                className="mt-2"
              >
                {user?.user_type.toUpperCase()}
              </Badge>
            </div>

            {user?.user_type === 'free' && (
              <div className="mt-4 p-4 bg-css-gold/10 rounded-lg">
                <p className="text-sm text-center mb-3">
                  Passez au Premium pour débloquer tout le contenu exclusif!
                </p>
                <Button variant="secondary" className="w-full">
                  Devenir Premium
                </Button>
              </div>
            )}
          </Card>

          {/* Active Campaigns */}
          <Card variant="elevated">
            <h3 className="font-bold text-lg mb-4">Campagnes Actives</h3>
            <div className="space-y-3">
              {activeCampaigns.length > 0 ? (
                activeCampaigns.map((campaign: any) => (
                  <div key={campaign.id} className="border-b pb-3 last:border-0">
                    <div className="font-semibold text-sm mb-2">
                      {campaign.title}
                    </div>
                    <div className="w-full bg-gray-200 rounded-full h-2 mb-2">
                      <div
                        className="bg-css-gold h-2 rounded-full"
                        style={{
                          width: `${(campaign.current_amount / campaign.goal_amount) * 100}%`
                        }}
                      />
                    </div>
                    <div className="flex justify-between text-xs text-gray-600">
                      <span>{campaign.current_amount} TND</span>
                      <span>{campaign.goal_amount} TND</span>
                    </div>
                  </div>
                ))
              ) : (
                <p className="text-sm text-gray-500 text-center">
                  Aucune campagne active
                </p>
              )}
            </div>

            <Link to="/campaigns">
              <Button variant="outline" size="sm" className="w-full mt-4">
                Soutenir le club
              </Button>
            </Link>
          </Card>

          {/* Quick Actions */}
          <Card variant="elevated">
            <h3 className="font-bold text-lg mb-4">Actions Rapides</h3>
            <div className="space-y-2">
              <Link to="/shop">
                <Button variant="outline" size="sm" className="w-full">
                  🛍️ Boutique Officielle
                </Button>
              </Link>
              <Link to="/freeoui">
                <Button variant="outline" size="sm" className="w-full">
                  🎁 Offres Freeoui
                </Button>
              </Link>
              <Link to="/auctions">
                <Button variant="outline" size="sm" className="w-full">
                  🔨 Enchères
                </Button>
              </Link>
              <Link to="/polls">
                <Button variant="outline" size="sm" className="w-full">
                  📊 Sondages
                </Button>
              </Link>
            </div>
          </Card>
        </div>
      </div>
    </DashboardLayout>
  )
}

export default DashboardPage
