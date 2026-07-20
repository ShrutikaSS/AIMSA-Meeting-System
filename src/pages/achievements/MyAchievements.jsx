import { useState, useEffect } from 'react'
import PageTransition from '../../components/motion/PageTransition'
import GlassCard from '../../components/ui/GlassCard'
import GradientButton from '../../components/ui/GradientButton'
import api from '../../lib/axios'

const CATEGORIES = ['Competition', 'Internship', 'Research Publication', 'Hackathon Win', 'Other']

export default function MyAchievements() {
  const [achievements, setAchievements] = useState([])
  const [loading, setLoading] = useState(true)
  const [error, setError] = useState(null)
  const [showForm, setShowForm] = useState(false)
  const [form, setForm] = useState({ title: '', category: '', date: '', description: '' })
  const [file, setFile] = useState(null)
  const [submitting, setSubmitting] = useState(false)

  const fetchAchievements = async () => {
    setLoading(true)
    setError(null)
    try {
      const { data } = await api.get('/achievements')
      setAchievements(data.achievements || [])
    } catch (err) {
      setError(err.response?.data?.message || 'Failed to load achievements')
    } finally {
      setLoading(false)
    }
  }

  useEffect(() => { fetchAchievements() }, [])

  const handleSubmit = async (e) => {
    e.preventDefault()
    if (!form.title || !form.category || !form.date) return
    setSubmitting(true)
    try {
      const fd = new FormData()
      fd.append('title', form.title)
      fd.append('category', form.category)
      fd.append('date', form.date)
      fd.append('description', form.description)
      if (file) fd.append('file', file)
      await api.post('/achievements', fd, { headers: { 'Content-Type': 'multipart/form-data' } })
      setForm({ title: '', category: '', date: '', description: '' })
      setFile(null)
      setShowForm(false)
      fetchAchievements()
    } catch (err) {
      alert(err.response?.data?.message || 'Upload failed')
    } finally {
      setSubmitting(false)
    }
  }

  const handleDelete = async (id) => {
    if (!window.confirm('Delete this achievement?')) return
    try {
      await api.delete(`/achievements/${id}`)
      setAchievements((prev) => prev.filter((a) => a._id !== id))
    } catch (err) {
      alert(err.response?.data?.message || 'Delete failed')
    }
  }

  const statusBadge = (status) => {
    const colors = { pending: 'text-yellow-400 border-yellow-400/30', approved: 'text-green-400 border-green-400/30', rejected: 'text-red-400 border-red-400/30' }
    return <span className={`text-[10px] font-semibold border px-1.5 py-0.5 rounded ${colors[status] || ''}`}>{status.toUpperCase()}</span>
  }

  return (
    <PageTransition>
      <section className="max-w-4xl mx-auto px-6 py-10">
        <div className="flex items-center justify-between mb-8">
          <h1 className="text-2xl font-bold text-gradient">My Achievements</h1>
          <GradientButton onClick={() => setShowForm(!showForm)} className="text-sm">
            {showForm ? 'Cancel' : '+ Add Achievement'}
          </GradientButton>
        </div>

        {showForm && (
          <GlassCard className="mb-8">
            <form onSubmit={handleSubmit} className="space-y-4">
              <div>
                <label className="block text-xs text-dark-muted mb-1">Title *</label>
                <input type="text" value={form.title} onChange={(e) => setForm({ ...form, title: e.target.value })}
                  className="w-full bg-warm-card border border-glass-border rounded-xl px-3 py-2 text-sm text-dark-title outline-none focus:border-orange-accent/50 transition-colors" required />
              </div>
              <div className="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                  <label className="block text-xs text-dark-muted mb-1">Category *</label>
                  <select value={form.category} onChange={(e) => setForm({ ...form, category: e.target.value })}
                    className="w-full bg-warm-card border border-glass-border rounded-xl px-3 py-2 text-sm text-dark-title outline-none focus:border-orange-accent/50 transition-colors" required>
                    <option value="">Select</option>
                    {CATEGORIES.map((c) => <option key={c} value={c}>{c}</option>)}
                  </select>
                </div>
                <div>
                  <label className="block text-xs text-dark-muted mb-1">Date *</label>
                  <input type="date" value={form.date} onChange={(e) => setForm({ ...form, date: e.target.value })}
                    className="w-full bg-warm-card border border-glass-border rounded-xl px-3 py-2 text-sm text-dark-title outline-none focus:border-orange-accent/50 transition-colors" required />
                </div>
              </div>
              <div>
                <label className="block text-xs text-dark-muted mb-1">Description</label>
                <textarea value={form.description} onChange={(e) => setForm({ ...form, description: e.target.value })}
                  className="w-full bg-warm-card border border-glass-border rounded-xl px-3 py-2 text-sm text-dark-title outline-none focus:border-orange-accent/50 transition-colors" rows={3} />
              </div>
              <div>
                <label className="block text-xs text-dark-muted mb-1">Certificate File (optional)</label>
                <input type="file" accept="image/*,.pdf" onChange={(e) => setFile(e.target.files[0])}
                  className="w-full text-sm text-dark-muted file:mr-3 file:py-1.5 file:px-3 file:rounded-xl file:border-0 file:text-xs file:font-semibold file:bg-orange-accent file:text-white file:cursor-pointer" />
              </div>
              <GradientButton type="submit" disabled={submitting} className="text-sm">
                {submitting ? 'Submitting...' : 'Submit Achievement'}
              </GradientButton>
            </form>
          </GlassCard>
        )}

        {loading && <p className="text-dark-muted text-sm">Loading...</p>}
        {error && <p className="text-red-400 text-sm">{error}</p>}
        {!loading && !error && achievements.length === 0 && (
          <p className="text-dark-subtle text-sm">No achievements yet. Add one above!</p>
        )}

        <div className="space-y-4">
          {achievements.map((a) => (
            <GlassCard key={a._id}>
              <div className="flex items-start justify-between gap-3">
                <div className="min-w-0 flex-1">
                  <div className="flex items-center gap-2 mb-1 flex-wrap">
                    {statusBadge(a.status)}
                    <h3 className="text-dark-title font-semibold text-sm">{a.title}</h3>
                  </div>
                  <p className="text-xs text-dark-muted mt-1">{a.description}</p>
                  <div className="flex flex-wrap gap-3 mt-2 text-[10px] text-dark-subtle">
                    <span>{a.category}</span>
                    <span>{new Date(a.date).toLocaleDateString()}</span>
                    {a.fileUrl && <a href={`http://localhost:5000${a.fileUrl}`} target="_blank" rel="noreferrer" className="text-blue-accent no-underline">View File</a>}
                    {a.status === 'rejected' && a.reviewedBy && <span>Reviewer: {a.reviewedBy.name}</span>}
                    {a.reviewNotes && <span className="text-red-400">Notes: {a.reviewNotes}</span>}
                  </div>
                </div>
                <button onClick={() => handleDelete(a._id)}
                  className="text-xs px-2 py-1 rounded-lg border border-red-400/30 text-red-400 hover:bg-red-400/10 transition-colors cursor-pointer bg-transparent shrink-0">Del</button>
              </div>
            </GlassCard>
          ))}
        </div>
      </section>
    </PageTransition>
  )
}
