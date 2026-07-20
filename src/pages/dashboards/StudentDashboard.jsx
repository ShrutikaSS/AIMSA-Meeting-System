import { useState, useEffect } from 'react'
import { Link } from 'react-router-dom'
import PageTransition from '../../components/motion/PageTransition'
import { StaggerContainer, StaggerItem } from '../../components/motion/StaggerList'
import FadeIn from '../../components/motion/FadeIn'
import GlassCard from '../../components/ui/GlassCard'
import SectionHeading from '../../components/ui/SectionHeading'
import api from '../../lib/axios'

const statusColors = { pending: 'text-yellow-400', approved: 'text-green-400', rejected: 'text-red-400', expired: 'text-dark-subtle' }

export default function StudentDashboard() {
  const [data, setData] = useState(null)
  const [loading, setLoading] = useState(true)

  useEffect(() => {
    (async () => {
      try {
        const { data: res } = await api.get('/dashboard')
        setData(res.data)
      } catch (_) {} finally { setLoading(false) }
    })()
  }, [])

  if (loading) return <PageTransition><section className="max-w-7xl mx-auto px-6 py-12"><p className="text-dark-muted">Loading...</p></section></PageTransition>

  const m = data?.membership
  const regCount = data?.registeredEvents?.length ?? 0
  const upCount = data?.upcomingEvents?.length ?? 0
  const certCount = data?.certificates?.length ?? 0
  const achCount = data?.achievements?.length ?? 0

  return (
    <PageTransition>
      <section className="max-w-7xl mx-auto px-6 py-12">
        <SectionHeading>Student Member Dashboard</SectionHeading>
        <p className="text-dark-muted mb-8">Explore events, achievements, and your membership.</p>

        {/* Membership Card */}
        <GlassCard className="mb-8">
          <h3 className="text-sm font-semibold text-dark-title mb-3">Membership Status</h3>
          {m ? (
            <div className="flex items-center gap-3">
              <div className={`text-2xl font-bold ${statusColors[m.status] || 'text-dark-muted'}`}>{m.status.toUpperCase()}</div>
              <div className="text-xs text-dark-muted">
                <div>Applied: {new Date(m.appliedAt).toLocaleDateString()}</div>
                {m.renewedAt && <div>Renewed: {new Date(m.renewedAt).toLocaleDateString()}</div>}
                {m.approvedBy && <div>Approved by: {m.approvedBy.name}</div>}
              </div>
              <Link to={m.status === 'pending' ? '/membership/status' : '/membership/apply'} className="ml-auto text-xs px-3 py-1.5 rounded-xl bg-orange-accent text-white no-underline">Manage</Link>
            </div>
          ) : (
            <div className="flex items-center justify-between">
              <span className="text-xs text-dark-subtle">Not applied yet</span>
              <Link to="/membership/apply" className="text-xs px-3 py-1.5 rounded-xl bg-orange-accent text-white no-underline">Apply Now</Link>
            </div>
          )}
        </GlassCard>

        {/* Stats */}
        <StaggerContainer className="grid grid-cols-2 sm:grid-cols-4 gap-4 mb-10">
          <StaggerItem>
          <Link to="/events" className="no-underline"><GlassCard hover3d className="hover:bg-glass-hover transition-colors"><p className="text-xs text-dark-subtle">Registered Events</p><p className="text-2xl font-bold text-blue-accent mt-1">{regCount}</p></GlassCard></Link>
          </StaggerItem>
          <StaggerItem>
          <Link to="/events" className="no-underline"><GlassCard hover3d className="hover:bg-glass-hover transition-colors"><p className="text-xs text-dark-subtle">Upcoming</p><p className="text-2xl font-bold text-green-400 mt-1">{upCount}</p></GlassCard></Link>
          </StaggerItem>
          <StaggerItem>
          <Link to="/certificates" className="no-underline"><GlassCard hover3d className="hover:bg-glass-hover transition-colors"><p className="text-xs text-dark-subtle">Certificates</p><p className="text-2xl font-bold text-purple-400 mt-1">{certCount}</p></GlassCard></Link>
          </StaggerItem>
          <StaggerItem>
          <Link to="/achievements" className="no-underline"><GlassCard hover3d className="hover:bg-glass-hover transition-colors"><p className="text-xs text-dark-subtle">Achievements</p><p className="text-2xl font-bold text-orange-accent mt-1">{achCount}</p></GlassCard></Link>
          </StaggerItem>
        </StaggerContainer>

        {/* Event Calendar */}
        <FadeIn>
        <GlassCard className="mb-10">
          <h3 className="text-sm font-semibold text-dark-title mb-4">Events This Month</h3>
          {data?.eventsThisMonth?.length === 0 && <p className="text-xs text-dark-subtle">No events this month.</p>}
          <div className="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3">
            {data?.eventsThisMonth?.map((e) => (
              <Link key={e._id} to={`/events/${e._id}`} className="no-underline">
                <div className="bg-warm-card border border-glass-border rounded-xl p-3 hover:bg-glass-hover transition-colors">
                  <p className="text-xs font-semibold text-dark-title truncate">{e.name}</p>
                  <div className="flex gap-2 mt-1 text-[10px] text-dark-subtle">
                    <span>{new Date(e.date).toLocaleDateString()}</span>
                    <span>{e.time}</span>
                  </div>
                  {e.venue && <p className="text-[10px] text-gray-600 mt-0.5 truncate">{e.venue}</p>}
                </div>
              </Link>
            ))}
          </div>
        </GlassCard>
        </FadeIn>

        <FadeIn>
        {/* Recent + Notifications */}
        <div className="grid grid-cols-1 lg:grid-cols-2 gap-6">
          <GlassCard>
            <h3 className="text-sm font-semibold text-dark-title mb-4">My Registered Events</h3>
            {data?.registeredEvents?.length === 0 && <p className="text-xs text-dark-subtle">No registrations.</p>}
            <div className="space-y-2">
              {data?.registeredEvents?.map((r) => (
                <Link key={r._id} to={`/events/${r.event._id}`} className="flex items-center justify-between no-underline">
                  <span className="text-xs text-dark-body truncate">{r.event.name}</span>
                  <span className="text-[10px] text-dark-subtle">{new Date(r.event.date).toLocaleDateString()}</span>
                </Link>
              ))}
            </div>
          </GlassCard>

          <GlassCard>
            <h3 className="text-sm font-semibold text-dark-title mb-3">Notifications ({data?.unreadCount ?? 0})</h3>
            {data?.recentNotifications?.length === 0 ? <p className="text-xs text-dark-subtle">None</p> : (
              <div className="space-y-1">
                {data?.recentNotifications?.map((n) => (
                  <div key={n._id} className="flex items-center gap-2 text-xs text-dark-muted">
                    <span className={`w-1.5 h-1.5 rounded-full shrink-0 ${n.color === 'green' ? 'bg-green-400' : n.color === 'yellow' ? 'bg-yellow-400' : 'bg-red-400'}`} />
                    <span className="truncate">{n.title}</span>
                    <span className="text-[9px] text-gray-600 ml-auto">{new Date(n.createdAt).toLocaleDateString()}</span>
                  </div>
                ))}
              </div>
            )}
            <Link to="/notifications" className="block text-xs text-blue-accent mt-2 no-underline">View all</Link>
          </GlassCard>
        </div>
        </FadeIn>
      </section>
    </PageTransition>
  )
}
