import { useState, useEffect } from 'react'
import { useParams, Link } from 'react-router-dom'
import PageTransition from '../../components/motion/PageTransition'
import GlassCard from '../../components/ui/GlassCard'
import api from '../../lib/axios'

export default function Confirmation() {
  const { id } = useParams()
  const [data, setData] = useState(null)
  const [loading, setLoading] = useState(true)

  useEffect(() => {
    (async () => {
      try {
        const { data: res } = await api.get(`/registrations/${id}/confirmation`)
        setData(res.registration)
      } catch { } finally { setLoading(false) }
    })()
  }, [id])

  if (loading) return <div className="max-w-lg mx-auto px-6 py-10 text-dark-muted text-sm">Loading...</div>
  if (!data) return <div className="max-w-lg mx-auto px-6 py-10 text-dark-subtle text-sm">No confirmation found.</div>

  return (
    <PageTransition>
      <section className="max-w-lg mx-auto px-6 py-10">
        <Link to={`/events/${id}`} className="text-xs text-blue-accent hover:underline no-underline mb-4 inline-block">&larr; Back to Event</Link>

        <GlassCard glow className="text-center p-8">
          <div className="w-16 h-16 mx-auto mb-4 rounded-full bg-green-500/20 flex items-center justify-center">
            <svg className="w-8 h-8 text-green-400" fill="none" stroke="currentColor" strokeWidth="2" viewBox="0 0 24 24"><path d="M5 13l4 4L19 7"/></svg>
          </div>
          <h1 className="text-lg font-bold text-dark-title mb-2">Registration Confirmed</h1>
          <p className="text-xs text-dark-muted mb-6">Participation Confirmation</p>

          <div className="text-left space-y-2 text-sm border-t border-glass-border pt-4">
            <div className="flex justify-between"><span className="text-dark-subtle">Name:</span><span className="text-dark-title">{data.user?.name}</span></div>
            <div className="flex justify-between"><span className="text-dark-subtle">Email:</span><span className="text-dark-title">{data.user?.email}</span></div>
            <div className="flex justify-between"><span className="text-dark-subtle">Role:</span><span className="text-dark-title">{data.user?.role}</span></div>
            <div className="flex justify-between"><span className="text-dark-subtle">Event:</span><span className="text-dark-title">{data.event?.name}</span></div>
            <div className="flex justify-between"><span className="text-dark-subtle">Date:</span><span className="text-dark-title">{data.event?.date ? new Date(data.event.date).toLocaleDateString() : ''}</span></div>
            <div className="flex justify-between"><span className="text-dark-subtle">Venue:</span><span className="text-dark-title">{data.event?.venue || 'TBD'}</span></div>
            <div className="flex justify-between"><span className="text-dark-subtle">Status:</span><span className="text-green-400">{data.status}</span></div>
            <div className="flex justify-between"><span className="text-dark-subtle">Registered:</span><span className="text-dark-title">{data.registeredAt ? new Date(data.registeredAt).toLocaleString() : ''}</span></div>
          </div>
        </GlassCard>
      </section>
    </PageTransition>
  )
}
