import { useEffect, useState } from 'react'
import { DashboardLayout } from '../layouts/DashboardLayout'
import { Card, Badge, Button, Input, Spinner, Modal } from '../components'
import { api } from '../services/api'
import toast from 'react-hot-toast'

export const AuctionsPage = () => {
  const [auctions, setAuctions] = useState([])
  const [isLoading, setIsLoading] = useState(true)
  const [selectedAuction, setSelectedAuction] = useState<any>(null)
  const [bidAmount, setBidAmount] = useState('')
  const [isModalOpen, setIsModalOpen] = useState(false)
  const [isBidding, setIsBidding] = useState(false)

  useEffect(() => {
    fetchAuctions()
  }, [])

  const fetchAuctions = async () => {
    try {
      const response = await api.getAuctions({ status: 'active' })
      setAuctions(response.data || [])
    } catch (error) {
      console.error('Error fetching auctions:', error)
    } finally {
      setIsLoading(false)
    }
  }

  const handlePlaceBid = async () => {
    if (!selectedAuction || !bidAmount) return

    const amount = parseFloat(bidAmount)
    if (amount <= selectedAuction.current_bid) {
      toast.error('Votre enchère doit être supérieure à l\'enchère actuelle')
      return
    }

    setIsBidding(true)
    try {
      await api.placeBid(selectedAuction.id, amount)
      toast.success('Enchère placée avec succès!')
      setIsModalOpen(false)
      setBidAmount('')
      fetchAuctions()
    } catch (error: any) {
      toast.error(error.response?.data?.message || 'Erreur lors de l\'enchère')
    } finally {
      setIsBidding(false)
    }
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
        <h1 className="text-4xl font-bold text-black mb-2">Enchères 🔨</h1>
        <p className="text-gray-600">
          Participez aux enchères pour gagner des objets exclusifs CSS
        </p>
      </div>

      <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        {auctions.length > 0 ? (
          auctions.map((auction: any) => (
            <Card key={auction.id} variant="elevated" hover padding="none">
              {/* Image */}
              <div className="relative">
                <img
                  src={auction.image || '/placeholder-auction.jpg'}
                  alt={auction.title}
                  className="w-full h-64 object-cover rounded-t-xl"
                />
                <div className="absolute top-2 right-2">
                  <Badge variant="danger">
                    🔥 {auction.bids_count || 0} enchères
                  </Badge>
                </div>
              </div>

              <div className="p-6">
                {/* Title */}
                <h3 className="font-bold text-xl mb-2">{auction.title}</h3>

                {/* Description */}
                <p className="text-sm text-gray-600 mb-4 line-clamp-2">
                  {auction.description}
                </p>

                {/* Current Bid */}
                <div className="mb-4">
                  <div className="text-sm text-gray-600 mb-1">Enchère actuelle</div>
                  <div className="text-3xl font-bold text-css-gold">
                    {auction.current_bid || auction.starting_bid} TND
                  </div>
                  {auction.current_bidder && (
                    <div className="text-xs text-gray-600 mt-1">
                      par {auction.current_bidder}
                    </div>
                  )}
                </div>

                {/* Time Remaining */}
                <div className="mb-4 p-3 bg-red-50 border border-red-200 rounded-lg">
                  <div className="flex items-center justify-between">
                    <span className="text-sm font-semibold text-red-800">
                      ⏱️ Temps restant:
                    </span>
                    <span className="font-bold text-red-600">
                      {auction.time_remaining || '2h 15m'}
                    </span>
                  </div>
                </div>

                {/* Stats */}
                <div className="grid grid-cols-2 gap-3 mb-4 text-sm">
                  <div>
                    <span className="text-gray-600">Départ:</span>
                    <div className="font-semibold">{auction.starting_bid} TND</div>
                  </div>
                  <div>
                    <span className="text-gray-600">Incrément min:</span>
                    <div className="font-semibold">{auction.min_increment || 5} TND</div>
                  </div>
                </div>

                {/* Action Button */}
                <Button
                  variant="primary"
                  className="w-full"
                  onClick={() => {
                    setSelectedAuction(auction)
                    setIsModalOpen(true)
                  }}
                >
                  Enchérir maintenant
                </Button>
              </div>
            </Card>
          ))
        ) : (
          <div className="col-span-full">
            <Card variant="elevated">
              <div className="text-center py-12">
                <div className="text-6xl mb-4">🔨</div>
                <h2 className="text-2xl font-bold mb-2">
                  Aucune enchère active
                </h2>
                <p className="text-gray-600">
                  Revenez bientôt pour découvrir de nouvelles enchères
                </p>
              </div>
            </Card>
          </div>
        )}
      </div>

      {/* Bid Modal */}
      <Modal
        isOpen={isModalOpen}
        onClose={() => setIsModalOpen(false)}
        title="Placer une enchère"
        size="md"
      >
        {selectedAuction && (
          <div>
            <div className="mb-6">
              <h3 className="font-bold text-xl mb-2">{selectedAuction.title}</h3>
              <div className="flex items-baseline gap-2 mb-4">
                <span className="text-sm text-gray-600">Enchère actuelle:</span>
                <span className="text-2xl font-bold text-css-gold">
                  {selectedAuction.current_bid || selectedAuction.starting_bid} TND
                </span>
              </div>
              <p className="text-sm text-gray-600">
                Incrément minimum: {selectedAuction.min_increment || 5} TND
              </p>
            </div>

            <Input
              label="Votre enchère (TND)"
              type="number"
              value={bidAmount}
              onChange={(e) => setBidAmount(e.target.value)}
              placeholder={`Minimum ${(selectedAuction.current_bid || selectedAuction.starting_bid) + (selectedAuction.min_increment || 5)} TND`}
              helperText="Entrez un montant supérieur à l'enchère actuelle"
            />

            <div className="flex gap-3 mt-6">
              <Button
                variant="primary"
                onClick={handlePlaceBid}
                isLoading={isBidding}
                disabled={!bidAmount || isBidding}
                className="flex-1"
              >
                Confirmer l'enchère
              </Button>
              <Button
                variant="outline"
                onClick={() => setIsModalOpen(false)}
                disabled={isBidding}
              >
                Annuler
              </Button>
            </div>

            <div className="mt-4 p-3 bg-yellow-50 border border-yellow-200 rounded-lg">
              <p className="text-xs text-yellow-800">
                ⚠️ En plaçant cette enchère, vous vous engagez à acheter l'article
                si vous remportez l'enchère.
              </p>
            </div>
          </div>
        )}
      </Modal>
    </DashboardLayout>
  )
}

export default AuctionsPage
