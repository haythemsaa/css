import { useEffect, useState } from 'react'
import { DashboardLayout } from '../layouts/DashboardLayout'
import { Card, Badge, Button, Input, Spinner, Modal } from '../components'
import { api } from '../services/api'
import toast from 'react-hot-toast'

export const CampaignsPage = () => {
  const [campaigns, setCampaigns] = useState([])
  const [donationGoals, setDonationGoals] = useState([])
  const [isLoading, setIsLoading] = useState(true)
  const [selectedCampaign, setSelectedCampaign] = useState<any>(null)
  const [isModalOpen, setIsModalOpen] = useState(false)
  const [isDonating, setIsDonating] = useState(false)
  const [donationData, setDonationData] = useState({
    amount: '',
    isAnonymous: false,
    message: '',
    paymentMethod: 'd17'
  })

  useEffect(() => {
    fetchData()
  }, [])

  const fetchData = async () => {
    try {
      const [campaignsRes, goalsRes] = await Promise.all([
        api.getCampaigns({ status: 'active' }),
        api.getDonationGoals({ status: 'active' })
      ])
      setCampaigns(campaignsRes.data || [])
      setDonationGoals(goalsRes.data || [])
    } catch (error) {
      console.error('Error fetching campaigns:', error)
    } finally {
      setIsLoading(false)
    }
  }

  const handleDonate = async () => {
    if (!selectedCampaign || !donationData.amount) return

    const amount = parseFloat(donationData.amount)
    if (amount < 5) {
      toast.error('Le montant minimum est de 5 TND')
      return
    }

    setIsDonating(true)
    try {
      if (selectedCampaign.type === 'goal') {
        await api.donateToDonationGoal(
          selectedCampaign.id,
          amount,
          donationData.paymentMethod
        )
      } else {
        await api.makeDonation({
          amount,
          campaign_id: selectedCampaign.id,
          payment_method: donationData.paymentMethod,
          is_anonymous: donationData.isAnonymous,
          message: donationData.message
        })
      }

      toast.success('Merci pour votre don!')
      setIsModalOpen(false)
      setDonationData({ amount: '', isAnonymous: false, message: '', paymentMethod: 'd17' })
      fetchData()
    } catch (error: any) {
      toast.error(error.response?.data?.message || 'Erreur lors du don')
    } finally {
      setIsDonating(false)
    }
  }

  if (isLoading) {
    return (
      <DashboardLayout>
        <Spinner size="xl" className="h-screen" />
      </DashboardLayout>
    )
  }

  const allItems = [
    ...campaigns.map((c: any) => ({ ...c, type: 'campaign' })),
    ...donationGoals.map((g: any) => ({ ...g, type: 'goal' }))
  ]

  return (
    <DashboardLayout>
      <div className="mb-8">
        <h1 className="text-4xl font-bold text-black mb-2">Soutenez le CSS 💙</h1>
        <p className="text-gray-600">
          Contribuez au développement et aux projets du club
        </p>
      </div>

      <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        {allItems.length > 0 ? (
          allItems.map((item: any) => {
            const progress =item.goal_amount
              ? (item.current_amount / item.goal_amount) * 100
              : 0

            return (
              <Card key={`${item.type}-${item.id}`} variant="elevated" hover>
                {/* Header */}
                <div className="mb-4">
                  <div className="flex items-center gap-2 mb-3">
                    <Badge variant={item.type === 'goal' ? 'gold' : 'info'}>
                      {item.type === 'goal' ? '🎯 Objectif' : '📢 Campagne'}
                    </Badge>
                    {item.is_urgent && (
                      <Badge variant="danger">⚡ Urgent</Badge>
                    )}
                  </div>

                  <h3 className="font-bold text-xl mb-2">
                    {item.title || item.name}
                  </h3>

                  <p className="text-sm text-gray-600 line-clamp-3">
                    {item.description}
                  </p>
                </div>

                {/* Progress */}
                {item.goal_amount && (
                  <div className="mb-4">
                    <div className="flex justify-between text-sm mb-2">
                      <span className="font-semibold text-css-gold">
                        {item.current_amount?.toLocaleString() || 0} TND
                      </span>
                      <span className="text-gray-600">
                        sur {item.goal_amount?.toLocaleString()} TND
                      </span>
                    </div>
                    <div className="w-full bg-gray-200 rounded-full h-3">
                      <div
                        className="bg-css-gold h-3 rounded-full transition-all"
                        style={{ width: `${Math.min(progress, 100)}%` }}
                      />
                    </div>
                    <div className="text-center mt-2">
                      <span className="text-sm font-semibold text-css-gold">
                        {progress.toFixed(1)}% atteint
                      </span>
                    </div>
                  </div>
                )}

                {/* Stats */}
                <div className="grid grid-cols-2 gap-3 mb-4 text-sm">
                  <div className="text-center p-2 bg-gray-50 rounded">
                    <div className="font-bold text-lg">{item.donors_count || 0}</div>
                    <div className="text-gray-600 text-xs">Donateurs</div>
                  </div>
                  <div className="text-center p-2 bg-gray-50 rounded">
                    <div className="font-bold text-lg">
                      {item.days_remaining || '30'}j
                    </div>
                    <div className="text-gray-600 text-xs">Restants</div>
                  </div>
                </div>

                {/* Donate Button */}
                <Button
                  variant="primary"
                  className="w-full"
                  onClick={() => {
                    setSelectedCampaign(item)
                    setIsModalOpen(true)
                  }}
                >
                  💙 Faire un don
                </Button>
              </Card>
            )
          })
        ) : (
          <div className="col-span-full">
            <Card variant="elevated">
              <div className="text-center py-12">
                <div className="text-6xl mb-4">💙</div>
                <h2 className="text-2xl font-bold mb-2">
                  Aucune campagne active
                </h2>
                <p className="text-gray-600">
                  Revenez bientôt pour soutenir le club
                </p>
              </div>
            </Card>
          </div>
        )}
      </div>

      {/* Donation Modal */}
      <Modal
        isOpen={isModalOpen}
        onClose={() => setIsModalOpen(false)}
        title="Faire un don"
        size="md"
      >
        {selectedCampaign && (
          <div>
            <div className="mb-6">
              <h3 className="font-bold text-xl mb-2">
                {selectedCampaign.title || selectedCampaign.name}
              </h3>
              {selectedCampaign.goal_amount && (
                <div className="text-sm text-gray-600">
                  {selectedCampaign.current_amount?.toLocaleString()} TND /{' '}
                  {selectedCampaign.goal_amount?.toLocaleString()} TND collectés
                </div>
              )}
            </div>

            <div className="space-y-4">
              <Input
                label="Montant du don (TND)"
                type="number"
                min="5"
                value={donationData.amount}
                onChange={(e) =>
                  setDonationData({ ...donationData, amount: e.target.value })
                }
                placeholder="Minimum 5 TND"
                helperText="Le montant minimum est de 5 TND"
              />

              {/* Quick amounts */}
              <div>
                <label className="block text-sm font-semibold text-gray-700 mb-2">
                  Montants suggérés
                </label>
                <div className="grid grid-cols-4 gap-2">
                  {[10, 20, 50, 100].map((amount) => (
                    <Button
                      key={amount}
                      variant="outline"
                      size="sm"
                      onClick={() =>
                        setDonationData({ ...donationData, amount: amount.toString() })
                      }
                    >
                      {amount} TND
                    </Button>
                  ))}
                </div>
              </div>

              {/* Payment method */}
              <div>
                <label className="block text-sm font-semibold text-gray-700 mb-2">
                  Méthode de paiement
                </label>
                <select
                  value={donationData.paymentMethod}
                  onChange={(e) =>
                    setDonationData({
                      ...donationData,
                      paymentMethod: e.target.value
                    })
                  }
                  className="w-full px-4 py-2.5 border-2 border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-black"
                >
                  <option value="d17">D17</option>
                  <option value="konnect">Konnect</option>
                  <option value="paymee">Paymee</option>
                  <option value="sadad">Sadad</option>
                </select>
              </div>

              {/* Message */}
              <div>
                <label className="block text-sm font-semibold text-gray-700 mb-2">
                  Message (optionnel)
                </label>
                <textarea
                  value={donationData.message}
                  onChange={(e) =>
                    setDonationData({
                      ...donationData,
                      message: e.target.value
                    })
                  }
                  className="w-full px-4 py-2.5 border-2 border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-black"
                  rows={3}
                  placeholder="Ajoutez un message de soutien..."
                />
              </div>

              {/* Anonymous option */}
              <label className="flex items-center gap-2 cursor-pointer">
                <input
                  type="checkbox"
                  checked={donationData.isAnonymous}
                  onChange={(e) =>
                    setDonationData({
                      ...donationData,
                      isAnonymous: e.target.checked
                    })
                  }
                  className="w-5 h-5"
                />
                <span className="text-sm">Faire un don anonyme</span>
              </label>
            </div>

            <div className="flex gap-3 mt-6">
              <Button
                variant="primary"
                onClick={handleDonate}
                isLoading={isDonating}
                disabled={!donationData.amount || isDonating}
                className="flex-1"
              >
                Confirmer le don
              </Button>
              <Button
                variant="outline"
                onClick={() => setIsModalOpen(false)}
                disabled={isDonating}
              >
                Annuler
              </Button>
            </div>

            <div className="mt-4 p-3 bg-green-50 border border-green-200 rounded-lg">
              <p className="text-xs text-green-800">
                💙 Merci de votre soutien! Votre don aidera le CSS à réaliser ses
                projets.
              </p>
            </div>
          </div>
        )}
      </Modal>
    </DashboardLayout>
  )
}

export default CampaignsPage
