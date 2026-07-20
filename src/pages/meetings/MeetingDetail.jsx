import { useState, useEffect } from 'react'
import { useParams, Link, useNavigate } from 'react-router-dom'
import PageTransition from '../../components/motion/PageTransition'
import GlassCard from '../../components/ui/GlassCard'
import GradientButton from '../../components/ui/GradientButton'
import { useAuth } from '../../context/AuthContext'
import api from '../../lib/axios'

const SCHEDULER_ROLES = ['Faculty Coordinator', 'Association President', 'Vice President']

const tabs = ['details', 'mom', 'attendance']

export default function MeetingDetail() {
  const { id } = useParams()
  const { user } = useAuth()
  const navigate = useNavigate()
  const [meeting, setMeeting] = useState(null)
  const [mom, setMom] = useState(null)
  const [attendance, setAttendance] = useState([])
  const [activeTab, setActiveTab] = useState('details')
  const [loading, setLoading] = useState(true)
  const [momNotes, setMomNotes] = useState('')
  const [attendanceStatus, setAttendanceStatus] = useState({})
  const [error, setError] = useState(null)

  const canSchedule = SCHEDULER_ROLES.includes(user?.role)
  const isScheduler = meeting && meeting.createdBy?._id === user?._id
  const canEdit = canSchedule
  const isInvitee = meeting?.invitations?.some((i) => i.user?._id === user?._id)

  useEffect(() => {
    (async () => {
      setLoading(true)
      try {
        const { data: mData } = await api.get(`/meetings/${id}`)
        setMeeting(mData.meeting)

        const { data: momData } = await api.get(`/meetings/${id}/mom`).catch(() => ({ data: { mom: null } }))
        setMom(momData.mom)
        if (momData.mom) setMomNotes(momData.mom.notes || '')

        const { data: attData } = await api.get(`/meetings/${id}/attendance`).catch(() => ({ data: { attendance: [] } }))
        setAttendance(attData.attendance || [])
        const statusMap = {}
        ;(attData.attendance || []).forEach((a) => { statusMap[a.user?._id] = a.status })
        setAttendanceStatus(statusMap)
      } catch (err) {
        setError(err.response?.data?.message || 'Failed to load meeting')
      } finally {
        setLoading(false)
      }
    })()
  }, [id])

  const handleCancel = async () => {
    if (!window.confirm('Cancel this meeting?')) return
    try {
      await api.put(`/meetings/${id}/cancel`)
      const { data } = await api.get(`/meetings/${id}`)
      setMeeting(data.meeting)
    } catch (err) {
      alert(err.response?.data?.message || 'Failed to cancel')
    }
  }

  const handleRespond = async (status) => {
    try {
      await api.put(`/meetings/${id}/respond`, { status })
      const { data } = await api.get(`/meetings/${id}`)
      setMeeting(data.meeting)
    } catch (err) {
      alert(err.response?.data?.message || 'Failed to respond')
    }
  }

  const handleSaveMOM = async () => {
    try {
      const payload = { notes: momNotes }
      if (mom) {
        const { data } = await api.put(`/meetings/${id}/mom`, payload)
        setMom(data.mom)
      } else {
        const { data } = await api.post(`/meetings/${id}/mom`, payload)
        setMom(data.mom)
      }
      alert('MOM saved')
    } catch (err) {
      alert(err.response?.data?.message || 'Failed to save MOM')
    }
  }

  const handleMarkAttendance = async (userId, status) => {
    try {
      const { data } = await api.post(`/meetings/${id}/attendance`, { userId, status })
      setAttendance(data.attendance)
      const statusMap = {}
      data.attendance.forEach((a) => { statusMap[a.user?._id] = a.status })
      setAttendanceStatus(statusMap)
    } catch (err) {
      alert(err.response?.data?.message || 'Failed to mark attendance')
    }
  }

  const canMarkAttendance = canSchedule || isScheduler

  if (loading) return <div className="max-w-4xl mx-auto px-6 py-10 text-dark-muted text-sm">Loading...</div>
  if (error) return <div className="max-w-4xl mx-auto px-6 py-10 text-red-400 text-sm">{error}</div>
  if (!meeting) return null

  return (
    <PageTransition>
      <section className="max-w-4xl mx-auto px-6 py-10">
        <Link to="/meetings" className="text-xs text-blue-accent hover:underline no-underline mb-4 inline-block">&larr; Back to Meetings</Link>

        <GlassCard glow className="mb-6">
          <div className="flex items-start justify-between gap-4">
            <div>
              <h1 className="text-xl font-bold text-dark-title mb-1">{meeting.title}</h1>
              <p className="text-xs text-dark-muted">
                {new Date(meeting.date).toLocaleDateString()} at {meeting.time}
                {meeting.venue && ` · ${meeting.venue}`}
              </p>
            </div>
            <span className={`text-[11px] font-semibold px-3 py-1 rounded-full border ${
              meeting.status === 'scheduled' ? 'text-blue-accent border-blue-accent/30 bg-blue-accent/10' :
              meeting.status === 'ongoing' ? 'text-green-400 border-green-400/30 bg-green-400/10' :
              meeting.status === 'completed' ? 'text-dark-muted border-gray-500/30 bg-gray-500/10' :
              'text-red-400 border-red-400/30 bg-red-400/10'
            }`}>{meeting.status}</span>
          </div>

          {meeting.agenda && (
            <div className="mt-4">
              <p className="text-xs font-semibold text-dark-body mb-1">Agenda</p>
              <p className="text-sm text-dark-muted whitespace-pre-wrap">{meeting.agenda}</p>
            </div>
          )}

          <div className="mt-4 flex flex-wrap gap-2 text-xs text-dark-subtle">
            <span>Scheduled by: {meeting.createdBy?.name || 'N/A'}</span>
            {meeting.linkedEvent && <span>Linked Event: {meeting.linkedEvent?.title || 'N/A'}</span>}
            {meeting.invitations && <span>{meeting.invitations.length} invitee(s)</span>}
          </div>

          {meeting.status === 'scheduled' && (
            <div className="mt-4 flex gap-3">
              {canEdit && (
                <>
                  <Link to={`/meetings/${id}/edit`}>
                    <GradientButton className="text-xs px-4 py-1.5">Edit</GradientButton>
                  </Link>
                  <button onClick={handleCancel} className="text-xs px-4 py-1.5 rounded-2xl border border-red-400/30 text-red-400 hover:bg-red-400/10 transition-colors cursor-pointer bg-transparent">Cancel Meeting</button>
                </>
              )}
              {isInvitee && meeting.status === 'scheduled' && (
                <>
                  <button onClick={() => handleRespond('accepted')} className="text-xs px-4 py-1.5 rounded-2xl bg-green-500/20 text-green-400 border border-green-500/30 hover:bg-green-500/30 transition-colors cursor-pointer">Accept</button>
                  <button onClick={() => handleRespond('declined')} className="text-xs px-4 py-1.5 rounded-2xl bg-red-500/20 text-red-400 border border-red-500/30 hover:bg-red-500/30 transition-colors cursor-pointer">Decline</button>
                </>
              )}
            </div>
          )}
        </GlassCard>

        {/* Tabs */}
        <div className="flex gap-1 mb-6 border-b border-glass-border">
          {tabs.map((t) => (
            <button
              key={t}
              onClick={() => setActiveTab(t)}
              className={`px-4 py-2 text-xs font-medium capitalize transition-colors border-b-2 cursor-pointer bg-transparent ${
                activeTab === t ? 'text-blue-accent border-blue-accent' : 'text-dark-subtle border-transparent hover:text-dark-title'
              }`}
            >
              {t === 'mom' ? 'Minutes of Meeting' : t}
            </button>
          ))}
        </div>

        {/* Tab: Details - Invitations */}
        {activeTab === 'details' && (
          <GlassCard>
            <h3 className="text-sm font-semibold text-dark-title mb-3">Invitations</h3>
            {meeting.invitations?.length === 0 && <p className="text-xs text-dark-subtle">No invitees yet.</p>}
            <div className="space-y-2">
              {meeting.invitations?.map((inv, i) => (
                <div key={i} className="flex items-center justify-between text-xs">
                  <span className="text-dark-body">{inv.user?.name || 'Unknown'} <span className="text-dark-subtle">({inv.user?.role || ''})</span></span>
                  <span className={`${
                    inv.status === 'accepted' ? 'text-green-400' :
                    inv.status === 'declined' ? 'text-red-400' : 'text-amber-400'
                  }`}>{inv.status}</span>
                </div>
              ))}
            </div>
          </GlassCard>
        )}

        {/* Tab: MOM */}
        {activeTab === 'mom' && (
          <GlassCard>
            <h3 className="text-sm font-semibold text-dark-title mb-3">Minutes of Meeting</h3>
            <textarea
              value={momNotes}
              onChange={(e) => setMomNotes(e.target.value)}
              rows={6}
              className="w-full px-4 py-3 rounded-2xl bg-warm-card border border-glass-border text-dark-title text-sm placeholder-dark-subtle focus:outline-none focus:border-blue-accent resize-y"
              placeholder="Type MOM notes here..."
              readOnly={!canEdit && !isScheduler && user?.role !== 'Committee Member'}
            />
            {(canEdit || isScheduler || user?.role === 'Committee Member') && (
              <div className="mt-3 flex gap-3">
                <button onClick={handleSaveMOM} className="text-xs px-4 py-1.5 rounded-2xl bg-gradient-accent text-white font-semibold cursor-pointer border-0">Save MOM</button>
              </div>
            )}
            {mom?.attachments?.length > 0 && (
              <div className="mt-4">
                <p className="text-xs font-semibold text-dark-body mb-2">Attachments</p>
                {mom.attachments.map((att, i) => (
                  <p key={i} className="text-xs text-blue-accent">{att.filename}</p>
                ))}
              </div>
            )}
          </GlassCard>
        )}

        {/* Tab: Attendance */}
        {activeTab === 'attendance' && (
          <GlassCard>
            <h3 className="text-sm font-semibold text-dark-title mb-3">Attendance</h3>
            {isInvitee && meeting.status !== 'cancelled' && (
              <div className="mb-4 flex gap-2">
                <button onClick={() => handleMarkAttendance(user._id, 'present')} className={`text-xs px-3 py-1 rounded-xl border cursor-pointer ${attendanceStatus[user._id] === 'present' ? 'bg-green-500/20 text-green-400 border-green-500/30' : 'bg-transparent text-dark-muted border-glass-border hover:text-dark-title'}`}>Mark Present</button>
                <button onClick={() => handleMarkAttendance(user._id, 'absent')} className={`text-xs px-3 py-1 rounded-xl border cursor-pointer ${attendanceStatus[user._id] === 'absent' ? 'bg-red-500/20 text-red-400 border-red-500/30' : 'bg-transparent text-dark-muted border-glass-border hover:text-dark-title'}`}>Mark Absent</button>
              </div>
            )}
            {canMarkAttendance && meeting.invitations?.map((inv) => (
              <div key={inv.user?._id} className="flex items-center justify-between text-xs py-1.5">
                <span className="text-dark-body">{inv.user?.name || 'Unknown'}</span>
                <div className="flex gap-1">
                  <button onClick={() => handleMarkAttendance(inv.user._id, 'present')} className={`px-2 py-0.5 rounded-lg border text-[10px] cursor-pointer ${attendanceStatus[inv.user._id] === 'present' ? 'bg-green-500/20 text-green-400 border-green-500/30' : 'bg-transparent text-dark-subtle border-glass-border hover:text-dark-title'}`}>P</button>
                  <button onClick={() => handleMarkAttendance(inv.user._id, 'absent')} className={`px-2 py-0.5 rounded-lg border text-[10px] cursor-pointer ${attendanceStatus[inv.user._id] === 'absent' ? 'bg-red-500/20 text-red-400 border-red-500/30' : 'bg-transparent text-dark-subtle border-glass-border hover:text-dark-title'}`}>A</button>
                </div>
              </div>
            ))}
            {(!canMarkAttendance && !isInvitee) && <p className="text-xs text-dark-subtle">Attendance records available to invitees and schedulers.</p>}
            {attendance.length > 0 && (
              <div className="mt-4 pt-3 border-t border-glass-border">
                <p className="text-xs font-semibold text-dark-body mb-2">Current Records</p>
                {attendance.map((a) => (
                  <div key={a._id} className="flex justify-between text-xs py-1">
                    <span className="text-dark-body">{a.user?.name || 'Unknown'}</span>
                    <span className={a.status === 'present' ? 'text-green-400' : 'text-red-400'}>{a.status}</span>
                  </div>
                ))}
              </div>
            )}
          </GlassCard>
        )}
      </section>
    </PageTransition>
  )
}
