import { useState, useEffect } from 'react'
import PageTransition from '../../components/motion/PageTransition'
import GlassCard from '../../components/ui/GlassCard'
import { useAuth } from '../../context/AuthContext'
import api from '../../lib/axios'

const TYPE_COLORS = {
  Participation: 'border-blue-accent/30 text-blue-accent',
  Volunteer: 'border-green-400/30 text-green-400',
  Winner: 'border-yellow-400/30 text-yellow-400',
  Appreciation: 'border-purple-400/30 text-purple-400',
}

export default function MyCertificates() {
  const { user } = useAuth()
  const [certificates, setCertificates] = useState([])
  const [loading, setLoading] = useState(true)
  const [error, setError] = useState(null)

  useEffect(() => {
    (async () => {
      setLoading(true)
      setError(null)
      try {
        const { data } = await api.get('/certificates')
        setCertificates(data.certificates || [])
      } catch (err) {
        setError(err.response?.data?.message || 'Failed to load certificates')
      } finally {
        setLoading(false)
      }
    })()
  }, [])

  const handleDownload = (id) => {
    const token = localStorage.getItem('token')
    window.open(`http://localhost:5000/api/certificates/${id}/download?token=${token}`, '_blank')
  }

  return (
    <PageTransition>
      <section className="max-w-4xl mx-auto px-6 py-10">
        <h1 className="text-2xl font-bold text-gradient mb-8">My Certificates</h1>

        {loading && <p className="text-dark-muted text-sm">Loading...</p>}
        {error && <p className="text-red-400 text-sm">{error}</p>}
        {!loading && !error && certificates.length === 0 && (
          <p className="text-dark-subtle text-sm">No certificates yet.</p>
        )}

        <div className="grid grid-cols-1 sm:grid-cols-2 gap-4">
          {certificates.map((c) => (
            <GlassCard key={c._id} className="hover:bg-glass-hover transition-colors">
              <div className="flex flex-col h-full">
                <div className="flex items-center gap-2 mb-2">
                  <span className={`text-[10px] font-semibold border px-1.5 py-0.5 rounded ${TYPE_COLORS[c.type] || ''}`}>{c.type}</span>
                </div>
                <h3 className="text-dark-title font-semibold text-sm mb-1">{c.title}</h3>
                {c.description && <p className="text-xs text-dark-muted mb-2">{c.description}</p>}
                {c.event && <p className="text-[10px] text-dark-subtle">Event: {c.event.name}</p>}
                <div className="mt-auto pt-2 flex items-center justify-between">
                  <div className="text-[10px] text-dark-subtle">
                    <div>Issued: {new Date(c.issueDate).toLocaleDateString()}</div>
                    <div>Cert#: {c.certificateNumber}</div>
                  </div>
                  <button onClick={() => handleDownload(c._id)}
                    className="text-xs px-3 py-1.5 rounded-xl bg-orange-accent text-white hover:bg-orange-accent/80 transition-colors cursor-pointer border-0">Download PDF</button>
                </div>
              </div>
            </GlassCard>
          ))}
        </div>
      </section>
    </PageTransition>
  )
}
