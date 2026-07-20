import { useState, useEffect } from 'react'
import { Link } from 'react-router-dom'
import PageTransition from '../../components/motion/PageTransition'
import { StaggerContainer, StaggerItem } from '../../components/motion/StaggerList'
import FadeIn from '../../components/motion/FadeIn'
import GlassCard from '../../components/ui/GlassCard'
import SectionHeading from '../../components/ui/SectionHeading'
import api from '../../lib/axios'

export default function FacultyDashboard() {
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
    { label: 'Approved Events', value: data?.eventsApproved ?? 0, to: '/events', color: 'text-green-400' },
    { label: 'Pending Events', value: data?.eventsPending ?? 0, to: '/events', color: 'text-yellow-400' },
    { label: 'Students', value: data?.totalStudents ?? 0, to: '/users', color: 'text-blue-accent' },
    { label: 'Memberships', value: data?.approvedMemberships ?? 0, to: '/membership/manage', color: 'text-purple-400' },
    { label: 'Pending Reviews', value: data?.pendingAchievements ?? 0, to: '/achievements/review', color: 'text-orange-accent' },
    { label: 'Attendance Rate', value: (data?.attendanceSummary?.percentage ?? 0) + '%', to: '/meetings', color: 'text-cyan-400' },
  ]

  const att = data?.attendanceSummary

  return (
    <PageTransition>
      <section className="max-w-7xl mx-auto px-6 py-12">
        <SectionHeading>Faculty Coordinator Dashboard</SectionHeading>
        <p className="text-dark-muted mb-8">Monitor events, members, and student progress.</p>

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

        <FadeIn>
        <div className="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-10">
          <GlassCard>
            <h3 className="text-sm font-semibold text-dark-title mb-4">Attendance Summary</h3>
            {att && att.total > 0 ? (
              <div className="space-y-3">
                <div className="flex items-center gap-4">
                  <div className="flex-1 bg-warm-card rounded-full h-2">
                    <div className="bg-green-400 h-2 rounded-full" style={{ width: att.percentage + '%' }} />
                  </div>
                  <span className="text-xs text-dark-muted">{att.percentage}%</span>
                </div>
                <div className="flex gap-4 text-xs text-dark-muted">
                  <span>Present: <span className="text-green-400 font-semibold">{att.present}</span></span>
                  <span>Absent: <span className="text-red-400 font-semibold">{att.absent}</span></span>
                  <span>Total: <span className="text-dark-body font-semibold">{att.total}</span></span>
                </div>
              </div>
            ) : <p className="text-xs text-dark-subtle">No attendance records yet.</p>}
          </GlassCard>

          <GlassCard>
            <h3 className="text-sm font-semibold text-dark-title mb-4">Today's Meetings</h3>
            {data?.todayMeetings?.length === 0 && <p className="text-xs text-dark-subtle">No meetings scheduled today.</p>}
            <div className="space-y-2">
              {data?.todayMeetings?.map((m) => (
                <Link key={m._id} to={`/meetings/${m._id}`} className="flex items-center justify-between no-underline">
                  <span className="text-xs text-dark-body truncate">{m.title}</span>
                  <span className="text-[10px] text-dark-subtle">{m.time}</span>
                </Link>
              ))}
            </div>
          </GlassCard>
        </div>
        </FadeIn>

        <FadeIn>
        <div className="grid grid-cols-1 lg:grid-cols-3 gap-4">
          <Link to="/events/create" className="no-underline"><GlassCard className="hover:bg-glass-hover transition-colors text-center py-6"><p className="text-sm font-semibold text-orange-accent">+ Create Event</p></GlassCard></Link>
          <Link to="/achievements/review" className="no-underline"><GlassCard className="hover:bg-glass-hover transition-colors text-center py-6"><p className="text-sm font-semibold text-blue-accent">{data?.pendingAchievements ?? 0} Achievements to Review</p></GlassCard></Link>
          <GlassCard>
            <h3 className="text-sm font-semibold text-dark-title mb-3">Notifications</h3>
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
