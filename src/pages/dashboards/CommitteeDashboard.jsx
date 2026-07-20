import { useState, useEffect } from 'react'
import { Link } from 'react-router-dom'
import PageTransition from '../../components/motion/PageTransition'
import { StaggerContainer, StaggerItem } from '../../components/motion/StaggerList'
import FadeIn from '../../components/motion/FadeIn'
import GlassCard from '../../components/ui/GlassCard'
import SectionHeading from '../../components/ui/SectionHeading'
import api from '../../lib/axios'

export default function CommitteeDashboard() {
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

  const att = data?.myAttendance

  return (
    <PageTransition>
      <section className="max-w-7xl mx-auto px-6 py-12">
        <SectionHeading>Committee Member Dashboard</SectionHeading>
        <p className="text-dark-muted mb-8">Your meetings, attendance, and resources.</p>

        <StaggerContainer className="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-10">
          <StaggerItem>
          <GlassCard hover3d>
            <p className="text-xs text-dark-subtle uppercase tracking-wider">My Meetings</p>
            <p className="text-3xl font-bold text-blue-accent mt-1">{data?.myMeetings?.length ?? 0}</p>
          </GlassCard>
          </StaggerItem>
          <StaggerItem>
          <GlassCard hover3d>
            <p className="text-xs text-dark-subtle uppercase tracking-wider">Attendance Rate</p>
            <p className="text-3xl font-bold text-green-400 mt-1">{att?.percentage ?? 0}%</p>
          </GlassCard>
          </StaggerItem>
          <StaggerItem>
          <GlassCard hover3d>
            <p className="text-xs text-dark-subtle uppercase tracking-wider">MOMs Published</p>
            <p className="text-3xl font-bold text-purple-400 mt-1">{data?.recentMoms?.length ?? 0}</p>
          </GlassCard>
          </StaggerItem>
        </StaggerContainer>

        <FadeIn>
        <div className="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-10">
          <GlassCard>
            <h3 className="text-sm font-semibold text-dark-title mb-4">My Meetings</h3>
            {data?.myMeetings?.length === 0 && <p className="text-xs text-dark-subtle">No meetings yet.</p>}
            <div className="space-y-2">
              {data?.myMeetings?.map((m) => (
                <Link key={m._id} to={`/meetings/${m._id}`} className="flex items-center justify-between no-underline">
                  <span className="text-xs text-dark-body truncate">{m.title}</span>
                  <div className="flex gap-2 text-[10px] text-dark-subtle">
                    <span>{new Date(m.date).toLocaleDateString()}</span>
                    <span>{m.time}</span>
                  </div>
                </Link>
              ))}
            </div>
          </GlassCard>

          <GlassCard>
            <h3 className="text-sm font-semibold text-dark-title mb-4">Attendance Records</h3>
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
        </div>
        </FadeIn>

        <FadeIn>
        <div className="grid grid-cols-1 lg:grid-cols-3 gap-4">
          <GlassCard>
            <h3 className="text-sm font-semibold text-dark-title mb-3">Recent MOM</h3>
            {data?.recentMoms?.length === 0 ? <p className="text-xs text-dark-subtle">None</p> : (
              <div className="space-y-1">
                {data?.recentMoms?.map((m) => (
                  <Link key={m._id} to={`/meetings/${m.meeting._id}`} className="block text-xs text-dark-muted no-underline hover:text-dark-title">
                    {m.meeting.title} — <span className="text-gray-600">{new Date(m.createdAt).toLocaleDateString()}</span>
                  </Link>
                ))}
              </div>
            )}
          </GlassCard>
          <Link to="/meetings" className="no-underline"><GlassCard className="hover:bg-glass-hover transition-colors text-center py-6"><p className="text-sm font-semibold text-blue-accent">View All Meetings</p></GlassCard></Link>
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
