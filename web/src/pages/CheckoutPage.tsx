import { useState } from 'react'
import { useNavigate } from 'react-router-dom'
import { DashboardLayout } from '../layouts/DashboardLayout'
import { Card, Input, Button, Badge } from '../components'
import { useCartStore } from '../store/cartStore'
import { api } from '../services/api'
import toast from 'react-hot-toast'

export const CheckoutPage = () => {
  const navigate = useNavigate()
  const { items, total, clearCart } = useCartStore()
  const [isProcessing, setIsProcessing] = useState(false)
  const [formData, setFormData] = useState({
    firstName: '',
    lastName: '',
    email: '',
    phone: '',
    address: '',
    city: '',
    postalCode: '',
    paymentMethod: 'd17'
  })

  const paymentMethods = [
    { id: 'd17', name: 'D17', icon: '💳' },
    { id: 'konnect', name: 'Konnect', icon: '📱' },
    { id: 'paymee', name: 'Paymee', icon: '💰' },
    { id: 'sadad', name: 'Sadad', icon: '🏦' }
  ]

  const handleInputChange = (e: React.ChangeEvent<HTMLInputElement>) => {
    setFormData({
      ...formData,
      [e.target.name]: e.target.value
    })
  }

  const handleSubmit = async (e: React.FormEvent) => {
    e.preventDefault()
    setIsProcessing(true)

    try {
      const orderData = {
        payment_method: formData.paymentMethod,
        shipping_address: {
          first_name: formData.firstName,
          last_name: formData.lastName,
          email: formData.email,
          phone: formData.phone,
          address: formData.address,
          city: formData.city,
          postal_code: formData.postalCode
        }
      }

      const response = await api.createOrder(orderData)
      await clearCart()

      toast.success('Commande passée avec succès!')
      navigate(`/orders/${response.data.id}`)
    } catch (error: any) {
      toast.error(error.response?.data?.message || 'Erreur lors de la commande')
    } finally {
      setIsProcessing(false)
    }
  }

  if (items.length === 0) {
    navigate('/cart')
    return null
  }

  return (
    <DashboardLayout>
      <div className="max-w-6xl mx-auto">
        <h1 className="text-4xl font-bold text-black mb-8">
          Finaliser la commande
        </h1>

        <form onSubmit={handleSubmit}>
          <div className="grid grid-cols-1 lg:grid-cols-3 gap-8">
            {/* Left Column - Forms */}
            <div className="lg:col-span-2 space-y-6">
              {/* Shipping Information */}
              <Card variant="elevated">
                <h2 className="text-2xl font-bold mb-6">📦 Informations de livraison</h2>

                <div className="grid grid-cols-2 gap-4">
                  <Input
                    label="Prénom"
                    name="firstName"
                    value={formData.firstName}
                    onChange={handleInputChange}
                    required
                  />
                  <Input
                    label="Nom"
                    name="lastName"
                    value={formData.lastName}
                    onChange={handleInputChange}
                    required
                  />
                  <Input
                    label="Email"
                    type="email"
                    name="email"
                    value={formData.email}
                    onChange={handleInputChange}
                    required
                  />
                  <Input
                    label="Téléphone"
                    type="tel"
                    name="phone"
                    value={formData.phone}
                    onChange={handleInputChange}
                    required
                  />
                  <div className="col-span-2">
                    <Input
                      label="Adresse complète"
                      name="address"
                      value={formData.address}
                      onChange={handleInputChange}
                      required
                    />
                  </div>
                  <Input
                    label="Ville"
                    name="city"
                    value={formData.city}
                    onChange={handleInputChange}
                    required
                  />
                  <Input
                    label="Code postal"
                    name="postalCode"
                    value={formData.postalCode}
                    onChange={handleInputChange}
                    required
                  />
                </div>
              </Card>

              {/* Payment Method */}
              <Card variant="elevated">
                <h2 className="text-2xl font-bold mb-6">💳 Méthode de paiement</h2>

                <div className="grid grid-cols-2 gap-4">
                  {paymentMethods.map((method) => (
                    <label
                      key={method.id}
                      className={`cursor-pointer p-4 border-2 rounded-lg transition ${
                        formData.paymentMethod === method.id
                          ? 'border-black bg-gray-50'
                          : 'border-gray-300 hover:border-gray-400'
                      }`}
                    >
                      <input
                        type="radio"
                        name="paymentMethod"
                        value={method.id}
                        checked={formData.paymentMethod === method.id}
                        onChange={handleInputChange}
                        className="sr-only"
                      />
                      <div className="flex items-center justify-between">
                        <div className="flex items-center gap-3">
                          <span className="text-2xl">{method.icon}</span>
                          <span className="font-semibold">{method.name}</span>
                        </div>
                        {formData.paymentMethod === method.id && (
                          <Badge variant="success">✓</Badge>
                        )}
                      </div>
                    </label>
                  ))}
                </div>
              </Card>
            </div>

            {/* Right Column - Order Summary */}
            <div>
              <Card variant="elevated" className="sticky top-24">
                <h2 className="text-2xl font-bold mb-6">Résumé</h2>

                {/* Order Items */}
                <div className="space-y-3 mb-6 max-h-64 overflow-y-auto">
                  {items.map((item) => (
                    <div key={item.id} className="flex gap-3">
                      <img
                        src={item.product_image || '/placeholder-product.jpg'}
                        alt={item.product_name}
                        className="w-16 h-16 object-cover rounded"
                      />
                      <div className="flex-1">
                        <div className="font-semibold text-sm line-clamp-1">
                          {item.product_name}
                        </div>
                        <div className="text-sm text-gray-600">
                          Qté: {item.quantity}
                        </div>
                      </div>
                      <div className="font-semibold">
                        {(item.price * item.quantity).toFixed(2)} TND
                      </div>
                    </div>
                  ))}
                </div>

                {/* Totals */}
                <div className="space-y-3 mb-6 pt-6 border-t">
                  <div className="flex justify-between">
                    <span className="text-gray-600">Sous-total</span>
                    <span className="font-semibold">{total.toFixed(2)} TND</span>
                  </div>
                  <div className="flex justify-between">
                    <span className="text-gray-600">Livraison</span>
                    <span className="font-semibold text-green-600">Gratuite</span>
                  </div>
                  <div className="border-t pt-3">
                    <div className="flex justify-between text-xl">
                      <span className="font-bold">Total</span>
                      <span className="font-bold text-css-gold">
                        {total.toFixed(2)} TND
                      </span>
                    </div>
                  </div>
                </div>

                {/* Submit Button */}
                <Button
                  type="submit"
                  variant="primary"
                  size="lg"
                  className="w-full"
                  isLoading={isProcessing}
                  disabled={isProcessing}
                >
                  {isProcessing ? 'Traitement...' : 'Confirmer la commande'}
                </Button>

                {/* Security Notice */}
                <div className="mt-6 pt-6 border-t text-center">
                  <div className="flex items-center justify-center gap-2 text-sm text-gray-600">
                    <span className="text-lg">🔒</span>
                    <span>Paiement 100% sécurisé</span>
                  </div>
                </div>
              </Card>
            </div>
          </div>
        </form>
      </div>
    </DashboardLayout>
  )
}

export default CheckoutPage
