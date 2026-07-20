import { useState, useEffect } from 'react'
import { Link } from 'react-router-dom'
import PageTransition from '../../components/motion/PageTransition'
import GlassCard from '../../components/ui/GlassCard'
import api from '../../lib/axios'

export default function MyRegistrations() {
  const [registrations, setRegistrations] = useState([])
  const [loading, setLoading] = useState(true)

  useEffect(() => {
    (async () => {
      try {
        const { data } = await api.get('/registrations/my')
        setRegistrations(data.registrations || [])
      } catch { } finally { setLoading(false) }
    })()
  }, [])

  return (
    <PageTransition>
      <section className="max-w-4xl mx-auto px-6 py-10">
        <Link to="/events" className="text-xs text-blue-accent hover:underline no-underline mb-4 inline-block">&larr; Back to Events</Link>
        <h1 className="text-2xl font-bold text-gradient mb-6">My Registrations</h1>

        {loading && <p className="text-dark-muted text-sm">Loading...</p>}
        {!loading && registrations.length === 0 && <p className="text-dark-subtle text-sm">No registrations yet.</p>}

        <div className="space-y-3">
          {registrations.map((r) => (
            <Link key={r._id} to={`/events/${r.event?._id}`} className="block no-underline">
              <GlassCard className="hover:bg-glass-hover transition-colors">
                <div className="flex items-center justify-between">
                  <div>
                    <p className="text-sm font-semibold text-dark-title">{r.event?.name || 'Unknown Event'}</p>
                    <p className="text-xs text-dark-subtle">
                      {r.event?.date ? new Date(r.event.date).toLocaleDateString() : ''} {r.event?.time ? `at ${r.event.time}` : ''}
                    </p>
                  </div>
                  <span className={`text-xs font-medium px-2 py-0.5 rounded-full border ${
                    r.status === 'registered' ? 'text-green-400 border-green-400/30' :
                    r.status === 'cancelled' ? 'text-red-400 border-red-400/30' :
                    'text-blue-accent border-blue-accent/30'
                  }`}>{r.status}</span>
                </div>
              </GlassCard>
            </Link>
          ))}
        </div>
      </section>
    </PageTransition>
  )
}
