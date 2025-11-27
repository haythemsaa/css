import { useEffect, useState } from 'react'
import { DashboardLayout } from '../layouts/DashboardLayout'
import { Card, Badge, Button, Spinner, Modal } from '../components'
import { api } from '../services/api'
import QRCode from 'qrcode.react'
import toast from 'react-hot-toast'

export const FreeuiPage = () => {
  const [partners, setPartners] = useState([])
  const [isLoading, setIsLoading] = useState(true)
  const [selectedOffer, setSelectedOffer] = useState<any>(null)
  const [qrCode, setQrCode] = useState<any>(null)
  const [isModalOpen, setIsModalOpen] = useState(false)

  useEffect(() => {
    const fetchPartners = async () => {
      try {
        const response = await api.getPartners()
        setPartners(response.data || [])
      } catch (error) {
        console.error('Error fetching partners:', error)
      } finally {
        setIsLoading(false)
      }
    }

    fetchPartners()
  }, [])

  const handleGenerateQR = async (partnerId: number, offerId?: number) => {
    try {
      const response = await api.generateReductionCode(partnerId, offerId)
      setQrCode(response)
      setIsModalOpen(true)
      toast.success('QR Code généré avec succès!')
    } catch (error: any) {
      toast.error(error.response?.data?.message || 'Erreur lors de la génération du QR Code')
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
        <h1 className="text-4xl font-bold text-black mb-2">Freeoui 🎁</h1>
        <p className="text-gray-600">
          Profitez de réductions exclusives chez nos partenaires
        </p>
      </div>

      {/* Partners Grid */}
      <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        {partners.length > 0 ? (
          partners.map((partner: any) => (
            <Card key={partner.id} variant="elevated" hover>
              {/* Partner Header */}
              <div className="flex items-start justify-between mb-4">
                <div className="flex-1">
                  <h3 className="font-bold text-xl mb-1">{partner.name}</h3>
                  <Badge variant="info" size="sm">
                    {partner.category}
                  </Badge>
                </div>
                {partner.logo && (
                  <img
                    src={partner.logo}
                    alt={partner.name}
                    className="w-16 h-16 object-contain rounded-lg"
                  />
                )}
              </div>

              {/* Description */}
              <p className="text-sm text-gray-600 mb-4 line-clamp-2">
                {partner.description}
              </p>

              {/* Offers */}
              {partner.active_offers?.length > 0 && (
                <div className="mb-4">
                  <div className="text-sm font-semibold mb-2">
                    Offres disponibles:
                  </div>
                  <div className="space-y-2">
                    {partner.active_offers.map((offer: any) => (
                      <div
                        key={offer.id}
                        className="p-3 bg-css-gold/10 rounded-lg border border-css-gold/20"
                      >
                        <div className="flex items-center justify-between">
                          <div className="flex-1">
                            <div className="font-semibold text-sm mb-1">
                              {offer.title}
                            </div>
                            <div className="text-xs text-gray-600">
                              {offer.description}
                            </div>
                          </div>
                          <Badge variant="gold" size="lg">
                            -{offer.discount_percentage}%
                          </Badge>
                        </div>

                        <Button
                          variant="secondary"
                          size="sm"
                          className="w-full mt-3"
                          onClick={() => handleGenerateQR(partner.id, offer.id)}
                        >
                          Générer QR Code
                        </Button>
                      </div>
                    ))}
                  </div>
                </div>
              )}

              {/* Partner Info */}
              <div className="border-t pt-4 space-y-2">
                {partner.address && (
                  <div className="text-sm text-gray-600">
                    📍 {partner.address}
                  </div>
                )}
                {partner.phone && (
                  <div className="text-sm text-gray-600">
                    📞 {partner.phone}
                  </div>
                )}
                {partner.distance && (
                  <div className="text-sm text-css-gold font-semibold">
                    📏 À {partner.distance} km de vous
                  </div>
                )}
              </div>

              {/* Default Discount */}
              {!partner.active_offers?.length && partner.default_discount && (
                <Button
                  variant="primary"
                  size="sm"
                  className="w-full mt-4"
                  onClick={() => handleGenerateQR(partner.id)}
                >
                  Obtenir -{partner.default_discount}% de réduction
                </Button>
              )}
            </Card>
          ))
        ) : (
          <div className="col-span-full">
            <Card>
              <p className="text-center text-gray-500 py-8">
                Aucun partenaire disponible
              </p>
            </Card>
          </div>
        )}
      </div>

      {/* QR Code Modal */}
      <Modal
        isOpen={isModalOpen}
        onClose={() => setIsModalOpen(false)}
        title="Votre QR Code de Réduction"
        size="md"
      >
        {qrCode && (
          <div className="text-center">
            <div className="bg-white p-8 rounded-xl inline-block mb-4">
              <QRCode value={qrCode.code} size={256} />
            </div>

            <div className="mb-6">
              <div className="text-2xl font-bold mb-2">
                Code: {qrCode.code}
              </div>
              <Badge variant="gold" size="lg">
                -{qrCode.discount_percentage}% de réduction
              </Badge>
            </div>

            <div className="bg-yellow-50 border-2 border-yellow-400 rounded-lg p-4 mb-4">
              <div className="font-semibold mb-2">⏰ Temps restant:</div>
              <div className="text-3xl font-bold text-yellow-700">
                {qrCode.expires_in || '15:00'}
              </div>
            </div>

            <div className="text-sm text-gray-600 mb-4">
              <p className="mb-2">
                Présentez ce QR Code au partenaire pour bénéficier de votre réduction.
              </p>
              <p className="font-semibold text-orange-600">
                ⚠️ Ce code expire dans 15 minutes
              </p>
            </div>

            <Button
              variant="primary"
              className="w-full"
              onClick={() => setIsModalOpen(false)}
            >
              Fermer
            </Button>
          </div>
        )}
      </Modal>
    </DashboardLayout>
  )
}

export default FreeuiPage
