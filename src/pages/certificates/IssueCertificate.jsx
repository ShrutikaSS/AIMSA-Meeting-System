import { useState, useEffect } from 'react'
import PageTransition from '../../components/motion/PageTransition'
import GlassCard from '../../components/ui/GlassCard'
import GradientButton from '../../components/ui/GradientButton'
import api from '../../lib/axios'

const CERT_TYPES = ['Participation', 'Volunteer', 'Winner', 'Appreciation']

export default function IssueCertificate() {
  const [users, setUsers] = useState([])
  const [events, setEvents] = useState([])
  const [loading, setLoading] = useState(true)
  const [form, setForm] = useState({ userId: '', eventId: '', type: '', title: '', description: '' })
  const [submitting, setSubmitting] = useState(false)
  const [message, setMessage] = useState(null)
  const [mode, setMode] = useState('single')

  useEffect(() => {
    (async () => {
      try {
        const [uRes, eRes] = await Promise.all([
          api.get('/users'),
          api.get('/events?limit=100'),
        ])
        setUsers(uRes.data.users || [])
        setEvents(eRes.data.events || [])
      } catch (err) {
        console.error(err)
      } finally {
        setLoading(false)
      }
    })()
  }, [])

  const handleSubmit = async (e) => {
    e.preventDefault()
    if (!form.userId || !form.type || !form.title) return
    setSubmitting(true)
    setMessage(null)
    try {
      const payload = {
        userId: mode === 'single' ? form.userId : undefined,
        eventId: form.eventId || undefined,
        type: form.type,
        title: form.title,
        description: form.description,
      }
      const { data } = form.eventId && mode === 'bulk'
        ? await api.post('/certificates/bulk', { ...payload, userIds: [form.userId] })
        : await api.post('/certificates', payload)
      setMessage({ type: 'success', text: `Certificate issued successfully! No: ${data.certificate?.certificateNumber || ''}` })
      setForm({ userId: '', eventId: '', type: '', title: '', description: '' })
    } catch (err) {
      setMessage({ type: 'error', text: err.response?.data?.message || 'Failed to issue certificate' })
    } finally {
      setSubmitting(false)
    }
  }

  return (
    <PageTransition>
      <section className="max-w-3xl mx-auto px-6 py-10">
        <h1 className="text-2xl font-bold text-gradient mb-8">Issue Certificate</h1>

        {message && (
          <div className={`mb-4 text-xs px-4 py-2 rounded-xl ${message.type === 'success' ? 'bg-green-400/10 text-green-400 border border-green-400/20' : 'bg-red-400/10 text-red-400 border border-red-400/20'}`}>
            {message.text}
          </div>
        )}

        <GlassCard>
          <form onSubmit={handleSubmit} className="space-y-4">
            <div className="flex gap-2 mb-2">
              {['single', 'bulk'].map((m) => (
                <button key={m} type="button" onClick={() => setMode(m)}
                  className={`text-xs px-3 py-1.5 rounded-xl border transition-colors cursor-pointer bg-transparent ${mode === m ? 'border-orange-accent text-orange-accent' : 'border-glass-border text-dark-muted hover:text-dark-title'}`}>
                  {m === 'single' ? 'Single Issue' : 'Bulk Issue'}
                </button>
              ))}
            </div>

            <div>
              <label className="block text-xs text-dark-muted mb-1">Recipient *</label>
              <select value={form.userId} onChange={(e) => setForm({ ...form, userId: e.target.value })}
                className="w-full bg-warm-card border border-glass-border rounded-xl px-3 py-2 text-sm text-dark-title outline-none focus:border-orange-accent/50 transition-colors" required>
                <option value="">Select user</option>
                {users.map((u) => <option key={u._id} value={u._id}>{u.name} ({u.email})</option>)}
              </select>
            </div>

            <div className="grid grid-cols-1 sm:grid-cols-2 gap-4">
              <div>
                <label className="block text-xs text-dark-muted mb-1">Certificate Type *</label>
                <select value={form.type} onChange={(e) => setForm({ ...form, type: e.target.value })}
                  className="w-full bg-warm-card border border-glass-border rounded-xl px-3 py-2 text-sm text-dark-title outline-none focus:border-orange-accent/50 transition-colors" required>
                  <option value="">Select type</option>
                  {CERT_TYPES.map((t) => <option key={t} value={t}>{t}</option>)}
                </select>
              </div>
              <div>
                <label className="block text-xs text-dark-muted mb-1">Linked Event (optional)</label>
                <select value={form.eventId} onChange={(e) => setForm({ ...form, eventId: e.target.value })}
                  className="w-full bg-warm-card border border-glass-border rounded-xl px-3 py-2 text-sm text-dark-title outline-none focus:border-orange-accent/50 transition-colors">
                  <option value="">None</option>
                  {events.map((e) => <option key={e._id} value={e._id}>{e.name} - {new Date(e.date).toLocaleDateString()}</option>)}
                </select>
              </div>
            </div>

            <div>
              <label className="block text-xs text-dark-muted mb-1">Certificate Title *</label>
              <input type="text" value={form.title} onChange={(e) => setForm({ ...form, title: e.target.value })}
                className="w-full bg-warm-card border border-glass-border rounded-xl px-3 py-2 text-sm text-dark-title outline-none focus:border-orange-accent/50 transition-colors"
                placeholder="e.g. Hackathon Winner" required />
            </div>

            <div>
              <label className="block text-xs text-dark-muted mb-1">Description (optional)</label>
              <textarea value={form.description} onChange={(e) => setForm({ ...form, description: e.target.value })}
                className="w-full bg-warm-card border border-glass-border rounded-xl px-3 py-2 text-sm text-dark-title outline-none focus:border-orange-accent/50 transition-colors" rows={2} />
            </div>

            <GradientButton type="submit" disabled={submitting} className="text-sm">
              {submitting ? 'Issuing...' : 'Issue Certificate'}
            </GradientButton>
          </form>
        </GlassCard>
      </section>
    </PageTransition>
  )
}
