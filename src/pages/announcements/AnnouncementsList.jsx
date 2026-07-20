import { useState, useEffect } from 'react'
import { Link, useNavigate } from 'react-router-dom'
import PageTransition from '../../components/motion/PageTransition'
import GlassCard from '../../components/ui/GlassCard'
import GradientButton from '../../components/ui/GradientButton'
import { useAuth } from '../../context/AuthContext'
import api from '../../lib/axios'

const POSTER_ROLES = ['Administrator', 'Association President', 'Faculty Coordinator']

export default function AnnouncementsList() {
  const { user } = useAuth()
  const navigate = useNavigate()
  const [announcements, setAnnouncements] = useState([])
  const [loading, setLoading] = useState(true)
  const [error, setError] = useState(null)

  const canPost = POSTER_ROLES.includes(user?.role)

  useEffect(() => {
    (async () => {
      setLoading(true)
      setError(null)
      try {
        const { data } = await api.get('/announcements')
        setAnnouncements(data.announcements || [])
      } catch (err) {
        setError(err.response?.data?.message || 'Failed to load announcements')
      } finally {
        setLoading(false)
      }
    })()
  }, [])

  const handleDelete = async (id) => {
    if (!window.confirm('Delete this announcement?')) return
    try {
      await api.delete(`/announcements/${id}`)
      setAnnouncements((prev) => prev.filter((a) => a._id !== id))
    } catch (err) {
      alert(err.response?.data?.message || 'Delete failed')
    }
  }

  return (
    <PageTransition>
      <section className="max-w-4xl mx-auto px-6 py-10">
        <div className="flex items-center justify-between mb-8">
          <h1 className="text-2xl font-bold text-gradient">Notice Board</h1>
          {canPost && (
            <Link to="/announcements/create">
              <GradientButton className="text-sm">+ New Notice</GradientButton>
            </Link>
          )}
        </div>

        {loading && <p className="text-dark-muted text-sm">Loading...</p>}
        {error && <p className="text-red-400 text-sm">{error}</p>}
        {!loading && !error && announcements.length === 0 && (
          <p className="text-dark-subtle text-sm">No announcements yet.</p>
        )}

        <div className="space-y-4">
          {announcements.map((a) => (
            <GlassCard
              key={a._id}
              glow={a.pinned}
              className={`${a.pinned ? 'border-blue-accent/40' : ''} ${!canPost ? '' : 'hover:bg-glass-hover'} transition-colors`}
            >
              <div className="flex items-start justify-between gap-3">
                <div className="min-w-0 flex-1">
                  <div className="flex items-center gap-2 mb-1 flex-wrap">
                    {a.pinned && (
                      <span className="text-[10px] font-semibold text-blue-accent border border-blue-accent/30 px-1.5 py-0.5 rounded">PINNED</span>
                    )}
                    <h3 className="text-dark-title font-semibold text-sm">{a.title}</h3>
                  </div>
                  {a.body && <p className="text-xs text-dark-muted mt-1 whitespace-pre-wrap">{a.body}</p>}
                  <div className="flex flex-wrap gap-3 mt-2 text-[10px] text-dark-subtle">
                    <span>By {a.createdBy?.name || 'Unknown'} ({a.createdBy?.role || ''})</span>
                    <span>{new Date(a.createdAt).toLocaleDateString()}</span>
                    {a.event && <Link to={`/events/${a.event._id}`} className="text-blue-accent no-underline">Event: {a.event.name}</Link>}
                    {a.expiryDate && <span>Expires: {new Date(a.expiryDate).toLocaleDateString()}</span>}
                    {a.attachment?.filename && <span>Attached: {a.attachment.filename}</span>}
                  </div>
                </div>
                {canPost && (
                  <div className="flex gap-1 shrink-0">
                    <button onClick={() => navigate(`/announcements/${a._id}/edit`)} className="text-xs px-2 py-1 rounded-lg border border-glass-border text-dark-muted hover:text-dark-title transition-colors cursor-pointer bg-transparent">Edit</button>
                    <button onClick={() => handleDelete(a._id)} className="text-xs px-2 py-1 rounded-lg border border-red-400/30 text-red-400 hover:bg-red-400/10 transition-colors cursor-pointer bg-transparent">Del</button>
                  </div>
                )}
              </div>
            </GlassCard>
          ))}
        </div>
      </section>
    </PageTransition>
  )
}
