import { useState } from 'react'
import { Link } from 'react-router-dom'
import PageTransition from '../components/motion/PageTransition'
import GlassCard from '../components/ui/GlassCard'
import GradientButton from '../components/ui/GradientButton'
import { useAuth } from '../context/AuthContext'

export default function ForgotPassword() {
  const { forgotPassword } = useAuth()
  const [email, setEmail] = useState('')
  const [message, setMessage] = useState(null)
  const [error, setError] = useState(null)
  const [loading, setLoading] = useState(false)

  const handleSubmit = async (e) => {
    e.preventDefault()
    setMessage(null)
    setError(null)
    setLoading(true)
    try {
      const data = await forgotPassword(email)
      setMessage(`Reset token generated. In production this would be emailed. Token: ${data.resetToken}`)
    } catch (err) {
      setError(err.response?.data?.message || 'Something went wrong')
    } finally {
      setLoading(false)
    }
  }

  return (
    <PageTransition>
      <section className="min-h-[80vh] flex items-center justify-center px-6">
        <GlassCard glow className="w-full max-w-md p-8">
          <h2 className="text-2xl font-bold text-gradient mb-2 text-center">Forgot Password</h2>
          <p className="text-dark-muted text-sm text-center mb-6">
            Enter your email and we'll send a reset link.
          </p>

          {message && <p className="text-green-400 text-sm text-center mb-4">{message}</p>}
          {error && <p className="text-red-400 text-sm text-center mb-4">{error}</p>}

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
            <GradientButton type="submit" disabled={loading} className="w-full">
              {loading ? 'Sending...' : 'Send Reset Link'}
            </GradientButton>
          </form>

          <div className="mt-4 text-center text-sm">
            <Link to="/login" className="text-blue-accent hover:underline no-underline">
              Back to sign in
            </Link>
          </div>
        </GlassCard>
      </section>
    </PageTransition>
  )
}
