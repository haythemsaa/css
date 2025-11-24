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
}

export const api = new ApiService()
export default api
