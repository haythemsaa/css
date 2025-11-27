import { useEffect, useState } from 'react'
import { Link } from 'react-router-dom'
import { DashboardLayout } from '../layouts/DashboardLayout'
import { Card, Badge, Button, Spinner } from '../components'
import { api } from '../services/api'

export const MatchesPage = () => {
  const [matches, setMatches] = useState([])
  const [isLoading, setIsLoading] = useState(true)
  const [filter, setFilter] = useState('all')

  useEffect(() => {
    const fetchMatches = async () => {
      try {
        const params = filter !== 'all' ? { status: filter } : {}
        const response = await api.getMatches(params)
        setMatches(response.data || [])
      } catch (error) {
        console.error('Error fetching matches:', error)
      } finally {
        setIsLoading(false)
      }
    }

    fetchMatches()
  }, [filter])

  if (isLoading) {
    return (
      <DashboardLayout>
        <Spinner size="xl" className="h-screen" />
      </DashboardLayout>
    )
  }

  return (
    <DashboardLayout>
      <div className="mb-8">
        <h1 className="text-4xl font-bold text-black mb-4">Matchs</h1>

        {/* Filters */}
        <div className="flex flex-wrap gap-2">
          <Button
            variant={filter === 'all' ? 'primary' : 'outline'}
            size="sm"
            onClick={() => setFilter('all')}
          >
            Tous
          </Button>
          <Button
            variant={filter === 'upcoming' ? 'primary' : 'outline'}
            size="sm"
            onClick={() => setFilter('upcoming')}
          >
            À venir
          </Button>
          <Button
            variant={filter === 'live' ? 'primary' : 'outline'}
            size="sm"
            onClick={() => setFilter('live')}
          >
            En direct
          </Button>
          <Button
            variant={filter === 'completed' ? 'primary' : 'outline'}
            size="sm"
            onClick={() => setFilter('completed')}
          >
            Terminés
          </Button>
        </div>
      </div>

      {/* Matches Grid */}
      <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        {matches.length > 0 ? (
          matches.map((match: any) => (
            <Card key={match.id} variant="elevated" hover>
              {/* Match Header */}
              <div className="flex items-center justify-between mb-4">
                <Badge variant="info" size="sm">
                  {match.competition}
                </Badge>
                {match.status === 'live' && (
                  <Badge variant="danger" size="sm">
                    🔴 EN DIRECT
                  </Badge>
                )}
              </div>

              {/* Teams */}
              <div className="mb-4">
                {/* Home Team */}
                <div className="flex items-center justify-between mb-3">
                  <div className="flex items-center space-x-3">
                    <div className="w-12 h-12 bg-black rounded-full flex items-center justify-center">
                      <span className="text-white font-bold">CSS</span>
                    </div>
                    <div>
                      <div className="font-bold">{match.home_team}</div>
                      <div className="text-xs text-gray-500">Domicile</div>
                    </div>
                  </div>
                  {match.status === 'completed' && (
                    <div className="text-3xl font-bold">{match.home_score}</div>
                  )}
                </div>

                {/* Away Team */}
                <div className="flex items-center justify-between">
                  <div className="flex items-center space-x-3">
                    <div className="w-12 h-12 bg-gray-200 rounded-full flex items-center justify-center">
                      <span className="text-gray-600 font-bold text-xs">
                        {match.away_team.substring(0, 3)}
                      </span>
                    </div>
                    <div>
                      <div className="font-bold">{match.away_team}</div>
                      <div className="text-xs text-gray-500">Extérieur</div>
                    </div>
                  </div>
                  {match.status === 'completed' && (
                    <div className="text-3xl font-bold">{match.away_score}</div>
                  )}
                </div>
              </div>

              {/* Match Info */}
              <div className="border-t pt-4">
                <div className="flex items-center justify-between text-sm">
                  <div className="text-gray-600">
                    📅 {new Date(match.date).toLocaleDateString('fr-FR')}
                  </div>
                  <div className="font-semibold">{match.time}</div>
                </div>
                <div className="text-sm text-gray-600 mt-1">
                  📍 {match.venue}
                </div>
              </div>

              {/* Actions */}
              <div className="mt-4">
                <Link to={`/matches/${match.id}`}>
                  <Button variant="outline" size="sm" className="w-full">
                    {match.status === 'live' ? 'Suivre en direct' : 'Voir détails'}
                  </Button>
                </Link>
              </div>
            </Card>
          ))
        ) : (
          <div className="col-span-full">
            <Card>
              <p className="text-center text-gray-500 py-8">
                Aucun match trouvé
              </p>
            </Card>
          </div>
        )}
      </div>
    </DashboardLayout>
  )
}

export default MatchesPage
