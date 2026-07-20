import { useState, useEffect } from 'react'
import { useParams, Link, useNavigate } from 'react-router-dom'
import PageTransition from '../../components/motion/PageTransition'
import GlassCard from '../../components/ui/GlassCard'
import GradientButton from '../../components/ui/GradientButton'
import { useAuth } from '../../context/AuthContext'
import api from '../../lib/axios'

const MANAGER_ROLES = ['Faculty Coordinator', 'Association President', 'Vice President']
const statusColors = {
  draft: 'text-dark-muted border-gray-500/30',
  published: 'text-green-400 border-green-400/30',
  cancelled: 'text-red-400 border-red-400/30',
  completed: 'text-blue-accent border-blue-accent/30',
}

export default function EventDetail() {
  const { id } = useParams()
  const { user } = useAuth()
  const navigate = useNavigate()

  const [event, setEvent] = useState(null)
  const [meetings, setMeetings] = useState([])
  const [myRegistration, setMyRegistration] = useState(null)
  const [registrations, setRegistrations] = useState([])
  const [activeTab, setActiveTab] = useState('details')
  const [loading, setLoading] = useState(true)
  const [error, setError] = useState(null)

  const canManage = MANAGER_ROLES.includes(user?.role) || user?.role === 'Administrator'
  const isStudent = user?.role === 'Student Member'

  useEffect(() => {
    (async () => {
      setLoading(true)
      try {
        const { data } = await api.get(`/events/${id}`)
        setEvent(data.event)
        setMeetings(data.meetings || [])

        if (isStudent) {
          try {
            const { data: regData } = await api.get(`/registrations/${id}/status`)
            setMyRegistration(regData.registration)
          } catch { /* no registration */ }
        }

        if (canManage) {
          try {
            const { data: listData } = await api.get(`/registrations/${id}/list`)
            setRegistrations(listData.registrations || [])
          } catch { /* no access */ }
        }
      } catch (err) {
        setError(err.response?.data?.message || 'Failed to load event')
      } finally {
        setLoading(false)
      }
    })()
  }, [id, isStudent, canManage])

  const handleRegister = async () => {
    try {
      const { data } = await api.post(`/registrations/${id}`)
      setMyRegistration(data.registration)
    } catch (err) {
      alert(err.response?.data?.message || 'Registration failed')
    }
  }

  const handleCancelRegistration = async () => {
    if (!window.confirm('Cancel your registration?')) return
    try {
      await api.put(`/registrations/${id}/cancel`)
      setMyRegistration(null)
    } catch (err) {
      alert(err.response?.data?.message || 'Failed to cancel')
    }
  }

  const handlePublish = async () => {
    try {
      const { data } = await api.put(`/events/${id}/publish`)
      setEvent(data.event)
    } catch (err) {
      alert(err.response?.data?.message || 'Failed to publish')
    }
  }

  const handleCancelEvent = async () => {
    if (!window.confirm('Cancel this event?')) return
    try {
      const { data } = await api.put(`/events/${id}/cancel`)
      setEvent(data.event)
    } catch (err) {
      alert(err.response?.data?.message || 'Failed to cancel')
    }
  }

  const handleExport = async (format) => {
    try {
      const { data } = await api.get(`/registrations/${id}/export?format=${format}`, { responseType: format === 'csv' ? 'blob' : 'json' })
      if (format === 'csv') {
        const blob = new Blob([data], { type: 'text/csv' })
        const url = URL.createObjectURL(blob)
        const a = document.createElement('a')
        a.href = url; a.download = `registrations-${id}.csv`; a.click()
        URL.revokeObjectURL(url)
      }
    } catch (err) {
      alert('Export failed')
    }
  }

  if (loading) return <div className="max-w-4xl mx-auto px-6 py-10 text-dark-muted text-sm">Loading...</div>
  if (error) return <div className="max-w-4xl mx-auto px-6 py-10 text-red-400 text-sm">{error}</div>
  if (!event) return null

  const tabs = canManage ? ['details', 'registrations', 'meetings'] : ['details', 'meetings']
  const canRegister = isStudent && event.status === 'published'
  const deadlinePassed = event.registrationDeadline && new Date() > new Date(event.registrationDeadline)

  return (
    <PageTransition>
      <section className="max-w-4xl mx-auto px-6 py-10">
        <Link to="/events" className="text-xs text-blue-accent hover:underline no-underline mb-4 inline-block">&larr; Back to Events</Link>

        <GlassCard glow className="mb-6">
          <div className="flex items-start justify-between gap-4 flex-wrap">
            <div>
              <h1 className="text-xl font-bold text-dark-title mb-1">{event.name}</h1>
              <p className="text-xs text-dark-muted">
                {new Date(event.date).toLocaleDateString()} at {event.time}
                {event.venue && ` · ${event.venue}`}
              </p>
              <span className={`inline-block mt-2 text-[10px] font-semibold px-2 py-0.5 rounded-full border ${statusColors[event.status]}`}>
                {event.status}
              </span>
              <span className="inline-block ml-2 text-[10px] text-dark-subtle bg-warm-hover px-2 py-0.5 rounded-full">{event.category}</span>
            </div>
            {canManage && (
              <div className="flex gap-2 flex-wrap">
                {event.status === 'draft' && (
                  <button onClick={handlePublish} className="text-xs px-3 py-1.5 rounded-2xl bg-green-500/20 text-green-400 border border-green-500/30 hover:bg-green-500/30 transition-colors cursor-pointer bg-transparent">Publish</button>
                )}
                <Link to={`/events/${id}/edit`}>
                  <button className="text-xs px-3 py-1.5 rounded-2xl border border-glass-border text-dark-muted hover:text-dark-title transition-colors cursor-pointer bg-transparent">Edit</button>
                </Link>
                {event.status !== 'cancelled' && (
                  <button onClick={handleCancelEvent} className="text-xs px-3 py-1.5 rounded-2xl border border-red-400/30 text-red-400 hover:bg-red-400/10 transition-colors cursor-pointer bg-transparent">Cancel</button>
                )}
              </div>
            )}
          </div>

          {event.description && (
            <div className="mt-4">
              <p className="text-xs font-semibold text-dark-body mb-1">Description</p>
              <p className="text-sm text-dark-muted whitespace-pre-wrap">{event.description}</p>
            </div>
          )}

          <div className="mt-3 flex flex-wrap gap-3 text-xs text-dark-subtle">
            {event.assignedFaculty && <span>Faculty: {event.assignedFaculty.name}</span>}
            {event.maxParticipants > 0 && <span>Max Participants: {event.maxParticipants}</span>}
            {event.registrationDeadline && <span>Deadline: {new Date(event.registrationDeadline).toLocaleDateString()}</span>}
          </div>

          {canRegister && (
            <div className="mt-4">
              {myRegistration?.status === 'registered' ? (
                <div className="flex gap-3 items-center">
                  <span className="text-xs text-green-400">You are registered</span>
                  {!deadlinePassed && (
                    <button onClick={handleCancelRegistration} className="text-xs px-3 py-1 rounded-xl border border-red-400/30 text-red-400 hover:bg-red-400/10 transition-colors cursor-pointer bg-transparent">Cancel Registration</button>
                  )}
                  <Link to={`/registrations/${id}/confirmation`}>
                    <button className="text-xs px-3 py-1 rounded-xl border border-glass-border text-dark-muted hover:text-dark-title transition-colors cursor-pointer bg-transparent">Confirmation</button>
                  </Link>
                </div>
              ) : (
                <button onClick={handleRegister} disabled={!!deadlinePassed} className="text-xs px-4 py-2 rounded-2xl bg-gradient-accent text-white font-semibold cursor-pointer border-0 disabled:opacity-50">
                  {deadlinePassed ? 'Deadline Passed' : 'Register Now'}
                </button>
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
              {t === 'registrations' ? `Registrations (${registrations.length})` : t}
            </button>
          ))}
        </div>

        {activeTab === 'details' && (
          <GlassCard>
            <h3 className="text-sm font-semibold text-dark-title mb-3">Event Details</h3>
            <div className="grid grid-cols-2 gap-3 text-xs">
              <div><span className="text-dark-subtle">Category:</span> <span className="text-dark-body">{event.category}</span></div>
              <div><span className="text-dark-subtle">Date:</span> <span className="text-dark-body">{new Date(event.date).toLocaleDateString()}</span></div>
              <div><span className="text-dark-subtle">Time:</span> <span className="text-dark-body">{event.time}</span></div>
              <div><span className="text-dark-subtle">Venue:</span> <span className="text-dark-body">{event.venue || 'TBD'}</span></div>
              <div><span className="text-dark-subtle">Max Participants:</span> <span className="text-dark-body">{event.maxParticipants || 'Unlimited'}</span></div>
              <div><span className="text-dark-subtle">Registration Deadline:</span> <span className="text-dark-body">{event.registrationDeadline ? new Date(event.registrationDeadline).toLocaleDateString() : 'None'}</span></div>
              {event.assignedFaculty && (
                <div className="col-span-2"><span className="text-dark-subtle">Assigned Faculty:</span> <span className="text-dark-body">{event.assignedFaculty.name} ({event.assignedFaculty.email})</span></div>
              )}
              <div className="col-span-2"><span className="text-dark-subtle">Created by:</span> <span className="text-dark-body">{event.createdBy?.name || 'N/A'}</span></div>
            </div>
          </GlassCard>
        )}

        {activeTab === 'registrations' && canManage && (
          <GlassCard>
            <div className="flex items-center justify-between mb-3">
              <h3 className="text-sm font-semibold text-dark-title">Registrations ({registrations.length})</h3>
              <div className="flex gap-2">
                <button onClick={() => handleExport('json')} className="text-xs px-3 py-1 rounded-xl border border-glass-border text-dark-muted hover:text-dark-title transition-colors cursor-pointer bg-transparent">Export JSON</button>
                <button onClick={() => handleExport('csv')} className="text-xs px-3 py-1 rounded-xl border border-glass-border text-dark-muted hover:text-dark-title transition-colors cursor-pointer bg-transparent">Export CSV</button>
              </div>
            </div>
            {registrations.length === 0 && <p className="text-xs text-dark-subtle">No registrations yet.</p>}
            <div className="space-y-1.5">
              {registrations.map((r) => (
                <div key={r._id} className="flex items-center justify-between text-xs py-1.5 border-b border-glass-border last:border-0">
                  <div>
                    <span className="text-dark-body">{r.user?.name || 'Unknown'}</span>
                    <span className="text-dark-subtle ml-2">{r.user?.email}</span>
                  </div>
                  <span className={r.status === 'registered' ? 'text-green-400' : r.status === 'cancelled' ? 'text-red-400' : 'text-blue-accent'}>{r.status}</span>
                </div>
              ))}
            </div>
          </GlassCard>
        )}

        {activeTab === 'meetings' && (
          <GlassCard>
            <h3 className="text-sm font-semibold text-dark-title mb-3">Linked Meetings ({meetings.length})</h3>
            {meetings.length === 0 && <p className="text-xs text-dark-subtle">No meetings linked to this event.</p>}
            <div className="space-y-2">
              {meetings.map((m) => (
                <Link key={m._id} to={`/meetings/${m._id}`} className="block no-underline">
                  <div className="glass px-4 py-3 hover:bg-glass-hover transition-colors">
                    <p className="text-sm font-medium text-dark-title">{m.title}</p>
                    <p className="text-xs text-dark-subtle">{new Date(m.date).toLocaleDateString()} at {m.time}</p>
                  </div>
                </Link>
              ))}
            </div>
          </GlassCard>
        )}
      </section>
    </PageTransition>
  )
}
