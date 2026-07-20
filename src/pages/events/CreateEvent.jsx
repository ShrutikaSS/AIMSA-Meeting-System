import { useState, useEffect } from 'react'
import { useNavigate } from 'react-router-dom'
import PageTransition from '../../components/motion/PageTransition'
import GlassCard from '../../components/ui/GlassCard'
import GradientButton from '../../components/ui/GradientButton'
import { useAuth } from '../../context/AuthContext'
import api from '../../lib/axios'

const MANAGER_ROLES = ['Faculty Coordinator', 'Association President', 'Vice President']
const CATEGORIES = ['Workshop', 'Hackathon', 'Tech Talk', 'Seminar', 'Cultural', 'Competition', 'Other']

export default function CreateEvent() {
  const { user } = useAuth()
  const navigate = useNavigate()

  const [form, setForm] = useState({
    name: '', category: 'Workshop', date: '', time: '', venue: '',
    description: '', maxParticipants: '', registrationDeadline: '', assignedFaculty: '',
  })
  const [faculty, setFaculty] = useState([])
  const [loading, setLoading] = useState(false)
  const [error, setError] = useState(null)

  useEffect(() => {
    if (!MANAGER_ROLES.includes(user?.role)) { navigate('/events'); return }
    api.get('/users?role=Faculty Coordinator').then(({ data }) => setFaculty(data.users || [])).catch(() => {})
  }, [user, navigate])

  const handleChange = (e) => setForm({ ...form, [e.target.name]: e.target.value })

  const handleSubmit = async (e) => {
    e.preventDefault()
    setLoading(true)
    setError(null)
    try {
      const payload = {
        ...form,
        maxParticipants: form.maxParticipants ? Number(form.maxParticipants) : 0,
        registrationDeadline: form.registrationDeadline || undefined,
        assignedFaculty: form.assignedFaculty || undefined,
      }
      const { data } = await api.post('/events', payload)
      navigate(`/events/${data.event._id}`)
    } catch (err) {
      setError(err.response?.data?.message || 'Failed to create event')
    } finally {
      setLoading(false)
    }
  }

  return (
    <PageTransition>
      <section className="max-w-3xl mx-auto px-6 py-10">
        <h1 className="text-2xl font-bold text-gradient mb-6">Create Event</h1>
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
                <label className="block text-xs text-dark-muted mb-1">Date *</label>
                <input type="date" name="date" value={form.date} onChange={handleChange} required className="w-full px-4 py-2.5 rounded-2xl bg-warm-card border border-glass-border text-dark-title text-sm focus:outline-none focus:border-blue-accent" />
              </div>
              <div>
                <label className="block text-xs text-dark-muted mb-1">Time *</label>
                <input type="time" name="time" value={form.time} onChange={handleChange} required className="w-full px-4 py-2.5 rounded-2xl bg-warm-card border border-glass-border text-dark-title text-sm focus:outline-none focus:border-blue-accent" />
              </div>
              <div>
                <label className="block text-xs text-dark-muted mb-1">Venue</label>
                <input name="venue" value={form.venue} onChange={handleChange} className="w-full px-4 py-2.5 rounded-2xl bg-warm-card border border-glass-border text-dark-title text-sm placeholder-dark-subtle focus:outline-none focus:border-blue-accent" placeholder="Auditorium / Online" />
              </div>
              <div>
                <label className="block text-xs text-dark-muted mb-1">Max Participants</label>
                <input type="number" name="maxParticipants" value={form.maxParticipants} onChange={handleChange} min="0" className="w-full px-4 py-2.5 rounded-2xl bg-warm-card border border-glass-border text-dark-title text-sm placeholder-dark-subtle focus:outline-none focus:border-blue-accent" placeholder="0 = unlimited" />
              </div>
              <div>
                <label className="block text-xs text-dark-muted mb-1">Registration Deadline</label>
                <input type="date" name="registrationDeadline" value={form.registrationDeadline} onChange={handleChange} className="w-full px-4 py-2.5 rounded-2xl bg-warm-card border border-glass-border text-dark-title text-sm focus:outline-none focus:border-blue-accent" />
              </div>
              <div>
                <label className="block text-xs text-dark-muted mb-1">Assigned Faculty Coordinator</label>
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
            <GradientButton type="submit" disabled={loading} className="text-sm">{loading ? 'Creating...' : 'Create Event'}</GradientButton>
            <button type="button" onClick={() => navigate('/events')} className="text-sm px-6 py-2.5 rounded-2xl border border-glass-border text-dark-muted hover:text-dark-title transition-colors cursor-pointer bg-transparent">Cancel</button>
          </div>
        </form>
      </section>
    </PageTransition>
  )
}
