import { useState, useEffect } from 'react'
import PageTransition from '../../components/motion/PageTransition'
import GlassCard from '../../components/ui/GlassCard'
import GradientButton from '../../components/ui/GradientButton'
import api from '../../lib/axios'

const CATEGORIES = ['Competition', 'Internship', 'Research Publication', 'Hackathon Win', 'Other']

export default function AchievementReview() {
  const [achievements, setAchievements] = useState([])
  const [loading, setLoading] = useState(true)
  const [error, setError] = useState(null)
  const [statusFilter, setStatusFilter] = useState('pending')
  const [notes, setNotes] = useState({})

  const fetchAchievements = async () => {
    setLoading(true)
    setError(null)
    try {
      const { data } = await api.get(`/achievements?status=${statusFilter}`)
      setAchievements(data.achievements || [])
    } catch (err) {
      setError(err.response?.data?.message || 'Failed to load')
    } finally {
      setLoading(false)
    }
  }

  useEffect(() => { fetchAchievements() }, [statusFilter])

  const handleApprove = async (id) => {
    try {
      await api.put(`/achievements/${id}/approve`, { reviewNotes: notes[id] || '' })
      setAchievements((prev) => prev.filter((a) => a._id !== id))
    } catch (err) {
      alert(err.response?.data?.message || 'Failed')
    }
  }

  const handleReject = async (id) => {
    try {
      await api.put(`/achievements/${id}/reject`, { reviewNotes: notes[id] || '' })
      setAchievements((prev) => prev.filter((a) => a._id !== id))
    } catch (err) {
      alert(err.response?.data?.message || 'Failed')
    }
  }

  const tabs = ['pending', 'approved', 'rejected']

  return (
    <PageTransition>
      <section className="max-w-5xl mx-auto px-6 py-10">
        <h1 className="text-2xl font-bold text-gradient mb-8">Review Achievements</h1>

        <div className="flex gap-2 mb-6 flex-wrap">
          {tabs.map((t) => (
            <button key={t} onClick={() => setStatusFilter(t)}
              className={`text-xs px-3 py-1.5 rounded-xl border transition-colors cursor-pointer bg-transparent ${statusFilter === t ? 'border-orange-accent text-orange-accent' : 'border-glass-border text-dark-muted hover:text-dark-title'}`}>
              {t.charAt(0).toUpperCase() + t.slice(1)}
            </button>
          ))}
        </div>

        {loading && <p className="text-dark-muted text-sm">Loading...</p>}
        {error && <p className="text-red-400 text-sm">{error}</p>}
        {!loading && !error && achievements.length === 0 && (
          <p className="text-dark-subtle text-sm">No {statusFilter} achievements.</p>
        )}

        <div className="space-y-4">
          {achievements.map((a) => (
            <GlassCard key={a._id}>
              <div className="flex items-start justify-between gap-3">
                <div className="min-w-0 flex-1">
                  <div className="flex items-center gap-2 mb-1 flex-wrap">
                    <span className={`text-[10px] font-semibold border px-1.5 py-0.5 rounded ${a.status === 'pending' ? 'text-yellow-400 border-yellow-400/30' : a.status === 'approved' ? 'text-green-400 border-green-400/30' : 'text-red-400 border-red-400/30'}`}>{a.status.toUpperCase()}</span>
                    <h3 className="text-dark-title font-semibold text-sm">{a.title}</h3>
                  </div>
                  <p className="text-xs text-dark-muted mt-1">{a.description}</p>
                  <div className="flex flex-wrap gap-3 mt-2 text-[10px] text-dark-subtle">
                    <span>{a.category}</span>
                    <span>{new Date(a.date).toLocaleDateString()}</span>
                    <span>By {a.user?.name} ({a.user?.email})</span>
                    {a.fileUrl && <a href={`http://localhost:5000${a.fileUrl}`} target="_blank" rel="noreferrer" className="text-blue-accent no-underline">View File</a>}
                  </div>

                  {a.status === 'pending' && (
                    <div className="mt-3 flex flex-wrap items-center gap-2">
                      <input type="text" placeholder="Review notes (optional)"
                        value={notes[a._id] || ''} onChange={(e) => setNotes({ ...notes, [a._id]: e.target.value })}
                        className="flex-1 min-w-[200px] bg-warm-card border border-glass-border rounded-xl px-3 py-1.5 text-xs text-dark-title outline-none focus:border-orange-accent/50 transition-colors" />
                      <GradientButton onClick={() => handleApprove(a._id)} className="text-[11px] px-3 py-1">Approve</GradientButton>
                      <button onClick={() => handleReject(a._id)}
                        className="text-[11px] px-3 py-1 rounded-xl border border-red-400/30 text-red-400 hover:bg-red-400/10 transition-colors cursor-pointer bg-transparent">Reject</button>
                    </div>
                  )}
                  {a.reviewNotes && <p className="text-[10px] text-dark-subtle mt-1">Notes: {a.reviewNotes}</p>}
                  {a.reviewedBy && <p className="text-[10px] text-gray-600 mt-1">Reviewed by: {a.reviewedBy.name}</p>}
                </div>
              </div>
            </GlassCard>
          ))}
        </div>
      </section>
    </PageTransition>
  )
}
