import { useState } from 'react'
import { Link } from 'react-router-dom'
import PageTransition from '../components/motion/PageTransition'
import GlassCard from '../components/ui/GlassCard'
import GradientButton from '../components/ui/GradientButton'
import { useAuth } from '../context/AuthContext'

export default function Login() {
  const { login, loading, error } = useAuth()
  const [email, setEmail] = useState('')
  const [password, setPassword] = useState('')
  const [localError, setLocalError] = useState(null)

  const handleSubmit = async (e) => {
    e.preventDefault()
    setLocalError(null)
    try {
      await login(email, password)
    } catch (err) {
      setLocalError(err.message)
    }
  }

  return (
    <PageTransition>
      <section className="min-h-[80vh] flex items-center justify-center px-6">
        <GlassCard glow className="w-full max-w-md p-8">
          <h2 className="text-2xl font-bold text-gradient mb-6 text-center">Sign In</h2>

          {(localError || error) && (
            <p className="text-red-400 text-sm text-center mb-4">{localError || error}</p>
          )}

          <form onSubmit={handleSubmit} className="flex flex-col gap-5">
            <div>
              <label className="block text-sm text-dark-muted mb-1">Email</label>
              <input
                type="email"
                value={email}
                onChange={(e) => setEmail(e.target.value)}
                required
                className="w-full px-4 py-2.5 rounded-2xl bg-warm-card border border-glass-border text-dark-title placeholder-dark-subtle focus:outline-none focus:border-blue-accent transition-colors"
                placeholder="you@example.com"
              />
            </div>
            <div>
              <label className="block text-sm text-dark-muted mb-1">Password</label>
              <input
                type="password"
                value={password}
                onChange={(e) => setPassword(e.target.value)}
                required
                className="w-full px-4 py-2.5 rounded-2xl bg-warm-card border border-glass-border text-dark-title placeholder-dark-subtle focus:outline-none focus:border-blue-accent transition-colors"
                placeholder="••••••••"
              />
            </div>
            <GradientButton type="submit" disabled={loading} className="w-full">
              {loading ? 'Signing in...' : 'Sign In'}
            </GradientButton>
          </form>

          <div className="mt-4 text-center text-sm">
            <Link to="/forgot-password" className="text-blue-accent hover:underline no-underline">
              Forgot password?
            </Link>
          </div>
        </GlassCard>
      </section>
    </PageTransition>
  )
}
