import { useEffect, useState } from 'react'
import { DashboardLayout } from '../layouts/DashboardLayout'
import { Card, Badge, Button, Spinner, Modal, Input } from '../components'
import { api } from '../services/api'
import toast from 'react-hot-toast'

export const EventsPage = () => {
  const [events, setEvents] = useState([])
  const [isLoading, setIsLoading] = useState(true)
  const [selectedEvent, setSelectedEvent] = useState<any>(null)
  const [isModalOpen, setIsModalOpen] = useState(false)
  const [isRegistering, setIsRegistering] = useState(false)
  const [registrationData, setRegistrationData] = useState({
    guests: 1,
    notes: ''
  })

  useEffect(() => {
    fetchEvents()
  }, [])

  const fetchEvents = async () => {
    try {
      const response = await api.getEvents({ upcoming: true })
      setEvents(response.data || [])
    } catch (error) {
      console.error('Error fetching events:', error)
    } finally {
      setIsLoading(false)
    }
  }

  const handleRegister = async () => {
    if (!selectedEvent) return

    setIsRegistering(true)
    try {
      await api.registerForEvent(selectedEvent.id, registrationData)
      toast.success('Inscription réussie!')
      setIsModalOpen(false)
      setRegistrationData({ guests: 1, notes: '' })
      fetchEvents()
    } catch (error: any) {
      toast.error(error.response?.data?.message || 'Erreur lors de l\'inscription')
    } finally {
      setIsRegistering(false)
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
        <h1 className="text-4xl font-bold text-black mb-2">Événements 📅</h1>
        <p className="text-gray-600">
          Découvrez et participez aux événements organisés par le CSS
        </p>
      </div>

      <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        {events.length > 0 ? (
          events.map((event: any) => (
            <Card key={event.id} variant="elevated" hover padding="none">
              {/* Image */}
              <div className="relative">
                <img
                  src={event.image || '/placeholder-event.jpg'}
                  alt={event.title}
                  className="w-full h-56 object-cover rounded-t-xl"
                />
                <div className="absolute top-2 left-2">
                  <Badge variant={event.is_vip ? 'gold' : 'info'}>
                    {event.type || 'Événement'}
                  </Badge>
                </div>
                {event.is_full && (
                  <div className="absolute top-2 right-2">
                    <Badge variant="danger">Complet</Badge>
                  </div>
                )}
              </div>

              <div className="p-6">
                {/* Title */}
                <h3 className="font-bold text-xl mb-2">{event.title}</h3>

                {/* Description */}
                <p className="text-sm text-gray-600 mb-4 line-clamp-3">
                  {event.description}
                </p>

                {/* Date & Time */}
                <div className="space-y-2 mb-4">
                  <div className="flex items-center gap-2 text-sm">
                    <span className="text-lg">📅</span>
                    <span>
                      {new Date(event.date).toLocaleDateString('fr-FR', {
                        weekday: 'long',
                        day: 'numeric',
                        month: 'long',
                        year: 'numeric'
                      })}
                    </span>
                  </div>
                  <div className="flex items-center gap-2 text-sm">
                    <span className="text-lg">🕐</span>
                    <span>{event.time}</span>
                  </div>
                  <div className="flex items-center gap-2 text-sm">
                    <span className="text-lg">📍</span>
                    <span>{event.location}</span>
                  </div>
                </div>

                {/* Capacity */}
                {event.max_attendees && (
                  <div className="mb-4">
                    <div className="flex justify-between text-sm mb-2">
                      <span className="text-gray-600">Places disponibles</span>
                      <span className="font-semibold">
                        {event.max_attendees - (event.registered_count || 0)} /{' '}
                        {event.max_attendees}
                      </span>
                    </div>
                    <div className="w-full bg-gray-200 rounded-full h-2">
                      <div
                        className="bg-css-gold h-2 rounded-full"
                        style={{
                          width: `${
                            ((event.registered_count || 0) / event.max_attendees) * 100
                          }%`
                        }}
                      />
                    </div>
                  </div>
                )}

                {/* Price */}
                {event.price > 0 ? (
                  <div className="mb-4">
                    <span className="text-2xl font-bold text-css-gold">
                      {event.price} TND
                    </span>
                  </div>
                ) : (
                  <Badge variant="success" className="mb-4">
                    Gratuit
                  </Badge>
                )}

                {/* Register Button */}
                <Button
                  variant="primary"
                  className="w-full"
                  disabled={event.is_full || event.user_registered}
                  onClick={() => {
                    setSelectedEvent(event)
                    setIsModalOpen(true)
                  }}
                >
                  {event.user_registered
                    ? '✓ Inscrit'
                    : event.is_full
                    ? 'Complet'
                    : 'S\'inscrire'}
                </Button>
              </div>
            </Card>
          ))
        ) : (
          <div className="col-span-full">
            <Card variant="elevated">
              <div className="text-center py-12">
                <div className="text-6xl mb-4">📅</div>
                <h2 className="text-2xl font-bold mb-2">Aucun événement à venir</h2>
                <p className="text-gray-600">
                  Revenez bientôt pour découvrir nos prochains événements
                </p>
              </div>
            </Card>
          </div>
        )}
      </div>

      {/* Registration Modal */}
      <Modal
        isOpen={isModalOpen}
        onClose={() => setIsModalOpen(false)}
        title="Inscription à l'événement"
        size="md"
      >
        {selectedEvent && (
          <div>
            <div className="mb-6">
              <h3 className="font-bold text-xl mb-2">{selectedEvent.title}</h3>
              <div className="text-sm text-gray-600 space-y-1">
                <div>📅 {new Date(selectedEvent.date).toLocaleDateString('fr-FR')}</div>
                <div>🕐 {selectedEvent.time}</div>
                <div>📍 {selectedEvent.location}</div>
              </div>
            </div>

            <div className="space-y-4">
              <Input
                label="Nombre de participants"
                type="number"
                min="1"
                max={selectedEvent.max_guests || 4}
                value={registrationData.guests}
                onChange={(e) =>
                  setRegistrationData({
                    ...registrationData,
                    guests: parseInt(e.target.value)
                  })
                }
                helperText={`Maximum ${selectedEvent.max_guests || 4} personnes`}
              />

              <div>
                <label className="block text-sm font-semibold text-gray-700 mb-2">
                  Notes (optionnel)
                </label>
                <textarea
                  value={registrationData.notes}
                  onChange={(e) =>
                    setRegistrationData({
                      ...registrationData,
                      notes: e.target.value
                    })
                  }
                  className="w-full px-4 py-2.5 border-2 border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-black focus:border-black"
                  rows={3}
                  placeholder="Informations complémentaires..."
                />
              </div>
            </div>

            {selectedEvent.price > 0 && (
              <div className="mt-4 p-4 bg-css-gold/10 rounded-lg border border-css-gold/30">
                <div className="flex justify-between items-center">
                  <span className="font-semibold">Total à payer:</span>
                  <span className="text-2xl font-bold text-css-gold">
                    {selectedEvent.price * registrationData.guests} TND
                  </span>
                </div>
              </div>
            )}

            <div className="flex gap-3 mt-6">
              <Button
                variant="primary"
                onClick={handleRegister}
                isLoading={isRegistering}
                disabled={isRegistering}
                className="flex-1"
              >
                Confirmer l'inscription
              </Button>
              <Button
                variant="outline"
                onClick={() => setIsModalOpen(false)}
                disabled={isRegistering}
              >
                Annuler
              </Button>
            </div>
          </div>
        )}
      </Modal>
    </DashboardLayout>
  )
}

export default EventsPage
