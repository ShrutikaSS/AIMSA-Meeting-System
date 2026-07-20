import { useState, useEffect } from 'react'
import { Link } from 'react-router-dom'
import PageTransition from '../../components/motion/PageTransition'
import { StaggerContainer, StaggerItem } from '../../components/motion/StaggerList'
import FadeIn from '../../components/motion/FadeIn'
import GlassCard from '../../components/ui/GlassCard'
import SectionHeading from '../../components/ui/SectionHeading'
import api from '../../lib/axios'

export default function AdministratorDashboard() {
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

  if (loading) return <PageTransition><section className="max-w-7xl mx-auto px-6 py-12"><p className="text-dark-muted">Loading dashboard...</p></section></PageTransition>

  const stats = [
    { label: 'Student Members', value: data?.totalMembers ?? 0, to: '/membership/manage', color: 'text-blue-accent' },
    { label: 'Committee Members', value: data?.committeeMembers ?? 0, to: '/committee', color: 'text-purple-400' },
    { label: 'Upcoming Events', value: data?.eventsUpcoming ?? 0, to: '/events', color: 'text-green-400' },
    { label: 'Events Conducted', value: data?.eventsConducted ?? 0, to: '/events', color: 'text-orange-accent' },
    { label: 'Total Registrations', value: data?.totalRegistrations ?? 0, to: '/events', color: 'text-yellow-400' },
    { label: 'Certificates Issued', value: data?.certificatesGenerated ?? 0, to: '/certificates', color: 'text-cyan-400' },
  ]

  return (
    <PageTransition>
      <section className="max-w-7xl mx-auto px-6 py-12">
        <SectionHeading>Administrator Dashboard</SectionHeading>
        <p className="text-dark-muted mb-8">Full system oversight and configuration.</p>

        <StaggerContainer className="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 mb-10">
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

        <FadeIn className="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-10">
          <GlassCard>
            <h3 className="text-sm font-semibold text-dark-title mb-4">Recent Events</h3>
            {data?.recentEvents?.length === 0 && <p className="text-xs text-dark-subtle">No events yet.</p>}
            <div className="space-y-2">
              {data?.recentEvents?.map((e) => (
                <Link key={e._id} to={`/events/${e._id}`} className="flex items-center justify-between no-underline">
                  <span className="text-xs text-dark-body truncate">{e.name}</span>
                  <span className={`text-[10px] font-medium px-1.5 py-0.5 rounded ${e.status === 'published' ? 'text-green-400 bg-green-400/10' : e.status === 'draft' ? 'text-yellow-400 bg-yellow-400/10' : 'text-red-400 bg-red-400/10'}`}>{e.status}</span>
                </Link>
              ))}
            </div>
          </GlassCard>

          <GlassCard>
            <h3 className="text-sm font-semibold text-dark-title mb-4">Recent Members</h3>
            {data?.recentMembers?.length === 0 && <p className="text-xs text-dark-subtle">No members yet.</p>}
            <div className="space-y-2">
              {data?.recentMembers?.map((m) => (
                <div key={m._id} className="flex items-center gap-2">
                  <div className="w-6 h-6 rounded-full bg-gradient-accent flex items-center justify-center text-white font-bold text-[10px]">{m.name.charAt(0)}</div>
                  <span className="text-xs text-dark-body">{m.name}</span>
                  <span className="text-[10px] text-dark-subtle ml-auto">{m.email}</span>
                </div>
              ))}
            </div>
          </GlassCard>
        </FadeIn>

        <FadeIn className="grid grid-cols-1 lg:grid-cols-3 gap-4 mb-10">
          <Link to="/committee" className="no-underline"><GlassCard className="hover:bg-glass-hover transition-colors text-center py-6"><p className="text-sm font-semibold text-blue-accent">Manage Committee</p></GlassCard></Link>
          <Link to="/certificates/issue" className="no-underline"><GlassCard className="hover:bg-glass-hover transition-colors text-center py-6"><p className="text-sm font-semibold text-orange-accent">Issue Certificate</p></GlassCard></Link>
          <Link to="/reports" className="no-underline"><GlassCard className="hover:bg-glass-hover transition-colors text-center py-6"><p className="text-sm font-semibold text-purple-400">View Reports</p></GlassCard></Link>
        </FadeIn>

        <FadeIn><GlassCard>
          <h3 className="text-sm font-semibold text-dark-title mb-3">Recent Notifications</h3>
          {data?.recentNotifications?.length === 0 && <p className="text-xs text-dark-subtle">None yet.</p>}
          <div className="space-y-1">
            {data?.recentNotifications?.map((n) => (
              <div key={n._id} className="flex items-center gap-2 text-xs text-dark-muted">
                <span className={`w-1.5 h-1.5 rounded-full ${n.color === 'green' ? 'bg-green-400' : n.color === 'yellow' ? 'bg-yellow-400' : 'bg-red-400'}`} />
                <span className="truncate">{n.title}</span>
                <span className="text-[9px] text-gray-600 ml-auto">{new Date(n.createdAt).toLocaleDateString()}</span>
              </div>
            ))}
          </div>
        </GlassCard></FadeIn>
      </section>
    </PageTransition>
  )
}
