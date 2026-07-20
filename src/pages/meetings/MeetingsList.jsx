import { useState, useEffect } from 'react'
import { Link, useSearchParams } from 'react-router-dom'
import PageTransition from '../../components/motion/PageTransition'
import GlassCard from '../../components/ui/GlassCard'
import GradientButton from '../../components/ui/GradientButton'
import { useAuth } from '../../context/AuthContext'
import api from '../../lib/axios'

const SCHEDULER_ROLES = ['Faculty Coordinator', 'Association President', 'Vice President']
const statusColors = {
  scheduled: 'text-blue-accent border-blue-accent/30 bg-blue-accent/10',
  ongoing: 'text-green-400 border-green-400/30 bg-green-400/10',
  completed: 'text-dark-muted border-gray-500/30 bg-gray-500/10',
  cancelled: 'text-red-400 border-red-400/30 bg-red-400/10',
}

export default function MeetingsList() {
  const { user } = useAuth()
  const [searchParams, setSearchParams] = useSearchParams()
  const [meetings, setMeetings] = useState([])
  const [loading, setLoading] = useState(true)
  const [searchQuery, setSearchQuery] = useState('')
  const [error, setError] = useState(null)

  const statusFilter = searchParams.get('status') || ''

  const canSchedule = SCHEDULER_ROLES.includes(user?.role)

  useEffect(() => {
    (async () => {
      setLoading(true)
      setError(null)
      try {
        const params = {}
        if (statusFilter) params.status = statusFilter
        if (searchQuery) params.search = searchQuery
        const { data } = await api.get('/meetings', { params })
        setMeetings(data.meetings || [])
      } catch (err) {
        setError(err.response?.data?.message || 'Failed to load meetings')
      } finally {
        setLoading(false)
      }
    })()
  }, [statusFilter, searchQuery])

  const filters = ['', 'scheduled', 'ongoing', 'completed', 'cancelled']

  return (
    <PageTransition>
      <section className="max-w-7xl mx-auto px-6 py-10">
        <div className="flex items-center justify-between mb-8">
          <h1 className="text-2xl font-bold text-gradient">Meetings</h1>
          {canSchedule && (
            <Link to="/meetings/create">
              <GradientButton className="text-sm">+ Schedule Meeting</GradientButton>
            </Link>
          )}
        </div>

        <div className="flex flex-wrap items-center gap-3 mb-6">
          <div className="relative flex-1 min-w-[200px] max-w-xs">
            <svg className="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-dark-subtle" fill="none" stroke="currentColor" strokeWidth="2" viewBox="0 0 24 24"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.35-4.35"/></svg>
            <input type="text" value={searchQuery} onChange={(e) => setSearchQuery(e.target.value)}
              placeholder="Search meetings..." className="w-full pl-9 pr-3 py-2 rounded-xl bg-warm-card border border-glass-border text-sm text-dark-title outline-none focus:border-orange-accent/50 transition-colors" />
          </div>
        </div>
        <div className="flex gap-2 mb-6 flex-wrap">
          {filters.map((f) => (
            <button
              key={f}
              onClick={() => setSearchParams(f ? { status: f } : {})}
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

        {loading && <p className="text-dark-muted text-sm">Loading meetings...</p>}
        {error && <p className="text-red-400 text-sm">{error}</p>}

        {!loading && !error && meetings.length === 0 && (
          <p className="text-dark-subtle text-sm">No meetings found.</p>
        )}

        <div className="space-y-4">
          {meetings.map((m) => {
            const myInvite = m.invitations?.find((i) => i.user?._id === user?._id)
            return (
              <Link key={m._id} to={`/meetings/${m._id}`} className="block no-underline">
                <GlassCard glow className="hover:bg-glass-hover transition-colors">
                  <div className="flex items-start justify-between gap-4">
                    <div className="min-w-0 flex-1">
                      <div className="flex items-center gap-2 mb-1">
                        <h3 className="text-dark-title font-semibold text-sm truncate">{m.title}</h3>
                        <span className={`text-[10px] font-semibold px-2 py-0.5 rounded-full border ${statusColors[m.status] || ''}`}>
                          {m.status}
                        </span>
                      </div>
                      <p className="text-xs text-dark-subtle">
                        {new Date(m.date).toLocaleDateString()} at {m.time}
                        {m.venue && ` · ${m.venue}`}
                      </p>
                      {m.agenda && (
                        <p className="text-xs text-dark-muted mt-1 line-clamp-2">{m.agenda}</p>
                      )}
                    </div>
                    <div className="shrink-0 text-right text-xs text-dark-subtle">
                      {myInvite && (
                        <span className={`block ${myInvite.status === 'accepted' ? 'text-green-400' : myInvite.status === 'declined' ? 'text-red-400' : 'text-amber-400'}`}>
                          {myInvite.status}
                        </span>
                      )}
                      <span className="block mt-1">{m.invitations?.length || 0} invited</span>
                    </div>
                  </div>
                </GlassCard>
              </Link>
            )
          })}
        </div>
      </section>
    </PageTransition>
  )
}
