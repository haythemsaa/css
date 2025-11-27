import { useEffect } from 'react'
import { Link, useNavigate } from 'react-router-dom'
import { DashboardLayout } from '../layouts/DashboardLayout'
import { Card, Button, Spinner } from '../components'
import { useCartStore } from '../store/cartStore'
import toast from 'react-hot-toast'

export const CartPage = () => {
  const navigate = useNavigate()
  const { items, total, itemCount, isLoading, fetchCart, updateItem, removeItem } = useCartStore()

  useEffect(() => {
    fetchCart()
  }, [])

  const handleQuantityChange = async (itemId: number, newQuantity: number) => {
    if (newQuantity < 1) return
    try {
      await updateItem(itemId, newQuantity)
      toast.success('Quantité mise à jour')
    } catch (error) {
      toast.error('Erreur lors de la mise à jour')
    }
  }

  const handleRemove = async (itemId: number) => {
    try {
      await removeItem(itemId)
      toast.success('Produit retiré du panier')
    } catch (error) {
      toast.error('Erreur lors de la suppression')
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
      <div className="max-w-6xl mx-auto">
        <h1 className="text-4xl font-bold text-black mb-8">
          Mon Panier ({itemCount})
        </h1>

        {items.length === 0 ? (
          <Card variant="elevated">
            <div className="text-center py-12">
              <div className="text-6xl mb-4">🛒</div>
              <h2 className="text-2xl font-bold mb-2">Votre panier est vide</h2>
              <p className="text-gray-600 mb-6">
                Découvrez nos produits officiels CSS
              </p>
              <Link to="/shop">
                <Button variant="primary">
                  Continuer mes achats
                </Button>
              </Link>
            </div>
          </Card>
        ) : (
          <div className="grid grid-cols-1 lg:grid-cols-3 gap-8">
            {/* Cart Items */}
            <div className="lg:col-span-2 space-y-4">
              {items.map((item) => (
                <Card key={item.id} variant="bordered">
                  <div className="flex gap-6">
                    {/* Product Image */}
                    <img
                      src={item.product_image || '/placeholder-product.jpg'}
                      alt={item.product_name}
                      className="w-32 h-32 object-cover rounded-lg"
                    />

                    {/* Product Info */}
                    <div className="flex-1">
                      <h3 className="font-bold text-lg mb-1">
                        {item.product_name}
                      </h3>
                      {item.variant_name && (
                        <p className="text-sm text-gray-600 mb-2">
                          Variante: {item.variant_name}
                        </p>
                      )}
                      <p className="text-2xl font-bold text-css-gold mb-4">
                        {item.price} TND
                      </p>

                      {/* Quantity Controls */}
                      <div className="flex items-center gap-4">
                        <div className="flex items-center border-2 border-gray-300 rounded-lg">
                          <button
                            onClick={() => handleQuantityChange(item.id, item.quantity - 1)}
                            className="px-4 py-2 hover:bg-gray-100 transition"
                            disabled={item.quantity <= 1}
                          >
                            −
                          </button>
                          <span className="px-6 py-2 font-semibold">
                            {item.quantity}
                          </span>
                          <button
                            onClick={() => handleQuantityChange(item.id, item.quantity + 1)}
                            className="px-4 py-2 hover:bg-gray-100 transition"
                          >
                            +
                          </button>
                        </div>

                        <Button
                          variant="ghost"
                          size="sm"
                          onClick={() => handleRemove(item.id)}
                        >
                          🗑️ Supprimer
                        </Button>
                      </div>
                    </div>

                    {/* Item Total */}
                    <div className="text-right">
                      <p className="text-sm text-gray-600 mb-1">Total</p>
                      <p className="text-2xl font-bold">
                        {(item.price * item.quantity).toFixed(2)} TND
                      </p>
                    </div>
                  </div>
                </Card>
              ))}
            </div>

            {/* Order Summary */}
            <div>
              <Card variant="elevated" className="sticky top-24">
                <h2 className="text-2xl font-bold mb-6">Récapitulatif</h2>

                <div className="space-y-4 mb-6">
                  <div className="flex justify-between">
                    <span className="text-gray-600">Sous-total</span>
                    <span className="font-semibold">{total.toFixed(2)} TND</span>
                  </div>
                  <div className="flex justify-between">
                    <span className="text-gray-600">Livraison</span>
                    <span className="font-semibold">Gratuite</span>
                  </div>
                  <div className="border-t pt-4">
                    <div className="flex justify-between text-xl">
                      <span className="font-bold">Total</span>
                      <span className="font-bold text-css-gold">
                        {total.toFixed(2)} TND
                      </span>
                    </div>
                  </div>
                </div>

                <Button
                  variant="primary"
                  size="lg"
                  className="w-full mb-3"
                  onClick={() => navigate('/checkout')}
                >
                  Passer la commande
                </Button>

                <Link to="/shop">
                  <Button variant="outline" size="sm" className="w-full">
                    Continuer mes achats
                  </Button>
                </Link>

                {/* Trust Badges */}
                <div className="mt-6 pt-6 border-t space-y-3">
                  <div className="flex items-center gap-3 text-sm">
                    <span className="text-2xl">🔒</span>
                    <span>Paiement 100% sécurisé</span>
                  </div>
                  <div className="flex items-center gap-3 text-sm">
                    <span className="text-2xl">🚚</span>
                    <span>Livraison rapide sous 3-5 jours</span>
                  </div>
                  <div className="flex items-center gap-3 text-sm">
                    <span className="text-2xl">↩️</span>
                    <span>Retours gratuits sous 14 jours</span>
                  </div>
                </div>
              </Card>
            </div>
          </div>
        )}
      </div>
    </DashboardLayout>
  )
}

export default CartPage
