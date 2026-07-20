import { useState, useEffect } from 'react'
import { useNavigate } from 'react-router-dom'
import PageTransition from '../../components/motion/PageTransition'
import GlassCard from '../../components/ui/GlassCard'
import GradientButton from '../../components/ui/GradientButton'
import { useAuth } from '../../context/AuthContext'
import api from '../../lib/axios'

const SCHEDULER_ROLES = ['Faculty Coordinator', 'Association President', 'Vice President']

export default function CreateMeeting() {
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
  })
  const [users, setUsers] = useState([])
  const [selectedInvitees, setSelectedInvitees] = useState([])
  const [loading, setLoading] = useState(false)
  const [error, setError] = useState(null)

  useEffect(() => {
    if (!SCHEDULER_ROLES.includes(user?.role)) {
      navigate('/meetings')
      return
    }
    // Load users for invitee selection
    api.get('/users').then(({ data }) => {
      setUsers(data.users || [])
    }).catch(() => {})
  }, [user, navigate])

  const handleChange = (e) => {
    setForm({ ...form, [e.target.name]: e.target.value })
  }

  const toggleInvitee = (userId) => {
    setSelectedInvitees((prev) =>
      prev.includes(userId) ? prev.filter((id) => id !== userId) : [...prev, userId]
    )
  }

  const handleSubmit = async (e) => {
    e.preventDefault()
    setLoading(true)
    setError(null)
    try {
      const { data } = await api.post('/meetings', {
        ...form,
        linkedEvent: form.linkedEvent || undefined,
        invitees: selectedInvitees,
      })
      navigate(`/meetings/${data.meeting._id}`)
    } catch (err) {
      setError(err.response?.data?.message || 'Failed to create meeting')
    } finally {
      setLoading(false)
    }
  }

  return (
    <PageTransition>
      <section className="max-w-3xl mx-auto px-6 py-10">
        <h1 className="text-2xl font-bold text-gradient mb-6">Schedule Meeting</h1>

        {error && <p className="text-red-400 text-sm mb-4">{error}</p>}

        <form onSubmit={handleSubmit} className="space-y-5">
          <GlassCard>
            <div className="grid grid-cols-1 sm:grid-cols-2 gap-4">
              <div className="sm:col-span-2">
                <label className="block text-xs text-dark-muted mb-1">Title *</label>
                <input name="title" value={form.title} onChange={handleChange} required className="w-full px-4 py-2.5 rounded-2xl bg-warm-card border border-glass-border text-dark-title text-sm placeholder-dark-subtle focus:outline-none focus:border-blue-accent" />
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
                <input name="venue" value={form.venue} onChange={handleChange} className="w-full px-4 py-2.5 rounded-2xl bg-warm-card border border-glass-border text-dark-title text-sm placeholder-dark-subtle focus:outline-none focus:border-blue-accent" placeholder="Room 301 or Zoom link" />
              </div>
              <div>
                <label className="block text-xs text-dark-muted mb-1">Linked Event ID (optional)</label>
                <input name="linkedEvent" value={form.linkedEvent} onChange={handleChange} className="w-full px-4 py-2.5 rounded-2xl bg-warm-card border border-glass-border text-dark-title text-sm placeholder-dark-subtle focus:outline-none focus:border-blue-accent" placeholder="Event ObjectId" />
              </div>
              <div className="sm:col-span-2">
                <label className="block text-xs text-dark-muted mb-1">Agenda</label>
                <textarea name="agenda" value={form.agenda} onChange={handleChange} rows={4} className="w-full px-4 py-3 rounded-2xl bg-warm-card border border-glass-border text-dark-title text-sm placeholder-dark-subtle focus:outline-none focus:border-blue-accent resize-y" />
              </div>
            </div>
          </GlassCard>

          {/* Invitees */}
          <GlassCard>
            <label className="block text-xs font-semibold text-dark-title mb-3">Invite Committee Members & Volunteers</label>
            <div className="max-h-48 overflow-y-auto space-y-1.5">
              {users.filter((u) => ['Committee Member', 'Student Member'].includes(u.role)).map((u) => (
                <label key={u._id} className="flex items-center gap-2 text-xs text-dark-body cursor-pointer">
                  <input
                    type="checkbox"
                    checked={selectedInvitees.includes(u._id)}
                    onChange={() => toggleInvitee(u._id)}
                    className="accent-blue-accent"
                  />
                  {u.name} <span className="text-dark-subtle">({u.role})</span>
                </label>
              ))}
              {users.filter((u) => ['Committee Member', 'Student Member'].includes(u.role)).length === 0 && (
                <p className="text-xs text-dark-subtle">No members available</p>
              )}
            </div>
          </GlassCard>

          <div className="flex gap-3">
            <GradientButton type="submit" disabled={loading} className="text-sm">
              {loading ? 'Creating...' : 'Create Meeting'}
            </GradientButton>
            <button type="button" onClick={() => navigate('/meetings')} className="text-sm px-6 py-2.5 rounded-2xl border border-glass-border text-dark-muted hover:text-dark-title transition-colors cursor-pointer bg-transparent">
              Cancel
            </button>
          </div>
        </form>
      </section>
    </PageTransition>
  )
}
