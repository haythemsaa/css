import { useState, useEffect } from 'react'
import { api } from '@/services/api'

interface Partner {
  id: number
  name: string
  category: { name: string; icon: string }
  city: string
  reduction_value_premium: number
  reduction_value_socios: number
  is_featured: boolean
}

const PartnersPage = () => {
  const [partners, setPartners] = useState<Partner[]>([])
  const [loading, setLoading] = useState(true)
  const [selectedCity, setSelectedCity] = useState<string>('')

  useEffect(() => {
    fetchPartners()
  }, [selectedCity])

  const fetchPartners = async () => {
    try {
      setLoading(true)
      const response = await api.getPartners({
        ...(selectedCity && { city: selectedCity }),
      })
      setPartners(response.data)
    } catch (error) {
      console.error('Error fetching partners:', error)
    } finally {
      setLoading(false)
    }
  }

  return (
    <div className="min-h-screen bg-gray-50">
      {/* Header */}
      <div className="bg-css-black text-white py-16">
        <div className="container mx-auto px-4">
          <h1 className="text-4xl font-bold mb-4">
            Partenaires Freeoui
          </h1>
          <p className="text-xl text-gray-300">
            50+ partenaires pour économiser au quotidien
          </p>
        </div>
      </div>

      {/* Filters */}
      <div className="bg-white shadow-sm py-4">
        <div className="container mx-auto px-4">
          <div className="flex items-center gap-4">
            <select
              value={selectedCity}
              onChange={(e) => setSelectedCity(e.target.value)}
              className="px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-css-gold"
            >
              <option value="">Toutes les villes</option>
              <option value="Sfax">Sfax</option>
              <option value="Tunis">Tunis</option>
              <option value="Sousse">Sousse</option>
            </select>

            <button className="px-4 py-2 bg-css-gold text-black font-semibold rounded-lg hover:bg-yellow-500 transition">
              <span className="mr-2">📍</span>
              Près de moi
            </button>
          </div>
        </div>
      </div>

      {/* Partners Grid */}
      <div className="container mx-auto px-4 py-12">
        {loading ? (
          <div className="text-center py-12">
            <div className="inline-block animate-spin rounded-full h-12 w-12 border-4 border-css-gold border-t-transparent"></div>
          </div>
        ) : (
          <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            {partners.map((partner) => (
              <div
                key={partner.id}
                className="bg-white rounded-xl shadow-md hover:shadow-xl transition overflow-hidden"
              >
                {partner.is_featured && (
                  <div className="bg-css-gold text-black px-4 py-1 text-sm font-semibold">
                    ⭐ Partenaire Featured
                  </div>
                )}
                <div className="p-6">
                  <div className="flex items-start justify-between mb-4">
                    <div>
                      <h3 className="text-xl font-bold mb-2">{partner.name}</h3>
                      <p className="text-gray-600 flex items-center gap-2">
                        <span>{partner.category.icon}</span>
                        {partner.category.name}
                      </p>
                    </div>
                  </div>

                  <div className="mb-4">
                    <p className="text-sm text-gray-600">
                      📍 {partner.city}
                    </p>
                  </div>

                  <div className="space-y-2 mb-4">
                    <div className="flex justify-between items-center">
                      <span className="text-sm text-gray-600">Premium:</span>
                      <span className="font-bold text-css-gold">
                        {partner.reduction_value_premium}% de réduction
                      </span>
                    </div>
                    <div className="flex justify-between items-center">
                      <span className="text-sm text-gray-600">Socios:</span>
                      <span className="font-bold text-css-gold">
                        {partner.reduction_value_socios}% de réduction
                      </span>
                    </div>
                  </div>

                  <button className="w-full bg-css-black text-css-gold py-3 rounded-lg font-semibold hover:bg-gray-900 transition">
                    Générer QR Code
                  </button>
                </div>
              </div>
            ))}
          </div>
        )}
      </div>
    </div>
  )
}

export default PartnersPage
