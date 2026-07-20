import { useState, useEffect, useCallback } from 'react'
import PageTransition from '../../components/motion/PageTransition'
import GlassCard from '../../components/ui/GlassCard'
import GradientButton from '../../components/ui/GradientButton'
import api from '../../lib/axios'

const colorStyles = {
  green: { dot: 'bg-green-400', border: 'border-l-4 border-green-400', label: 'text-green-400' },
  yellow: { dot: 'bg-yellow-400', border: 'border-l-4 border-yellow-400', label: 'text-yellow-400' },
  red: { dot: 'bg-red-400', border: 'border-l-4 border-red-400', label: 'text-red-400' },
}

export default function NotificationsPage() {
  const [notifications, setNotifications] = useState([])
  const [unreadCount, setUnreadCount] = useState(0)
  const [loading, setLoading] = useState(true)
  const [error, setError] = useState(null)
  const [filter, setFilter] = useState('all')
  const [page, setPage] = useState(1)
  const [pages, setPages] = useState(1)

  const fetchNotifications = useCallback(async () => {
    setLoading(true)
    setError(null)
    try {
      const params = `?page=${page}&limit=30${filter === 'unread' ? '&unreadOnly=true' : ''}`
      const { data } = await api.get('/notifications' + params)
      setNotifications(data.notifications || [])
      setUnreadCount(data.unreadCount || 0)
      setPages(data.pages || 1)
    } catch (err) {
      setError(err.response?.data?.message || 'Failed to load notifications')
    } finally {
      setLoading(false)
    }
  }, [page, filter])

  useEffect(() => { fetchNotifications() }, [fetchNotifications])

  const handleMarkAllRead = async () => {
    try {
      await api.put('/notifications/read-all')
      setNotifications((prev) => prev.map((n) => ({ ...n, read: true })))
      setUnreadCount(0)
    } catch (_) {}
  }

  const handleMarkRead = async (id) => {
    try {
      await api.put(`/notifications/${id}/read`)
      setNotifications((prev) =>
        prev.map((n) => (n._id === id ? { ...n, read: true } : n))
      )
    } catch (_) {}
  }

  const handleDelete = async (id) => {
    if (!window.confirm('Delete this notification?')) return
    try {
      await api.delete(`/notifications/${id}`)
      setNotifications((prev) => prev.filter((n) => n._id !== id))
    } catch (_) {}
  }

  const tabs = [
    { key: 'all', label: 'All' },
    { key: 'unread', label: `Unread (${unreadCount})` },
  ]

  return (
    <PageTransition>
      <section className="max-w-4xl mx-auto px-6 py-10">
        <div className="flex items-center justify-between mb-8">
          <h1 className="text-2xl font-bold text-gradient">Notifications</h1>
          {unreadCount > 0 && (
            <GradientButton onClick={handleMarkAllRead} className="text-sm">Mark All Read</GradientButton>
          )}
        </div>

        <div className="flex gap-2 mb-6 flex-wrap">
          {tabs.map((t) => (
            <button key={t.key} onClick={() => { setFilter(t.key); setPage(1) }}
              className={`text-xs px-3 py-1.5 rounded-xl border transition-colors cursor-pointer bg-transparent ${filter === t.key ? 'border-orange-accent text-orange-accent' : 'border-glass-border text-dark-muted hover:text-dark-title'}`}>
              {t.label}
            </button>
          ))}
        </div>

        {loading && <p className="text-dark-muted text-sm">Loading...</p>}
        {error && <p className="text-red-400 text-sm">{error}</p>}
        {!loading && !error && notifications.length === 0 && (
          <p className="text-dark-subtle text-sm">No notifications found.</p>
        )}

        <div className="space-y-3">
          {notifications.map((n) => {
            const cs = colorStyles[n.color] || colorStyles.green
            return (
              <GlassCard
                key={n._id}
                className={`${cs.border} ${n.read ? 'opacity-60' : ''} transition-all`}
              >
                <div className="flex items-start gap-3">
                  <span className={`w-3 h-3 rounded-full mt-1 shrink-0 ${cs.dot}`} />
                  <div className="min-w-0 flex-1">
                    <div className="flex items-center gap-2 mb-0.5">
                      <span className={`text-[10px] font-semibold ${cs.label}`}>
                        {n.color.toUpperCase()}
                      </span>
                      <h3 className="text-sm font-semibold text-dark-title">{n.title}</h3>
                    </div>
                    {n.message && <p className="text-xs text-dark-muted mt-1">{n.message}</p>}
                    <div className="flex flex-wrap items-center gap-2 mt-2 text-[10px] text-dark-subtle">
                      <span>{new Date(n.createdAt).toLocaleDateString()} {new Date(n.createdAt).toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' })}</span>
                      <span className={`text-[10px] font-semibold px-1.5 py-0.5 rounded border ${cs.label} ${cs.border.split(' ')[1]}`}>{n.type.replace(/_/g, ' ')}</span>
                    </div>
                  </div>
                  <div className="flex gap-1 shrink-0">
                    {!n.read && (
                      <button onClick={() => handleMarkRead(n._id)}
                        className="text-[10px] px-2 py-1 rounded-lg border border-blue-accent/30 text-blue-accent hover:bg-blue-accent/10 transition-colors bg-transparent cursor-pointer">Read</button>
                    )}
                    <button onClick={() => handleDelete(n._id)}
                      className="text-[10px] px-2 py-1 rounded-lg border border-red-400/30 text-red-400 hover:bg-red-400/10 transition-colors bg-transparent cursor-pointer">Del</button>
                  </div>
                </div>
              </GlassCard>
            )
          })}
        </div>

        {pages > 1 && (
          <div className="flex justify-center gap-2 mt-8">
            <button onClick={() => setPage((p) => Math.max(1, p - 1))} disabled={page <= 1}
              className="text-xs px-3 py-1.5 rounded-xl border border-glass-border text-dark-muted hover:text-dark-title disabled:opacity-40 transition-colors bg-transparent cursor-pointer">Prev</button>
            <span className="text-xs text-dark-subtle self-center">Page {page} of {pages}</span>
            <button onClick={() => setPage((p) => Math.min(pages, p + 1))} disabled={page >= pages}
              className="text-xs px-3 py-1.5 rounded-xl border border-glass-border text-dark-muted hover:text-dark-title disabled:opacity-40 transition-colors bg-transparent cursor-pointer">Next</button>
          </div>
        )}
      </section>
    </PageTransition>
  )
}
