import { useState, useEffect } from 'react'
import { useNavigate } from 'react-router-dom'
import PageTransition from '../../components/motion/PageTransition'
import GlassCard from '../../components/ui/GlassCard'
import GradientButton from '../../components/ui/GradientButton'
import { useAuth } from '../../context/AuthContext'
import api from '../../lib/axios'

const POSTER_ROLES = ['Administrator', 'Association President', 'Faculty Coordinator']

export default function CreateAnnouncement() {
  const { user } = useAuth()
  const navigate = useNavigate()

  const [form, setForm] = useState({
    title: '', body: '', expiryDate: '', pinned: false, event: '',
  })
  const [events, setEvents] = useState([])
  const [loading, setLoading] = useState(false)
  const [error, setError] = useState(null)

  useEffect(() => {
    if (!POSTER_ROLES.includes(user?.role)) { navigate('/announcements'); return }
    api.get('/events?status=published').then(({ data }) => setEvents(data.events || [])).catch(() => {})
  }, [user, navigate])

  const handleChange = (e) => {
    const value = e.target.type === 'checkbox' ? e.target.checked : e.target.value
    setForm({ ...form, [e.target.name]: value })
  }

  const handleSubmit = async (e) => {
    e.preventDefault()
    setLoading(true)
    setError(null)
    try {
      const payload = {
        ...form,
        expiryDate: form.expiryDate || undefined,
        event: form.event || undefined,
      }
      const { data } = await api.post('/announcements', payload)
      navigate(`/announcements`)
    } catch (err) {
      setError(err.response?.data?.message || 'Failed to create announcement')
    } finally {
      setLoading(false)
    }
  }

  return (
    <PageTransition>
      <section className="max-w-3xl mx-auto px-6 py-10">
        <h1 className="text-2xl font-bold text-gradient mb-6">Post Announcement</h1>
        {error && <p className="text-red-400 text-sm mb-4">{error}</p>}
        <form onSubmit={handleSubmit} className="space-y-5">
          <GlassCard>
            <div className="grid grid-cols-1 sm:grid-cols-2 gap-4">
              <div className="sm:col-span-2">
                <label className="block text-xs text-dark-muted mb-1">Title *</label>
                <input name="title" value={form.title} onChange={handleChange} required className="w-full px-4 py-2.5 rounded-2xl bg-warm-card border border-glass-border text-dark-title text-sm focus:outline-none focus:border-blue-accent" />
              </div>
              <div className="sm:col-span-2">
                <label className="block text-xs text-dark-muted mb-1">Body</label>
                <textarea name="body" value={form.body} onChange={handleChange} rows={5} className="w-full px-4 py-3 rounded-2xl bg-warm-card border border-glass-border text-dark-title text-sm placeholder-dark-subtle focus:outline-none focus:border-blue-accent resize-y" placeholder="Announcement details..." />
              </div>
              <div>
                <label className="block text-xs text-dark-muted mb-1">Expiry Date</label>
                <input type="date" name="expiryDate" value={form.expiryDate} onChange={handleChange} className="w-full px-4 py-2.5 rounded-2xl bg-warm-card border border-glass-border text-dark-title text-sm focus:outline-none focus:border-blue-accent" />
              </div>
              <div>
                <label className="block text-xs text-dark-muted mb-1">Link to Event (optional)</label>
                <select name="event" value={form.event} onChange={handleChange} className="w-full px-4 py-2.5 rounded-2xl bg-warm-card border border-glass-border text-dark-title text-sm focus:outline-none focus:border-blue-accent">
                  <option value="">None</option>
                  {events.map((e) => <option key={e._id} value={e._id}>{e.name}</option>)}
                </select>
              </div>
              <div className="flex items-center gap-2">
                <input type="checkbox" name="pinned" id="pinned" checked={form.pinned} onChange={handleChange} className="accent-blue-accent" />
                <label htmlFor="pinned" className="text-xs text-dark-body">Pin to top</label>
              </div>
            </div>
          </GlassCard>
          <div className="flex gap-3">
            <GradientButton type="submit" disabled={loading} className="text-sm">{loading ? 'Posting...' : 'Post Announcement'}</GradientButton>
            <button type="button" onClick={() => navigate('/announcements')} className="text-sm px-6 py-2.5 rounded-2xl border border-glass-border text-dark-muted hover:text-dark-title transition-colors cursor-pointer bg-transparent">Cancel</button>
          </div>
        </form>
      </section>
    </PageTransition>
  )
}
