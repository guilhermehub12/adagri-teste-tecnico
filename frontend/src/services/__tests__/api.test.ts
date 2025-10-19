import { describe, it, expect, vi, beforeEach } from 'vitest'

// Mock axios before importing api
vi.mock('axios', () => {
  const mockAxiosInstance = {
    defaults: {
      baseURL: 'http://localhost:8001/api'
    },
    interceptors: {
      request: {
        use: vi.fn()
      },
      response: {
        use: vi.fn()
      }
    },
    post: vi.fn()
  }

  return {
    default: {
      create: vi.fn(() => mockAxiosInstance),
      post: vi.fn()
    }
  }
})

// Import api after mocking axios
const { api, login } = await import('../api')

describe('API Service', () => {
  beforeEach(() => {
    vi.clearAllMocks()
  })

  describe('login', () => {
    it('should call login endpoint with credentials', async () => {
      const mockResponse = {
        data: {
          token: 'mock-token',
          user: {
            id: 1,
            name: 'Test User',
            email: 'test@example.com',
            role: 'admin'
          }
        }
      }

      vi.mocked(api.post).mockResolvedValue(mockResponse)

      const credentials = {
        email: 'test@example.com',
        password: 'password123'
      }

      const result = await login(credentials)

      expect(api.post).toHaveBeenCalledWith('/auth/login', credentials)
      expect(result).toEqual(mockResponse.data)
    })

    it('should throw error on failed login', async () => {
      const mockError = new Error('Invalid credentials')
      vi.mocked(api.post).mockRejectedValue(mockError)

      const credentials = {
        email: 'wrong@example.com',
        password: 'wrongpass'
      }

      await expect(login(credentials)).rejects.toThrow('Invalid credentials')
    })
  })

  describe('API interceptors', () => {
    it('should have base URL configured', () => {
      expect(api.defaults.baseURL).toBeDefined()
    })

    it('should have request interceptor configured', () => {
      expect(api.interceptors.request).toBeDefined()
      expect(typeof api.interceptors.request.use).toBe('function')
    })

    it('should have response interceptor configured', () => {
      expect(api.interceptors.response).toBeDefined()
      expect(typeof api.interceptors.response.use).toBe('function')
    })
  })
})
