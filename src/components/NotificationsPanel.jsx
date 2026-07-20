import { useState, useEffect, useRef } from 'react'
import { Link } from 'react-router-dom'
import api from '../lib/axios'

const colorClasses = {
  green: 'border-l-4 border-green-400',
  yellow: 'border-l-4 border-yellow-400',
  red: 'border-l-4 border-red-400',
}

const dotColors = {
  green: 'bg-green-400',
  yellow: 'bg-yellow-400',
  red: 'bg-red-400',
}

export default function NotificationsPanel({ open, onClose }) {
  const [notifications, setNotifications] = useState([])
  const [unreadCount, setUnreadCount] = useState(0)
  const panelRef = useRef(null)

  useEffect(() => {
    if (!open) return
    ;(async () => {
      try {
        const { data } = await api.get('/notifications?limit=10')
        setNotifications(data.notifications || [])
        setUnreadCount(data.unreadCount || 0)
      } catch (_) {}
    })()
  }, [open])

  useEffect(() => {
    const handleClick = (e) => {
      if (panelRef.current && !panelRef.current.contains(e.target)) onClose()
    }
    if (open) {
      document.addEventListener('mousedown', handleClick)
      return () => document.removeEventListener('mousedown', handleClick)
    }
  }, [open, onClose])

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
      setUnreadCount((c) => Math.max(0, c - 1))
    } catch (_) {}
  }

  if (!open) return null

  return (
    <div
      ref={panelRef}
      className="absolute right-0 top-full mt-2 w-80 sm:w-96 glass p-0 shadow-soft rounded-xl overflow-hidden z-50"
    >
      <div className="flex items-center justify-between px-4 py-3 border-b border-glass-border">
        <h3 className="text-sm font-semibold text-dark-title">Notifications</h3>
        <div className="flex gap-2">
          {unreadCount > 0 && (
            <button onClick={handleMarkAllRead}
              className="text-[10px] text-blue-accent hover:text-blue-glow transition-colors bg-transparent border-0 cursor-pointer">Mark all read</button>
          )}
          <Link to="/notifications" onClick={onClose}
            className="text-[10px] text-dark-muted hover:text-dark-title no-underline">View all</Link>
        </div>
      </div>

      <div className="max-h-80 overflow-y-auto">
        {notifications.length === 0 && (
          <p className="text-xs text-dark-subtle text-center py-8">No notifications yet.</p>
        )}
        {notifications.map((n) => (
          <div
            key={n._id}
            onClick={() => !n.read && handleMarkRead(n._id)}
            className={`px-4 py-3 border-b border-glass-border last:border-b-0 hover:bg-glass-hover transition-colors cursor-pointer ${colorClasses[n.color] || ''} ${n.read ? 'opacity-60' : ''}`}
          >
            <div className="flex items-start gap-2">
              <span className={`w-2 h-2 rounded-full mt-1 shrink-0 ${dotColors[n.color] || 'bg-gray-500'}`} />
              <div className="min-w-0 flex-1">
                <p className="text-xs font-medium text-dark-title truncate">{n.title}</p>
                {n.message && <p className="text-[10px] text-dark-muted mt-0.5 line-clamp-2">{n.message}</p>}
                <p className="text-[9px] text-gray-600 mt-1">{new Date(n.createdAt).toLocaleDateString()} {new Date(n.createdAt).toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' })}</p>
              </div>
            </div>
          </div>
        ))}
      </div>
    </div>
  )
}
