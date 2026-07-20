import { useState, useEffect } from 'react'
import { useParams, useNavigate } from 'react-router-dom'
import PageTransition from '../../components/motion/PageTransition'
import GlassCard from '../../components/ui/GlassCard'
import GradientButton from '../../components/ui/GradientButton'
import { useAuth } from '../../context/AuthContext'
import api from '../../lib/axios'

const MANAGER_ROLES = ['Faculty Coordinator', 'Association President', 'Vice President']
const CATEGORIES = ['Workshop', 'Hackathon', 'Tech Talk', 'Seminar', 'Cultural', 'Competition', 'Other']

export default function EditEvent() {
  const { id } = useParams()
  const { user } = useAuth()
  const navigate = useNavigate()

  const [form, setForm] = useState({
    name: '', category: 'Workshop', date: '', time: '', venue: '',
    description: '', maxParticipants: '', registrationDeadline: '', assignedFaculty: '',
    status: 'draft',
  })
  const [faculty, setFaculty] = useState([])
  const [loading, setLoading] = useState(true)
  const [saving, setSaving] = useState(false)
  const [error, setError] = useState(null)

  useEffect(() => {
    if (!MANAGER_ROLES.includes(user?.role)) { navigate('/events'); return }
    Promise.all([
      api.get(`/events/${id}`),
      api.get('/users?role=Faculty Coordinator').catch(() => ({ data: { users: [] } })),
    ]).then(([ev, fac]) => {
      const e = ev.data.event
      setForm({
        name: e.name || '',
        category: e.category || 'Workshop',
        date: e.date ? e.date.slice(0, 10) : '',
        time: e.time || '',
        venue: e.venue || '',
        description: e.description || '',
        maxParticipants: e.maxParticipants || '',
        registrationDeadline: e.registrationDeadline ? e.registrationDeadline.slice(0, 10) : '',
        assignedFaculty: e.assignedFaculty?._id || '',
        status: e.status || 'draft',
      })
      setFaculty(fac.data.users || [])
    }).catch((err) => setError('Failed to load event'))
      .finally(() => setLoading(false))
  }, [id, user, navigate])

  const handleChange = (e) => setForm({ ...form, [e.target.name]: e.target.value })

  const handleSubmit = async (e) => {
    e.preventDefault()
    setSaving(true)
    setError(null)
    try {
      await api.put(`/events/${id}`, {
        ...form,
        maxParticipants: form.maxParticipants ? Number(form.maxParticipants) : 0,
        registrationDeadline: form.registrationDeadline || undefined,
        assignedFaculty: form.assignedFaculty || undefined,
      })
      navigate(`/events/${id}`)
    } catch (err) {
      setError(err.response?.data?.message || 'Failed to update event')
    } finally {
      setSaving(false)
    }
  }

  if (loading) return <div className="max-w-3xl mx-auto px-6 py-10 text-dark-muted text-sm">Loading...</div>
  if (error) return <div className="max-w-3xl mx-auto px-6 py-10 text-red-400 text-sm">{error}</div>

  return (
    <PageTransition>
      <section className="max-w-3xl mx-auto px-6 py-10">
        <h1 className="text-2xl font-bold text-gradient mb-6">Edit Event</h1>
        {error && <p className="text-red-400 text-sm mb-4">{error}</p>}
        <form onSubmit={handleSubmit} className="space-y-5">
          <GlassCard>
            <div className="grid grid-cols-1 sm:grid-cols-2 gap-4">
              <div className="sm:col-span-2">
                <label className="block text-xs text-dark-muted mb-1">Name *</label>
                <input name="name" value={form.name} onChange={handleChange} required className="w-full px-4 py-2.5 rounded-2xl bg-warm-card border border-glass-border text-dark-title text-sm focus:outline-none focus:border-blue-accent" />
              </div>
              <div>
                <label className="block text-xs text-dark-muted mb-1">Category *</label>
                <select name="category" value={form.category} onChange={handleChange} className="w-full px-4 py-2.5 rounded-2xl bg-warm-card border border-glass-border text-dark-title text-sm focus:outline-none focus:border-blue-accent">
                  {CATEGORIES.map((c) => <option key={c} value={c}>{c}</option>)}
                </select>
              </div>
              <div>
                <label className="block text-xs text-dark-muted mb-1">Status</label>
                <select name="status" value={form.status} onChange={handleChange} className="w-full px-4 py-2.5 rounded-2xl bg-warm-card border border-glass-border text-dark-title text-sm focus:outline-none focus:border-blue-accent">
                  <option value="draft">Draft</option>
                  <option value="published">Published</option>
                  <option value="completed">Completed</option>
                  <option value="cancelled">Cancelled</option>
                </select>
              </div>
              <div>
                <label className="block text-xs text-dark-muted mb-1">Date *</label>
                <input type="date" name="date" value={form.date} onChange={handleChange} required className="w-full px-4 py-2.5 rounded-2xl bg-warm-card border border-glass-border text-dark-title text-sm focus:outline-none focus:border-blue-accent" />
              </div>
              <div>
                <label className="block text-xs text-dark-muted mb-1">Time *</label>
                <input type="time" name="time" value={form.time} onChange={handleChange} required className="w-full px-4 py-2.5 rounded-2xl bg-warm-card border border-glass-border text-dark-title text-sm focus:outline-none focus:border-blue-accent" />
              </div>
              <div>
                <label className="block text-xs text-dark-muted mb-1">Venue</label>
                <input name="venue" value={form.venue} onChange={handleChange} className="w-full px-4 py-2.5 rounded-2xl bg-warm-card border border-glass-border text-dark-title text-sm placeholder-dark-subtle focus:outline-none focus:border-blue-accent" />
              </div>
              <div>
                <label className="block text-xs text-dark-muted mb-1">Max Participants</label>
                <input type="number" name="maxParticipants" value={form.maxParticipants} onChange={handleChange} min="0" className="w-full px-4 py-2.5 rounded-2xl bg-warm-card border border-glass-border text-dark-title text-sm placeholder-dark-subtle focus:outline-none focus:border-blue-accent" />
              </div>
              <div>
                <label className="block text-xs text-dark-muted mb-1">Registration Deadline</label>
                <input type="date" name="registrationDeadline" value={form.registrationDeadline} onChange={handleChange} className="w-full px-4 py-2.5 rounded-2xl bg-warm-card border border-glass-border text-dark-title text-sm focus:outline-none focus:border-blue-accent" />
              </div>
              <div>
                <label className="block text-xs text-dark-muted mb-1">Assigned Faculty</label>
                <select name="assignedFaculty" value={form.assignedFaculty} onChange={handleChange} className="w-full px-4 py-2.5 rounded-2xl bg-warm-card border border-glass-border text-dark-title text-sm focus:outline-none focus:border-blue-accent">
                  <option value="">None</option>
                  {faculty.map((f) => <option key={f._id} value={f._id}>{f.name}</option>)}
                </select>
              </div>
              <div className="sm:col-span-2">
                <label className="block text-xs text-dark-muted mb-1">Description</label>
                <textarea name="description" value={form.description} onChange={handleChange} rows={4} className="w-full px-4 py-3 rounded-2xl bg-warm-card border border-glass-border text-dark-title text-sm placeholder-dark-subtle focus:outline-none focus:border-blue-accent resize-y" />
              </div>
            </div>
          </GlassCard>
          <div className="flex gap-3">
            <GradientButton type="submit" disabled={saving} className="text-sm">{saving ? 'Saving...' : 'Save Changes'}</GradientButton>
            <button type="button" onClick={() => navigate(`/events/${id}`)} className="text-sm px-6 py-2.5 rounded-2xl border border-glass-border text-dark-muted hover:text-dark-title transition-colors cursor-pointer bg-transparent">Cancel</button>
          </div>
        </form>
      </section>
    </PageTransition>
  )
}
