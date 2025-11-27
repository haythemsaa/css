import { useEffect, useState } from 'react'
import { useParams, Link } from 'react-router-dom'
import { DashboardLayout } from '../layouts/DashboardLayout'
import { Card, Badge, Button, Spinner } from '../components'
import { api } from '../services/api'

export const MatchDetailPage = () => {
  const { id } = useParams()
  const [match, setMatch] = useState<any>(null)
  const [isLoading, setIsLoading] = useState(true)
  const [liveData, setLiveData] = useState<any>(null)

  useEffect(() => {
    const fetchMatch = async () => {
      try {
        const response = await api.getMatch(Number(id))
        setMatch(response.data)

        // Si le match est en direct, récupérer les données live
        if (response.data.status === 'live') {
          const liveResponse = await api.getLiveMatch(Number(id))
          setLiveData(liveResponse.data)
        }
      } catch (error) {
        console.error('Error fetching match:', error)
      } finally {
        setIsLoading(false)
      }
    }

    fetchMatch()

    // Rafraîchir les données live toutes les 30 secondes
    const interval = setInterval(() => {
      if (match?.status === 'live') {
        api.getLiveMatch(Number(id)).then(res => setLiveData(res.data))
      }
    }, 30000)

    return () => clearInterval(interval)
  }, [id])

  if (isLoading) {
    return (
      <DashboardLayout>
        <Spinner size="xl" className="h-screen" />
      </DashboardLayout>
    )
  }

  if (!match) {
    return (
      <DashboardLayout>
        <Card>
          <p className="text-center text-gray-500 py-8">Match introuvable</p>
          <Link to="/matches">
            <Button variant="outline" className="mx-auto block">
              Retour aux matchs
            </Button>
          </Link>
        </Card>
      </DashboardLayout>
    )
  }

  return (
    <DashboardLayout>
      <div className="max-w-4xl mx-auto">
        {/* Header */}
        <div className="mb-6">
          <Link to="/matches">
            <Button variant="ghost" size="sm">← Retour aux matchs</Button>
          </Link>
        </div>

        {/* Match Info Card */}
        <Card variant="elevated" className="mb-6">
          <div className="text-center mb-6">
            <Badge variant="info" className="mb-3">
              {match.competition}
            </Badge>
            {match.status === 'live' && (
              <Badge variant="danger" size="lg" className="ml-3">
                🔴 EN DIRECT
              </Badge>
            )}
            <div className="text-sm text-gray-600 mt-2">
              {new Date(match.date).toLocaleDateString('fr-FR', {
                weekday: 'long',
                year: 'numeric',
                month: 'long',
                day: 'numeric'
              })} • {match.time}
            </div>
            <div className="text-sm text-gray-600">
              📍 {match.venue}
            </div>
          </div>

          {/* Score Section */}
          <div className="grid grid-cols-3 gap-8 items-center my-8">
            {/* Home Team */}
            <div className="text-center">
              <div className="w-24 h-24 mx-auto bg-black rounded-full flex items-center justify-center mb-3">
                <span className="text-white font-bold text-2xl">CSS</span>
              </div>
              <h2 className="text-2xl font-bold">{match.home_team}</h2>
              <p className="text-sm text-gray-600">Domicile</p>
            </div>

            {/* Score */}
            <div className="text-center">
              {match.status === 'completed' || match.status === 'live' ? (
                <>
                  <div className="text-6xl font-bold mb-2">
                    {liveData?.home_score ?? match.home_score} - {liveData?.away_score ?? match.away_score}
                  </div>
                  {match.status === 'live' && liveData?.minute && (
                    <Badge variant="danger">{liveData.minute}'</Badge>
                  )}
                </>
              ) : (
                <div className="text-2xl font-semibold text-gray-600">
                  {match.time}
                </div>
              )}
            </div>

            {/* Away Team */}
            <div className="text-center">
              <div className="w-24 h-24 mx-auto bg-gray-200 rounded-full flex items-center justify-center mb-3">
                <span className="text-gray-600 font-bold text-xl">
                  {match.away_team.substring(0, 3)}
                </span>
              </div>
              <h2 className="text-2xl font-bold">{match.away_team}</h2>
              <p className="text-sm text-gray-600">Extérieur</p>
            </div>
          </div>
        </Card>

        <div className="grid grid-cols-1 lg:grid-cols-3 gap-6">
          {/* Main Content */}
          <div className="lg:col-span-2 space-y-6">
            {/* Live Events */}
            {match.status === 'live' && liveData?.events && (
              <Card variant="elevated">
                <h3 className="text-xl font-bold mb-4">⚡ Événements en Direct</h3>
                <div className="space-y-3">
                  {liveData.events.map((event: any, index: number) => (
                    <div key={index} className="flex items-center gap-3 p-3 bg-gray-50 rounded-lg">
                      <Badge variant="default" size="sm">{event.minute}'</Badge>
                      <div className="flex-1">
                        <div className="font-semibold">{event.type}</div>
                        <div className="text-sm text-gray-600">{event.player}</div>
                      </div>
                      <div className="text-2xl">
                        {event.type === 'goal' ? '⚽' : event.type === 'yellow_card' ? '🟨' : '🔴'}
                      </div>
                    </div>
                  ))}
                </div>
              </Card>
            )}

            {/* Statistics */}
            {match.statistics && (
              <Card variant="elevated">
                <h3 className="text-xl font-bold mb-4">📊 Statistiques</h3>
                <div className="space-y-4">
                  {Object.entries(match.statistics).map(([key, value]: [string, any]) => (
                    <div key={key}>
                      <div className="flex justify-between text-sm mb-2">
                        <span>{key}</span>
                        <span className="font-semibold">{value.home} - {value.away}</span>
                      </div>
                      <div className="w-full bg-gray-200 rounded-full h-2">
                        <div
                          className="bg-black h-2 rounded-full"
                          style={{
                            width: `${(value.home / (value.home + value.away)) * 100}%`
                          }}
                        />
                      </div>
                    </div>
                  ))}
                </div>
              </Card>
            )}

            {/* Lineups */}
            {match.lineups && (
              <Card variant="elevated">
                <h3 className="text-xl font-bold mb-4">👥 Compositions</h3>
                <div className="grid grid-cols-2 gap-6">
                  <div>
                    <h4 className="font-semibold mb-3">{match.home_team}</h4>
                    <div className="space-y-2">
                      {match.lineups.home?.map((player: any, index: number) => (
                        <div key={index} className="flex items-center gap-2">
                          <Badge variant="default" size="sm">{player.number}</Badge>
                          <span className="text-sm">{player.name}</span>
                        </div>
                      ))}
                    </div>
                  </div>
                  <div>
                    <h4 className="font-semibold mb-3">{match.away_team}</h4>
                    <div className="space-y-2">
                      {match.lineups.away?.map((player: any, index: number) => (
                        <div key={index} className="flex items-center gap-2">
                          <Badge variant="default" size="sm">{player.number}</Badge>
                          <span className="text-sm">{player.name}</span>
                        </div>
                      ))}
                    </div>
                  </div>
                </div>
              </Card>
            )}
          </div>

          {/* Sidebar */}
          <div className="space-y-6">
            {/* Match Info */}
            <Card variant="elevated">
              <h3 className="font-bold mb-4">Informations</h3>
              <div className="space-y-3 text-sm">
                <div>
                  <span className="text-gray-600">Compétition:</span>
                  <div className="font-semibold">{match.competition}</div>
                </div>
                <div>
                  <span className="text-gray-600">Stade:</span>
                  <div className="font-semibold">{match.venue}</div>
                </div>
                <div>
                  <span className="text-gray-600">Arbitre:</span>
                  <div className="font-semibold">{match.referee || 'N/A'}</div>
                </div>
                <div>
                  <span className="text-gray-600">Spectateurs:</span>
                  <div className="font-semibold">
                    {match.attendance?.toLocaleString() || 'N/A'}
                  </div>
                </div>
              </div>
            </Card>

            {/* Actions */}
            {match.status === 'upcoming' && (
              <Card variant="bordered">
                <h3 className="font-bold mb-4">🎫 Billets</h3>
                <p className="text-sm text-gray-600 mb-4">
                  Réservez vos billets pour ce match
                </p>
                <Button variant="primary" className="w-full">
                  Acheter des billets
                </Button>
              </Card>
            )}
          </div>
        </div>
      </div>
    </DashboardLayout>
  )
}

export default MatchDetailPage
