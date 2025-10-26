import { createContext, useContext, useState, useEffect } from 'react'
import { useNavigate } from 'react-router-dom'
import api from '../api/axios'

const AuthContext = createContext(null)

export const AuthProvider = ({ children }) => {
  const [user, setUser] = useState(null)
  const [token, setToken] = useState(localStorage.getItem('token'))
  const [loading, setLoading] = useState(true)

  useEffect(() => {
    const storedUser = localStorage.getItem('user')
    const storedToken = localStorage.getItem('token')
    
    if (storedUser && storedToken && storedUser !== 'undefined') {
      try {
        setUser(JSON.parse(storedUser))
        setToken(storedToken)
        api.defaults.headers.common['Authorization'] = `Bearer ${storedToken}`
      } catch (error) {
        // If parsing fails, clear localStorage
        localStorage.removeItem('user')
        localStorage.removeItem('token')
      }
    }
    setLoading(false)
  }, [])

  const login = async (username, password) => {
    try {
      const response = await api.post('/api/auth', { username, password })
      const { token: newToken, user: userData } = response.data
      
      // Validate response structure
      if (!newToken || !userData || !userData.id || !userData.role) {
        return {
          success: false,
          error: 'Invalid response from server'
        }
      }
      
      setUser(userData)
      setToken(newToken)
      localStorage.setItem('token', newToken)
      localStorage.setItem('user', JSON.stringify(userData))
      api.defaults.headers.common['Authorization'] = `Bearer ${newToken}`
      
      return { success: true }
    } catch (error) {
      return { 
        success: false, 
        error: error.response?.data?.error || 'Login failed' 
      }
    }
  }

  const logout = () => {
    setUser(null)
    setToken(null)
    localStorage.removeItem('token')
    localStorage.removeItem('user')
    delete api.defaults.headers.common['Authorization']
  }

  const value = {
    user,
    token,
    login,
    logout,
    loading,
    isAuthenticated: !!token,
    isManager: user?.role === 'manager',
    isEmployee: user?.role === 'employee'
  }

  return <AuthContext.Provider value={value}>{children}</AuthContext.Provider>
}

export const useAuth = () => {
  const context = useContext(AuthContext)
  if (!context) {
    throw new Error('useAuth must be used within an AuthProvider')
  }
  return context
}
