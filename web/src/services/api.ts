import axios, { AxiosInstance } from 'axios'

const API_BASE_URL = import.meta.env.VITE_API_URL || 'http://localhost:8000/api/v1'

class ApiService {
  private api: AxiosInstance
  private token: string | null = null

  constructor() {
    this.api = axios.create({
      baseURL: API_BASE_URL,
      timeout: 30000,
      headers: {
        'Content-Type': 'application/json',
        'Accept': 'application/json',
      },
    })

    // Request interceptor
    this.api.interceptors.request.use(
      (config) => {
        if (this.token) {
          config.headers.Authorization = `Bearer ${this.token}`
        }
        return config
      },
      (error) => Promise.reject(error)
    )

    // Response interceptor
    this.api.interceptors.response.use(
      (response) => response,
      (error) => {
        if (error.response?.status === 401) {
          this.clearToken()
          window.location.href = '/login'
        }
        return Promise.reject(error)
      }
    )

    // Load token from localStorage
    const savedToken = localStorage.getItem('auth_token')
    if (savedToken) {
      this.token = savedToken
    }
  }

  setToken(token: string) {
    this.token = token
    localStorage.setItem('auth_token', token)
  }

  clearToken() {
    this.token = null
    localStorage.removeItem('auth_token')
  }

  // Auth
  async login(email: string, password: string) {
    const response = await this.api.post('/auth/login', { email, password })
    return response.data
  }

  async register(data: Record<string, any>) {
    const response = await this.api.post('/auth/register', data)
    return response.data
  }

  async logout() {
    const response = await this.api.post('/auth/logout')
    this.clearToken()
    return response.data
  }

  async getProfile() {
    const response = await this.api.get('/user/profile')
    return response.data
  }

  // Contents
  async getContents(params?: {
    type?: string
    category_id?: number
    page?: number
  }) {
    const response = await this.api.get('/contents', { params })
    return response.data
  }

  async getContent(slug: string) {
    const response = await this.api.get(`/contents/${slug}`)
    return response.data
  }

  async getFeaturedContents() {
    const response = await this.api.get('/contents/featured')
    return response.data
  }

  // Partners
  async getPartners(params?: {
    category_id?: number
    city?: string
    page?: number
  }) {
    const response = await this.api.get('/partners', { params })
    return response.data
  }

  async getPartner(id: number) {
    const response = await this.api.get(`/partners/${id}`)
    return response.data
  }

  async getNearbyPartners(lat: number, lng: number, radius: number = 5) {
    const response = await this.api.get('/partners/nearby', {
      params: { latitude: lat, longitude: lng, radius },
    })
    return response.data
  }

  // Reduction Codes
  async generateReductionCode(partnerId: number, offerId?: number) {
    const response = await this.api.post('/reductions/generate', {
      partner_id: partnerId,
      ...(offerId && { offer_id: offerId }),
    })
    return response.data
  }

  async getActiveReductionCodes() {
    const response = await this.api.get('/reductions/active')
    return response.data
  }

  async getReductionStats() {
    const response = await this.api.get('/reductions/stats')
    return response.data
  }

  // Matches
  async getMatches(params?: { status?: string; page?: number }) {
    const response = await this.api.get('/matches', { params })
    return response.data
  }

  async getMatch(id: number) {
    const response = await this.api.get(`/matches/${id}`)
    return response.data
  }

  async getLiveMatch(id: number) {
    const response = await this.api.get(`/matches/${id}/live`)
    return response.data
  }

  // Donations
  async makeDonation(data: {
    amount: number
    campaign_id?: number
    payment_method: string
    is_anonymous?: boolean
    message?: string
  }) {
    const response = await this.api.post('/donations', data)
    return response.data
  }

  async getDonationHistory(page: number = 1) {
    const response = await this.api.get('/donations/history', {
      params: { page },
    })
    return response.data
  }

  // Campaigns
  async getCampaigns(params?: { status?: string; type?: string }) {
    const response = await this.api.get('/campaigns', { params })
    return response.data
  }

  async getCampaign(slug: string) {
    const response = await this.api.get(`/campaigns/${slug}`)
    return response.data
  }

  // Products
  async getProducts(params?: { category?: string; search?: string; page?: number }) {
    const response = await this.api.get('/products', { params })
    return response.data
  }

  async getProduct(id: number) {
    const response = await this.api.get(`/products/${id}`)
    return response.data
  }

  // Cart
  async getCart() {
    const response = await this.api.get('/cart')
    return response.data
  }

  async addToCart(productId: number, quantity: number = 1, variantId?: number) {
    const response = await this.api.post('/cart/items', {
      product_id: productId,
      quantity,
      ...(variantId && { variant_id: variantId })
    })
    return response.data
  }

  async updateCartItem(itemId: number, quantity: number) {
    const response = await this.api.put(`/cart/items/${itemId}`, { quantity })
    return response.data
  }

  async removeFromCart(itemId: number) {
    const response = await this.api.delete(`/cart/items/${itemId}`)
    return response.data
  }

  async clearCart() {
    const response = await this.api.delete('/cart')
    return response.data
  }

  // Orders
  async createOrder(data: {
    payment_method: string
    shipping_address: Record<string, any>
    notes?: string
  }) {
    const response = await this.api.post('/orders', data)
    return response.data
  }

  async getOrders(page: number = 1) {
    const response = await this.api.get('/orders', { params: { page } })
    return response.data
  }

  async getOrder(id: number) {
    const response = await this.api.get(`/orders/${id}`)
    return response.data
  }

  // Auctions
  async getAuctions(params?: { status?: string; page?: number }) {
    const response = await this.api.get('/auctions', { params })
    return response.data
  }

  async getAuction(id: number) {
    const response = await this.api.get(`/auctions/${id}`)
    return response.data
  }

  async placeBid(auctionId: number, amount: number) {
    const response = await this.api.post(`/auctions/${auctionId}/bids`, { amount })
    return response.data
  }

  // Donation Goals
  async getDonationGoals(params?: { status?: string }) {
    const response = await this.api.get('/donation-goals', { params })
    return response.data
  }

  async getDonationGoal(id: number) {
    const response = await this.api.get(`/donation-goals/${id}`)
    return response.data
  }

  async donateToDonationGoal(goalId: number, amount: number, paymentMethod: string) {
    const response = await this.api.post(`/donation-goals/${goalId}/donate`, {
      amount,
      payment_method: paymentMethod
    })
    return response.data
  }

  // Polls
  async getPolls(params?: { status?: string; category?: string }) {
    const response = await this.api.get('/polls', { params })
    return response.data
  }

  async getPoll(id: number) {
    const response = await this.api.get(`/polls/${id}`)
    return response.data
  }

  async votePoll(pollId: number, optionId: number) {
    const response = await this.api.post(`/polls/${pollId}/vote`, {
      poll_option_id: optionId
    })
    return response.data
  }

  // Players
  async getPlayers(params?: { position?: string; page?: number }) {
    const response = await this.api.get('/players', { params })
    return response.data
  }

  async getPlayer(id: number) {
    const response = await this.api.get(`/players/${id}`)
    return response.data
  }

  // Events
  async getEvents(params?: { type?: string; upcoming?: boolean }) {
    const response = await this.api.get('/events', { params })
    return response.data
  }

  async getEvent(id: number) {
    const response = await this.api.get(`/events/${id}`)
    return response.data
  }

  async registerForEvent(eventId: number, data?: Record<string, any>) {
    const response = await this.api.post(`/events/${eventId}/register`, data)
    return response.data
  }

  // Fan Tokens
  async getFanTokenWallet() {
    const response = await this.api.get('/fan-tokens/wallet')
    return response.data
  }

  async getFanTokenTransactions() {
    const response = await this.api.get('/fan-tokens/transactions')
    return response.data
  }

  async getRewardsStore() {
    const response = await this.api.get('/fan-tokens/rewards')
    return response.data
  }

  async redeemReward(rewardId: number) {
    const response = await this.api.post(`/fan-tokens/rewards/${rewardId}/redeem`)
    return response.data
  }

  // Tickets Marketplace
  async getTicketListings(params?: { match_id?: number }) {
    const response = await this.api.get('/tickets/marketplace', { params })
    return response.data
  }

  async createTicketListing(data: {
    match_id: number
    seat_section: string
    seat_row: string
    seat_number: string
    price: number
  }) {
    const response = await this.api.post('/tickets/marketplace', data)
    return response.data
  }

  async buyTicket(listingId: number, paymentMethod: string) {
    const response = await this.api.post(`/tickets/marketplace/${listingId}/buy`, {
      payment_method: paymentMethod
    })
    return response.data
  }

  // Badges
  async getUserBadges() {
    const response = await this.api.get('/badges/user')
    return response.data
  }

  async getAllBadges() {
    const response = await this.api.get('/badges')
    return response.data
  }

  // Notifications
  async getNotifications(page: number = 1) {
    const response = await this.api.get('/notifications', { params: { page } })
    return response.data
  }

  async markNotificationAsRead(id: number) {
    const response = await this.api.put(`/notifications/${id}/read`)
    return response.data
  }

  async markAllNotificationsAsRead() {
    const response = await this.api.put('/notifications/read-all')
    return response.data
  }

  // Search
  async search(query: string, filters?: Record<string, any>) {
    const response = await this.api.get('/search', {
      params: { q: query, ...filters }
    })
    return response.data
  }
}

export const api = new ApiService()
export default api
