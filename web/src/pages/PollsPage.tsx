import { useEffect, useState } from 'react'
import { DashboardLayout } from '../layouts/DashboardLayout'
import { Card, Badge, Button, Spinner } from '../components'
import { api } from '../services/api'
import toast from 'react-hot-toast'

export const PollsPage = () => {
  const [polls, setPolls] = useState([])
  const [isLoading, setIsLoading] = useState(true)

  useEffect(() => {
    fetchPolls()
  }, [])

  const fetchPolls = async () => {
    try {
      const response = await api.getPolls({ status: 'active' })
      setPolls(response.data || [])
    } catch (error) {
      console.error('Error fetching polls:', error)
    } finally {
      setIsLoading(false)
    }
  }

  const handleVote = async (pollId: number, optionId: number) => {
    try {
      await api.votePoll(pollId, optionId)
      toast.success('Vote enregistré!')
      fetchPolls() // Refresh to show updated results
    } catch (error: any) {
      toast.error(error.response?.data?.message || 'Erreur lors du vote')
    }
  }

  const calculatePercentage = (votes: number, total: number) => {
    if (total === 0) return 0
    return ((votes / total) * 100).toFixed(1)
  }

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
        <h1 className="text-4xl font-bold text-black mb-2">Sondages 📊</h1>
        <p className="text-gray-600">
          Donnez votre avis sur les sujets qui concernent le CSS
        </p>
      </div>

      <div className="max-w-4xl mx-auto space-y-6">
        {polls.length > 0 ? (
          polls.map((poll: any) => (
            <Card key={poll.id} variant="elevated">
              {/* Poll Header */}
              <div className="mb-6">
                <div className="flex items-center gap-3 mb-3">
                  <Badge variant="info">{poll.category || 'Général'}</Badge>
                  {poll.is_socios_only && (
                    <Badge variant="gold">⭐ Socios uniquement</Badge>
                  )}
                  {poll.status === 'active' && (
                    <Badge variant="success">Actif</Badge>
                  )}
                </div>

                <h2 className="text-2xl font-bold mb-2">{poll.question}</h2>

                {poll.description && (
                  <p className="text-gray-600">{poll.description}</p>
                )}

                <div className="flex items-center gap-4 mt-3 text-sm text-gray-600">
                  <span>👥 {poll.total_votes || 0} votes</span>
                  {poll.ends_at && (
                    <span>
                      ⏱️ Se termine le{' '}
                      {new Date(poll.ends_at).toLocaleDateString('fr-FR')}
                    </span>
                  )}
                </div>
              </div>

              {/* Poll Options */}
              <div className="space-y-3">
                {poll.options?.map((option: any) => {
                  const percentage = calculatePercentage(
                    option.votes_count || 0,
                    poll.total_votes || 0
                  )
                  const hasVoted = poll.user_has_voted
                  const userVotedThis = poll.user_vote_option_id === option.id

                  return (
                    <div key={option.id} className="relative">
                      {!hasVoted ? (
                        // Before voting - clickable button
                        <button
                          onClick={() => handleVote(poll.id, option.id)}
                          className="w-full text-left p-4 border-2 border-gray-300 rounded-lg hover:border-black transition"
                        >
                          <div className="flex items-center justify-between">
                            <span className="font-semibold">{option.text}</span>
                            <Badge variant="default" size="sm">
                              Voter
                            </Badge>
                          </div>
                        </button>
                      ) : (
                        // After voting - show results
                        <div className="p-4 border-2 border-gray-200 rounded-lg relative overflow-hidden">
                          {/* Background bar */}
                          <div
                            className="absolute inset-0 bg-css-gold/10"
                            style={{ width: `${percentage}%` }}
                          />

                          {/* Content */}
                          <div className="relative flex items-center justify-between">
                            <div className="flex items-center gap-3">
                              <span className="font-semibold">{option.text}</span>
                              {userVotedThis && (
                                <Badge variant="success" size="sm">
                                  ✓ Votre vote
                                </Badge>
                              )}
                            </div>
                            <div className="flex items-center gap-3">
                              <span className="text-sm text-gray-600">
                                {option.votes_count || 0} votes
                              </span>
                              <span className="font-bold text-css-gold">
                                {percentage}%
                              </span>
                            </div>
                          </div>
                        </div>
                      )}
                    </div>
                  )
                })}
              </div>

              {/* Poll Footer */}
              {poll.user_has_voted && (
                <div className="mt-6 pt-6 border-t">
                  <p className="text-sm text-gray-600 text-center">
                    ✓ Vous avez déjà voté pour ce sondage
                  </p>
                </div>
              )}
            </Card>
          ))
        ) : (
          <Card variant="elevated">
            <div className="text-center py-12">
              <div className="text-6xl mb-4">📊</div>
              <h2 className="text-2xl font-bold mb-2">Aucun sondage actif</h2>
              <p className="text-gray-600">
                Revenez bientôt pour participer aux prochains sondages
              </p>
            </div>
          </Card>
        )}
      </div>
    </DashboardLayout>
  )
}

export default PollsPage
