import { useState, useEffect, useRef, useCallback } from 'react'
import { Link } from 'react-router-dom'
import PageTransition from '../../components/motion/PageTransition'
import GlassCard from '../../components/ui/GlassCard'
import GradientButton from '../../components/ui/GradientButton'
import { useAuth } from '../../context/AuthContext'
import api from '../../lib/axios'

const MANAGER_ROLES = ['Faculty Coordinator', 'Association President', 'Vice President']
const statusColors = {
  draft: 'text-dark-muted border-gray-500/30 bg-gray-500/10',
  published: 'text-green-400 border-green-400/30 bg-green-400/10',
  cancelled: 'text-red-400 border-red-400/30 bg-red-400/10',
  completed: 'text-blue-accent border-blue-accent/30 bg-blue-accent/10',
}

export default function EventsList() {
  const { user } = useAuth()
  const [events, setEvents] = useState([])
  const [loading, setLoading] = useState(true)
  const [statusFilter, setStatusFilter] = useState('')
  const [searchQuery, setSearchQuery] = useState('')
  const [sortBy, setSortBy] = useState('date')
  const [sortOrder, setSortOrder] = useState('desc')
  const [error, setError] = useState(null)
  const debounceRef = useRef(null)

  const canManage = MANAGER_ROLES.includes(user?.role) || user?.role === 'Administrator'
  const isStudent = user?.role === 'Student Member'
  const isCommitteeMember = user?.role === 'Committee Member'

  const fetchEvents = useCallback(async () => {
    setLoading(true)
    setError(null)
    try {
      const params = { sortBy, sortOrder }
      if (statusFilter) params.status = statusFilter
      if (searchQuery) params.search = searchQuery
      if (isStudent || isCommitteeMember) params.status = 'published'
      const { data } = await api.get('/events', { params })
      setEvents(data.events || [])
    } catch (err) {
      setError(err.response?.data?.message || 'Failed to load events')
    } finally {
      setLoading(false)
    }
  }, [statusFilter, searchQuery, sortBy, sortOrder, isStudent, isCommitteeMember])

  useEffect(() => { fetchEvents() }, [fetchEvents])

  useEffect(() => {
    if (debounceRef.current) clearTimeout(debounceRef.current)
    debounceRef.current = setTimeout(fetchEvents, 300)
    return () => { if (debounceRef.current) clearTimeout(debounceRef.current) }
  }, [searchQuery])

  const filters = canManage ? ['', 'draft', 'published', 'completed', 'cancelled'] : []

  return (
    <PageTransition>
      <section className="max-w-7xl mx-auto px-6 py-10">
        <div className="flex items-center justify-between mb-8 flex-wrap gap-3">
          <h1 className="text-2xl font-bold text-gradient">Events</h1>
          <div className="flex gap-3">
            {(isStudent || isCommitteeMember) && (
              <Link to="/registrations/my">
                <button className="text-xs px-4 py-2 rounded-2xl border border-glass-border text-dark-muted hover:text-dark-title transition-colors cursor-pointer bg-transparent">My Registrations</button>
              </Link>
            )}
            {canManage && (
              <Link to="/events/create">
                <GradientButton className="text-sm">+ Create Event</GradientButton>
              </Link>
            )}
          </div>
        </div>

        <div className="flex flex-wrap items-center gap-3 mb-6">
          <div className="relative flex-1 min-w-[200px] max-w-xs">
            <svg className="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-dark-subtle" fill="none" stroke="currentColor" strokeWidth="2" viewBox="0 0 24 24"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.35-4.35"/></svg>
            <input type="text" value={searchQuery} onChange={(e) => setSearchQuery(e.target.value)}
              placeholder="Search events..." className="w-full pl-9 pr-3 py-2 rounded-xl bg-warm-card border border-glass-border text-sm text-dark-title outline-none focus:border-orange-accent/50 transition-colors" />
          </div>
          <select value={sortBy} onChange={(e) => setSortBy(e.target.value)}
            className="bg-warm-card border border-glass-border rounded-xl px-3 py-2 text-xs text-dark-title outline-none focus:border-orange-accent/50">
            <option value="date">Date</option>
            <option value="name">Name</option>
            <option value="createdAt">Created</option>
          </select>
          <button onClick={() => setSortOrder(sortOrder === 'desc' ? 'asc' : 'desc')}
            className="text-xs px-3 py-2 rounded-xl border border-glass-border text-dark-muted hover:text-dark-title bg-transparent cursor-pointer">
            {sortOrder === 'desc' ? '↓ Newest' : '↑ Oldest'}
          </button>
        </div>

        {canManage && (
          <div className="flex gap-2 mb-6 flex-wrap">
            {filters.map((f) => (
              <button
                key={f}
                onClick={() => setStatusFilter(f)}
                className={`px-3 py-1.5 text-xs font-medium rounded-xl border transition-colors cursor-pointer ${
                  statusFilter === f
                    ? 'bg-blue-accent/20 text-blue-accent border-blue-accent/40'
                    : 'bg-transparent text-dark-muted border-glass-border hover:text-dark-title hover:border-gray-500'
                }`}
              >
                {f || 'All'}
              </button>
            ))}
          </div>
        )}

        {loading && <p className="text-dark-muted text-sm">Loading events...</p>}
        {error && <p className="text-red-400 text-sm">{error}</p>}
        {!loading && !error && events.length === 0 && <p className="text-dark-subtle text-sm">No events found.</p>}

        <div className="space-y-4">
          {events.map((e) => (
            <Link key={e._id} to={`/events/${e._id}`} className="block no-underline">
              <GlassCard glow className="hover:bg-glass-hover transition-colors">
                <div className="flex items-start justify-between gap-4">
                  <div className="min-w-0 flex-1">
                    <div className="flex items-center gap-2 mb-1">
                      <h3 className="text-dark-title font-semibold text-sm truncate">{e.name}</h3>
                      <span className={`text-[10px] font-semibold px-2 py-0.5 rounded-full border ${statusColors[e.status] || ''}`}>
                        {e.status}
                      </span>
                    </div>
                    <p className="text-xs text-dark-subtle">
                      {new Date(e.date).toLocaleDateString()} at {e.time}
                      {e.venue && ` · ${e.venue}`}
                    </p>
                    <div className="flex gap-2 mt-1">
                      <span className="text-[10px] text-dark-subtle bg-warm-hover px-2 py-0.5 rounded-full">{e.category}</span>
                    </div>
                  </div>
                </div>
              </GlassCard>
            </Link>
          ))}
        </div>
      </section>
    </PageTransition>
  )
}
