import { useEffect, useState } from 'react'
import { DashboardLayout } from '../layouts/DashboardLayout'
import { Card, Badge, Button, Spinner } from '../components'
import { api } from '../services/api'
import { useCartStore } from '../store/cartStore'
import toast from 'react-hot-toast'

export const ShopPage = () => {
  const [products, setProducts] = useState([])
  const [isLoading, setIsLoading] = useState(true)
  const { addItem } = useCartStore()

  useEffect(() => {
    const fetchProducts = async () => {
      try {
        const response = await api.getProducts()
        setProducts(response.data || [])
      } catch (error) {
        console.error('Error fetching products:', error)
      } finally {
        setIsLoading(false)
      }
    }

    fetchProducts()
  }, [])

  const handleAddToCart = async (productId: number) => {
    try {
      await addItem(productId, 1)
      toast.success('Produit ajouté au panier!')
    } catch (error) {
      toast.error('Erreur lors de l\'ajout au panier')
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
        <h1 className="text-4xl font-bold text-black mb-2">Boutique Officielle</h1>
        <p className="text-gray-600">
          Découvrez nos produits officiels CSS
        </p>
      </div>

      {/* Products Grid */}
      <div className="grid grid-cols-1 md:grid-cols-3 lg:grid-cols-4 gap-6">
        {products.length > 0 ? (
          products.map((product: any) => (
            <Card key={product.id} variant="elevated" hover padding="none">
              {/* Product Image */}
              <div className="relative">
                <img
                  src={product.image || '/placeholder-product.jpg'}
                  alt={product.name}
                  className="w-full h-64 object-cover rounded-t-xl"
                />
                {product.is_new && (
                  <div className="absolute top-2 left-2">
                    <Badge variant="danger" size="sm">
                      NOUVEAU
                    </Badge>
                  </div>
                )}
                {product.discount_percentage > 0 && (
                  <div className="absolute top-2 right-2">
                    <Badge variant="warning" size="sm">
                      -{product.discount_percentage}%
                    </Badge>
                  </div>
                )}
                {product.stock === 0 && (
                  <div className="absolute inset-0 bg-black/60 flex items-center justify-center rounded-t-xl">
                    <Badge variant="default" size="lg">
                      RUPTURE DE STOCK
                    </Badge>
                  </div>
                )}
              </div>

              {/* Product Info */}
              <div className="p-4">
                <h3 className="font-bold text-lg mb-1 line-clamp-1">
                  {product.name}
                </h3>

                <p className="text-sm text-gray-600 line-clamp-2 mb-3">
                  {product.description}
                </p>

                {/* Price */}
                <div className="flex items-baseline gap-2 mb-4">
                  {product.discount_price ? (
                    <>
                      <span className="text-2xl font-bold text-css-gold">
                        {product.discount_price} TND
                      </span>
                      <span className="text-sm text-gray-500 line-through">
                        {product.price} TND
                      </span>
                    </>
                  ) : (
                    <span className="text-2xl font-bold text-black">
                      {product.price} TND
                    </span>
                  )}
                </div>

                {/* Stock Info */}
                {product.stock > 0 && product.stock <= 5 && (
                  <p className="text-xs text-orange-600 mb-3">
                    ⚠️ Plus que {product.stock} en stock
                  </p>
                )}

                {/* Add to Cart Button */}
                <Button
                  variant="primary"
                  size="sm"
                  className="w-full"
                  onClick={() => handleAddToCart(product.id)}
                  disabled={product.stock === 0}
                >
                  {product.stock === 0 ? 'Indisponible' : 'Ajouter au panier'}
                </Button>
              </div>
            </Card>
          ))
        ) : (
          <div className="col-span-full">
            <Card>
              <p className="text-center text-gray-500 py-8">
                Aucun produit disponible
              </p>
            </Card>
          </div>
        )}
      </div>
    </DashboardLayout>
  )
}

export default ShopPage
