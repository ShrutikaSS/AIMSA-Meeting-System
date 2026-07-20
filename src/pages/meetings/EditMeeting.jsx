import { useState, useEffect } from 'react'
import { useParams, useNavigate } from 'react-router-dom'
import PageTransition from '../../components/motion/PageTransition'
import GlassCard from '../../components/ui/GlassCard'
import GradientButton from '../../components/ui/GradientButton'
import { useAuth } from '../../context/AuthContext'
import api from '../../lib/axios'

const SCHEDULER_ROLES = ['Faculty Coordinator', 'Association President', 'Vice President']

export default function EditMeeting() {
  const { id } = useParams()
  const { user } = useAuth()
  const navigate = useNavigate()

  const [form, setForm] = useState({
    title: '',
    description: '',
    date: '',
    time: '',
    venue: '',
    agenda: '',
    linkedEvent: '',
    status: 'scheduled',
  })
  const [loading, setLoading] = useState(true)
  const [saving, setSaving] = useState(false)
  const [error, setError] = useState(null)

  useEffect(() => {
    if (!SCHEDULER_ROLES.includes(user?.role)) {
      navigate('/meetings')
      return
    }
    (async () => {
      try {
        const { data } = await api.get(`/meetings/${id}`)
        const m = data.meeting
        setForm({
          title: m.title || '',
          description: m.description || '',
          date: m.date ? m.date.slice(0, 10) : '',
          time: m.time || '',
          venue: m.venue || '',
          agenda: m.agenda || '',
          linkedEvent: m.linkedEvent?._id || '',
          status: m.status || 'scheduled',
        })
      } catch (err) {
        setError('Failed to load meeting')
      } finally {
        setLoading(false)
      }
    })()
  }, [id, user, navigate])

  const handleChange = (e) => {
    setForm({ ...form, [e.target.name]: e.target.value })
  }

  const handleSubmit = async (e) => {
    e.preventDefault()
    setSaving(true)
    setError(null)
    try {
      const { data } = await api.put(`/meetings/${id}`, {
        ...form,
        linkedEvent: form.linkedEvent || undefined,
      })
      navigate(`/meetings/${data.meeting._id}`)
    } catch (err) {
      setError(err.response?.data?.message || 'Failed to update meeting')
    } finally {
      setSaving(false)
    }
  }

  if (loading) return <div className="max-w-3xl mx-auto px-6 py-10 text-dark-muted text-sm">Loading...</div>
  if (error) return <div className="max-w-3xl mx-auto px-6 py-10 text-red-400 text-sm">{error}</div>

  return (
    <PageTransition>
      <section className="max-w-3xl mx-auto px-6 py-10">
        <h1 className="text-2xl font-bold text-gradient mb-6">Edit Meeting</h1>

        <form onSubmit={handleSubmit} className="space-y-5">
          <GlassCard>
            <div className="grid grid-cols-1 sm:grid-cols-2 gap-4">
              <div className="sm:col-span-2">
                <label className="block text-xs text-dark-muted mb-1">Title *</label>
                <input name="title" value={form.title} onChange={handleChange} required className="w-full px-4 py-2.5 rounded-2xl bg-warm-card border border-glass-border text-dark-title text-sm focus:outline-none focus:border-blue-accent" />
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
                <label className="block text-xs text-dark-muted mb-1">Venue / Link</label>
                <input name="venue" value={form.venue} onChange={handleChange} className="w-full px-4 py-2.5 rounded-2xl bg-warm-card border border-glass-border text-dark-title text-sm placeholder-dark-subtle focus:outline-none focus:border-blue-accent" />
              </div>
              <div>
                <label className="block text-xs text-dark-muted mb-1">Status</label>
                <select name="status" value={form.status} onChange={handleChange} className="w-full px-4 py-2.5 rounded-2xl bg-warm-card border border-glass-border text-dark-title text-sm focus:outline-none focus:border-blue-accent">
                  <option value="scheduled">Scheduled</option>
                  <option value="ongoing">Ongoing</option>
                  <option value="completed">Completed</option>
                  <option value="cancelled">Cancelled</option>
                </select>
              </div>
              <div>
                <label className="block text-xs text-dark-muted mb-1">Linked Event ID</label>
                <input name="linkedEvent" value={form.linkedEvent} onChange={handleChange} className="w-full px-4 py-2.5 rounded-2xl bg-warm-card border border-glass-border text-dark-title text-sm placeholder-dark-subtle focus:outline-none focus:border-blue-accent" placeholder="Event ObjectId" />
              </div>
              <div className="sm:col-span-2">
                <label className="block text-xs text-dark-muted mb-1">Agenda</label>
                <textarea name="agenda" value={form.agenda} onChange={handleChange} rows={4} className="w-full px-4 py-3 rounded-2xl bg-warm-card border border-glass-border text-dark-title text-sm placeholder-dark-subtle focus:outline-none focus:border-blue-accent resize-y" />
              </div>
            </div>
          </GlassCard>

          <div className="flex gap-3">
            <GradientButton type="submit" disabled={saving} className="text-sm">
              {saving ? 'Saving...' : 'Save Changes'}
            </GradientButton>
            <button type="button" onClick={() => navigate(`/meetings/${id}`)} className="text-sm px-6 py-2.5 rounded-2xl border border-glass-border text-dark-muted hover:text-dark-title transition-colors cursor-pointer bg-transparent">
              Cancel
            </button>
          </div>
        </form>
      </section>
    </PageTransition>
  )
}
