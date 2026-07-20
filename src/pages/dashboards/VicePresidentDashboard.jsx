import { useState, useEffect } from 'react'
import { Link } from 'react-router-dom'
import PageTransition from '../../components/motion/PageTransition'
import { StaggerContainer, StaggerItem } from '../../components/motion/StaggerList'
import FadeIn from '../../components/motion/FadeIn'
import GlassCard from '../../components/ui/GlassCard'
import SectionHeading from '../../components/ui/SectionHeading'
import api from '../../lib/axios'

export default function VicePresidentDashboard() {
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

  const stats = [
    { label: 'Committee', value: data?.committeeMembers ?? 0, to: '/committee', color: 'text-purple-400' },
    { label: 'Registrations', value: data?.totalRegistrations ?? 0, to: '/events', color: 'text-blue-accent' },
    { label: 'Membership Pending', value: data?.pendingMemberships ?? 0, to: '/membership/manage', color: 'text-yellow-400' },
    { label: 'Achievements Pending', value: data?.pendingAchievements ?? 0, to: '/achievements/review', color: 'text-orange-accent' },
  ]

  return (
    <PageTransition>
      <section className="max-w-7xl mx-auto px-6 py-12">
        <SectionHeading>Vice President Dashboard</SectionHeading>
        <p className="text-dark-muted mb-8">Association overview and pending actions.</p>

        <StaggerContainer className="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-10">
          {stats.map((s) => (
            <StaggerItem key={s.label}>
            <Link to={s.to} className="no-underline">
              <GlassCard hover3d className="hover:bg-glass-hover transition-colors">
                <p className="text-xs text-dark-subtle uppercase tracking-wider">{s.label}</p>
                <p className={`text-3xl font-bold mt-1 ${s.color}`}>{s.value}</p>
              </GlassCard>
            </Link>
            </StaggerItem>
          ))}
        </StaggerContainer>

        <FadeIn>
        <div className="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-10">
          <GlassCard>
            <h3 className="text-sm font-semibold text-dark-title mb-4">Upcoming Events</h3>
            {data?.upcomingEvents?.length === 0 && <p className="text-xs text-dark-subtle">No upcoming events.</p>}
            <div className="space-y-2">
              {data?.upcomingEvents?.map((e) => (
                <Link key={e._id} to={`/events/${e._id}`} className="flex items-center justify-between no-underline">
                  <span className="text-xs text-dark-body truncate">{e.name}</span>
                  <span className="text-[10px] text-dark-subtle">{new Date(e.date).toLocaleDateString()}</span>
                </Link>
              ))}
            </div>
          </GlassCard>

          <GlassCard>
            <h3 className="text-sm font-semibold text-dark-title mb-4">Recent Announcements</h3>
            {data?.recentAnnouncements?.length === 0 && <p className="text-xs text-dark-subtle">No announcements.</p>}
            <div className="space-y-2">
              {data?.recentAnnouncements?.map((a) => (
                <div key={a._id} className="text-xs text-dark-muted">
                  <span className="font-medium text-dark-body">{(a.createdBy && a.createdBy.name) || 'Unknown'}</span>
                  <span className="ml-1">{a.title}</span>
                </div>
              ))}
            </div>
          </GlassCard>
        </div>
        </FadeIn>

        <FadeIn>
        <div className="grid grid-cols-1 lg:grid-cols-3 gap-4">
          <Link to="/events/create" className="no-underline"><GlassCard className="hover:bg-glass-hover transition-colors text-center py-6"><p className="text-sm font-semibold text-orange-accent">+ Create Event</p></GlassCard></Link>
          <Link to="/certificates/issue" className="no-underline"><GlassCard className="hover:bg-glass-hover transition-colors text-center py-6"><p className="text-sm font-semibold text-blue-accent">Issue Certificate</p></GlassCard></Link>
          <GlassCard>
            <h3 className="text-sm font-semibold text-dark-title mb-3">Notifications ({data?.unreadCount ?? 0})</h3>
            {data?.recentNotifications?.length === 0 ? <p className="text-xs text-dark-subtle">None</p> : (
              <div className="space-y-1">
                {data?.recentNotifications?.map((n) => (
                  <div key={n._id} className="flex items-center gap-2 text-xs text-dark-muted">
                    <span className={`w-1.5 h-1.5 rounded-full ${n.color === 'green' ? 'bg-green-400' : n.color === 'yellow' ? 'bg-yellow-400' : 'bg-red-400'}`} />
                    <span className="truncate">{n.title}</span>
                  </div>
                ))}
              </div>
            )}
          </GlassCard>
        </div>
        </FadeIn>
      </section>
    </PageTransition>
  )
}
