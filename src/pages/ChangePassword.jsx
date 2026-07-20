import { useState } from 'react'
import PageTransition from '../components/motion/PageTransition'
import GlassCard from '../components/ui/GlassCard'
import GradientButton from '../components/ui/GradientButton'
import { useAuth } from '../context/AuthContext'

export default function ChangePassword() {
  const { changePassword } = useAuth()
  const [currentPassword, setCurrentPassword] = useState('')
  const [newPassword, setNewPassword] = useState('')
  const [message, setMessage] = useState(null)
  const [error, setError] = useState(null)
  const [loading, setLoading] = useState(false)

  const handleSubmit = async (e) => {
    e.preventDefault()
    setMessage(null)
    setError(null)
    setLoading(true)
    try {
      const data = await changePassword(currentPassword, newPassword)
      setMessage(data.message || 'Password changed successfully')
      setCurrentPassword('')
      setNewPassword('')
    } catch (err) {
      setError(err.response?.data?.message || 'Failed to change password')
    } finally {
      setLoading(false)
    }
  }

  return (
    <PageTransition>
      <section className="min-h-[80vh] flex items-center justify-center px-6">
        <GlassCard glow className="w-full max-w-md p-8">
          <h2 className="text-2xl font-bold text-gradient mb-6 text-center">Change Password</h2>

          {message && <p className="text-green-400 text-sm text-center mb-4">{message}</p>}
          {error && <p className="text-red-400 text-sm text-center mb-4">{error}</p>}

          <form onSubmit={handleSubmit} className="flex flex-col gap-5">
            <div>
              <label className="block text-sm text-dark-muted mb-1">Current Password</label>
              <input
                type="password"
                value={currentPassword}
                onChange={(e) => setCurrentPassword(e.target.value)}
                required
                className="w-full px-4 py-2.5 rounded-2xl bg-warm-card border border-glass-border text-dark-title focus:outline-none focus:border-blue-accent transition-colors"
              />
            </div>
            <div>
              <label className="block text-sm text-dark-muted mb-1">New Password</label>
              <input
                type="password"
                value={newPassword}
                onChange={(e) => setNewPassword(e.target.value)}
                required
                minLength={6}
                className="w-full px-4 py-2.5 rounded-2xl bg-warm-card border border-glass-border text-dark-title focus:outline-none focus:border-blue-accent transition-colors"
              />
            </div>
            <GradientButton type="submit" disabled={loading} className="w-full">
              {loading ? 'Updating...' : 'Change Password'}
            </GradientButton>
          </form>
        </GlassCard>
      </section>
    </PageTransition>
  )
}
