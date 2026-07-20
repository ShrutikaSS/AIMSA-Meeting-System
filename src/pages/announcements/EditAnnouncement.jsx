import { useState, useEffect } from 'react'
import { useParams, useNavigate } from 'react-router-dom'
import PageTransition from '../../components/motion/PageTransition'
import GlassCard from '../../components/ui/GlassCard'
import GradientButton from '../../components/ui/GradientButton'
import { useAuth } from '../../context/AuthContext'
import api from '../../lib/axios'

const POSTER_ROLES = ['Administrator', 'Association President', 'Faculty Coordinator']

export default function EditAnnouncement() {
  const { id } = useParams()
  const { user } = useAuth()
  const navigate = useNavigate()

  const [form, setForm] = useState({
    title: '', body: '', expiryDate: '', pinned: false, event: '',
  })
  const [events, setEvents] = useState([])
  const [loading, setLoading] = useState(true)
  const [saving, setSaving] = useState(false)
  const [error, setError] = useState(null)

  useEffect(() => {
    if (!POSTER_ROLES.includes(user?.role)) { navigate('/announcements'); return }
    Promise.all([
      api.get(`/announcements/${id}`),
      api.get('/events?status=published').catch(() => ({ data: { events: [] } })),
    ]).then(([ann, ev]) => {
      const a = ann.data.announcement
      setForm({
        title: a.title || '',
        body: a.body || '',
        expiryDate: a.expiryDate ? a.expiryDate.slice(0, 10) : '',
        pinned: a.pinned || false,
        event: a.event?._id || '',
      })
      setEvents(ev.data.events || [])
    }).catch(() => setError('Failed to load announcement'))
      .finally(() => setLoading(false))
  }, [id, user, navigate])

  const handleChange = (e) => {
    const value = e.target.type === 'checkbox' ? e.target.checked : e.target.value
    setForm({ ...form, [e.target.name]: value })
  }

  const handleSubmit = async (e) => {
    e.preventDefault()
    setSaving(true)
    setError(null)
    try {
      await api.put(`/announcements/${id}`, {
        ...form,
        expiryDate: form.expiryDate || undefined,
        event: form.event || undefined,
      })
      navigate('/announcements')
    } catch (err) {
      setError(err.response?.data?.message || 'Failed to update')
    } finally {
      setSaving(false)
    }
  }

  if (loading) return <div className="max-w-3xl mx-auto px-6 py-10 text-dark-muted text-sm">Loading...</div>
  if (error) return <div className="max-w-3xl mx-auto px-6 py-10 text-red-400 text-sm">{error}</div>

  return (
    <PageTransition>
      <section className="max-w-3xl mx-auto px-6 py-10">
        <h1 className="text-2xl font-bold text-gradient mb-6">Edit Announcement</h1>
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
                <textarea name="body" value={form.body} onChange={handleChange} rows={5} className="w-full px-4 py-3 rounded-2xl bg-warm-card border border-glass-border text-dark-title text-sm placeholder-dark-subtle focus:outline-none focus:border-blue-accent resize-y" />
              </div>
              <div>
                <label className="block text-xs text-dark-muted mb-1">Expiry Date</label>
                <input type="date" name="expiryDate" value={form.expiryDate} onChange={handleChange} className="w-full px-4 py-2.5 rounded-2xl bg-warm-card border border-glass-border text-dark-title text-sm focus:outline-none focus:border-blue-accent" />
              </div>
              <div>
                <label className="block text-xs text-dark-muted mb-1">Link to Event</label>
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
            <GradientButton type="submit" disabled={saving} className="text-sm">{saving ? 'Saving...' : 'Save Changes'}</GradientButton>
            <button type="button" onClick={() => navigate('/announcements')} className="text-sm px-6 py-2.5 rounded-2xl border border-glass-border text-dark-muted hover:text-dark-title transition-colors cursor-pointer bg-transparent">Cancel</button>
          </div>
        </form>
      </section>
    </PageTransition>
  )
}
