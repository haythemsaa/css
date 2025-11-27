import { create } from 'zustand'
import { persist } from 'zustand/middleware'
import { api } from '../services/api'

interface CartItem {
  id: number
  product_id: number
  product_name: string
  product_image?: string
  quantity: number
  price: number
  variant_id?: number
  variant_name?: string
}

interface CartState {
  items: CartItem[]
  total: number
  itemCount: number
  isLoading: boolean
  fetchCart: () => Promise<void>
  addItem: (productId: number, quantity?: number, variantId?: number) => Promise<void>
  updateItem: (itemId: number, quantity: number) => Promise<void>
  removeItem: (itemId: number) => Promise<void>
  clearCart: () => Promise<void>
}

export const useCartStore = create<CartState>()(
  persist(
    (set, get) => ({
      items: [],
      total: 0,
      itemCount: 0,
      isLoading: false,

      fetchCart: async () => {
        set({ isLoading: true })
        try {
          const response = await api.getCart()
          set({
            items: response.items,
            total: response.total,
            itemCount: response.item_count,
            isLoading: false
          })
        } catch (error) {
          set({ isLoading: false })
          throw error
        }
      },

      addItem: async (productId: number, quantity = 1, variantId?: number) => {
        set({ isLoading: true })
        try {
          await api.addToCart(productId, quantity, variantId)
          await get().fetchCart()
        } catch (error) {
          set({ isLoading: false })
          throw error
        }
      },

      updateItem: async (itemId: number, quantity: number) => {
        set({ isLoading: true })
        try {
          await api.updateCartItem(itemId, quantity)
          await get().fetchCart()
        } catch (error) {
          set({ isLoading: false })
          throw error
        }
      },

      removeItem: async (itemId: number) => {
        set({ isLoading: true })
        try {
          await api.removeFromCart(itemId)
          await get().fetchCart()
        } catch (error) {
          set({ isLoading: false })
          throw error
        }
      },

      clearCart: async () => {
        set({ isLoading: true })
        try {
          await api.clearCart()
          set({
            items: [],
            total: 0,
            itemCount: 0,
            isLoading: false
          })
        } catch (error) {
          set({ isLoading: false })
          throw error
        }
      }
    }),
    {
      name: 'cart-storage'
    }
  )
)
