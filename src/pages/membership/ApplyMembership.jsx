import { useState, useEffect } from 'react'
import { useNavigate } from 'react-router-dom'
import PageTransition from '../../components/motion/PageTransition'
import GlassCard from '../../components/ui/GlassCard'
import GradientButton from '../../components/ui/GradientButton'
import { useAuth } from '../../context/AuthContext'
import api from '../../lib/axios'

export default function ApplyMembership() {
  const { user } = useAuth()
  const navigate = useNavigate()
  const [photo, setPhoto] = useState('')
  const [existing, setExisting] = useState(null)
  const [loading, setLoading] = useState(true)
  const [submitting, setSubmitting] = useState(false)
  const [message, setMessage] = useState(null)
  const [error, setError] = useState(null)

  useEffect(() => {
    if (user?.role !== 'Student Member') { navigate('/'); return }
    api.get('/membership/my').then(({ data }) => {
      setExisting(data.membership)
      if (data.membership?.photo) setPhoto(data.membership.photo)
    }).catch(() => {}).finally(() => setLoading(false))
  }, [user, navigate])

  const handleSubmit = async (e) => {
    e.preventDefault()
    setSubmitting(true)
    setError(null)
    setMessage(null)
    try {
      const payload = { photo: photo || undefined }
      const { data } = await api.post('/membership/apply', payload)
      setExisting(data.membership)
      setMessage('Application submitted successfully')
    } catch (err) {
      setError(err.response?.data?.message || 'Failed to submit application')
    } finally {
      setSubmitting(false)
    }
  }

  const handleRenew = async () => {
    try {
      const { data } = await api.put('/membership/renew')
      setExisting(data.membership)
      setMessage('Renewal request submitted')
    } catch (err) {
      setError(err.response?.data?.message || 'Renewal failed')
    }
  }

  if (loading) return <div className="max-w-lg mx-auto px-6 py-10 text-dark-muted text-sm">Loading...</div>

  const canApply = !existing || existing.status === 'rejected' || existing.status === 'expired'

  return (
    <PageTransition>
      <section className="max-w-lg mx-auto px-6 py-10">
        <h1 className="text-2xl font-bold text-gradient mb-6">Student Membership</h1>

        {message && <p className="text-green-400 text-sm mb-4">{message}</p>}
        {error && <p className="text-red-400 text-sm mb-4">{error}</p>}

        {existing && existing.status !== 'rejected' && existing.status !== 'expired' && (
          <GlassCard glow className="mb-6 text-center">
            <p className="text-sm text-dark-title">Membership Status</p>
            <span className={`inline-block mt-2 text-xs font-semibold px-3 py-1 rounded-full border ${
              existing.status === 'approved' ? 'text-green-400 border-green-400/30 bg-green-400/10' :
              existing.status === 'pending' ? 'text-amber-400 border-amber-400/30 bg-amber-400/10' :
              'text-dark-muted border-gray-500/30'
            }`}>{existing.status}</span>
            {existing.photo && (
              <div className="mt-3">
                <img src={existing.photo} alt="Profile" className="w-20 h-20 rounded-full mx-auto object-cover border-2 border-glass-border" />
              </div>
            )}
            {existing.status === 'approved' && (
              <button onClick={handleRenew} className="mt-4 text-xs px-4 py-2 rounded-2xl border border-amber-400/30 text-amber-400 hover:bg-amber-400/10 transition-colors cursor-pointer bg-transparent">Renew Membership</button>
            )}
          </GlassCard>
        )}

        {canApply && (
          <GlassCard>
            <h3 className="text-sm font-semibold text-dark-title mb-4">
              {existing ? 'Re-apply for Membership' : 'Apply for Membership'}
            </h3>
            <form onSubmit={handleSubmit} className="space-y-4">
              <div>
                <label className="block text-xs text-dark-muted mb-1">Photo URL (optional)</label>
                <input value={photo} onChange={(e) => setPhoto(e.target.value)} className="w-full px-4 py-2.5 rounded-2xl bg-warm-card border border-glass-border text-dark-title text-sm placeholder-dark-subtle focus:outline-none focus:border-blue-accent" placeholder="https://example.com/photo.jpg" />
              </div>
              <div className="text-xs text-dark-subtle">
                <p>Name: {user?.name}</p>
                <p>Email: {user?.email}</p>
              </div>
              <GradientButton type="submit" disabled={submitting} className="text-sm">
                {submitting ? 'Submitting...' : 'Submit Application'}
              </GradientButton>
            </form>
          </GlassCard>
        )}
      </section>
    </PageTransition>
  )
}
