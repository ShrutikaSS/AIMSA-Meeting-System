import { createContext, useContext, useState, useCallback, useEffect, useRef } from 'react'
import { useNavigate } from 'react-router-dom'
import api from '../lib/axios'

const SESSION_TIMEOUT_MS = 60 * 60 * 1000

const roleToDashboard = {
  Administrator: '/dashboard/administrator',
  'Faculty Coordinator': '/dashboard/faculty',
  'Association President': '/dashboard/president',
  'Vice President': '/dashboard/vice-president',
  'Committee Member': '/dashboard/committee',
  'Student Member': '/dashboard/student',
}

const AuthContext = createContext(null)

function getStoredUser() {
  try {
    const u = localStorage.getItem('aimsa_user')
    return u ? JSON.parse(u) : null
  } catch {
    return null
  }
}

export function AuthProvider({ children }) {
  const [user, setUser] = useState(getStoredUser)
  const [loading, setLoading] = useState(false)
  const [error, setError] = useState(null)
  const navigate = useNavigate()
  const timeoutRef = useRef(null)

  const getDashboardRoute = useCallback((role) => roleToDashboard[role] || '/', [])

  const clearSession = useCallback(() => {
    localStorage.removeItem('aimsa_token')
    localStorage.removeItem('aimsa_user')
    setUser(null)
    if (timeoutRef.current) clearTimeout(timeoutRef.current)
  }, [])

  const resetSessionTimer = useCallback(() => {
    if (timeoutRef.current) clearTimeout(timeoutRef.current)
    timeoutRef.current = setTimeout(() => {
      clearSession()
      navigate('/login')
    }, SESSION_TIMEOUT_MS)
  }, [clearSession, navigate])

  useEffect(() => {
    const handleActivity = () => resetSessionTimer()
    window.addEventListener('mousemove', handleActivity)
    window.addEventListener('keydown', handleActivity)
    window.addEventListener('click', handleActivity)
    if (user) resetSessionTimer()
    return () => {
      window.removeEventListener('mousemove', handleActivity)
      window.removeEventListener('keydown', handleActivity)
      window.removeEventListener('click', handleActivity)
      if (timeoutRef.current) clearTimeout(timeoutRef.current)
    }
  }, [user, resetSessionTimer])

  const login = useCallback(async (email, password) => {
    setLoading(true)
    setError(null)
    try {
      const { data } = await api.post('/auth/login', { email, password })
      if (!data.success) throw new Error(data.message || 'Login failed')
      localStorage.setItem('aimsa_token', data.token)
      localStorage.setItem('aimsa_user', JSON.stringify(data.user))
      setUser(data.user)
      const route = roleToDashboard[data.user.role]
      navigate(route || '/')
      return data
    } catch (err) {
      const msg = err.response?.data?.message || err.message || 'Login failed'
      setError(msg)
      throw new Error(msg)
    } finally {
      setLoading(false)
    }
  }, [navigate])

  const register = useCallback(async (userData) => {
    setLoading(true)
    setError(null)
    try {
      const { data } = await api.post('/auth/register', userData)
      if (!data.success) throw new Error(data.message || 'Registration failed')
      localStorage.setItem('aimsa_token', data.token)
      localStorage.setItem('aimsa_user', JSON.stringify(data.user))
      setUser(data.user)
      const route = roleToDashboard[data.user.role]
      navigate(route || '/')
      return data
    } catch (err) {
      const msg = err.response?.data?.message || err.message || 'Registration failed'
      setError(msg)
      throw new Error(msg)
    } finally {
      setLoading(false)
    }
  }, [navigate])

  const logout = useCallback(() => {
    clearSession()
    navigate('/login')
  }, [clearSession, navigate])

  const changePassword = useCallback(async (currentPassword, newPassword) => {
    const { data } = await api.put('/auth/change-password', { currentPassword, newPassword })
    return data
  }, [])

  const forgotPassword = useCallback(async (email) => {
    const { data } = await api.post('/auth/forgot-password', { email })
    return data
  }, [])

  return (
    <AuthContext.Provider
      value={{
        user,
        loading,
        error,
        login,
        register,
        logout,
        changePassword,
        forgotPassword,
        getDashboardRoute,
        isAuthenticated: !!user,
      }}
    >
      {children}
    </AuthContext.Provider>
  )
}

export function useAuth() {
  const ctx = useContext(AuthContext)
  if (!ctx) throw new Error('useAuth must be used within AuthProvider')
  return ctx
}
