import { useState, useEffect } from 'react'
import { useNavigate } from 'react-router-dom'
import PageTransition from '../../components/motion/PageTransition'
import GlassCard from '../../components/ui/GlassCard'
import { useAuth } from '../../context/AuthContext'
import api from '../../lib/axios'

export default function MemberManagement() {
  const { user } = useAuth()
  const navigate = useNavigate()
  const [memberships, setMemberships] = useState([])
  const [loading, setLoading] = useState(true)
  const [statusFilter, setStatusFilter] = useState('')
  const [searchQuery, setSearchQuery] = useState('')
  const [error, setError] = useState(null)

  useEffect(() => {
    if (!['Administrator', 'Faculty Coordinator'].includes(user?.role)) { navigate('/'); return }
    loadMemberships()
  }, [user, navigate, statusFilter, searchQuery])

  const loadMemberships = async () => {
    setLoading(true)
    try {
      const params = {}
      if (statusFilter) params.status = statusFilter
      if (searchQuery) params.search = searchQuery
      const { data } = await api.get('/membership/all', { params })
      setMemberships(data.memberships || [])
    } catch (err) {
      setError(err.response?.data?.message || 'Failed to load memberships')
    } finally {
      setLoading(false)
    }
  }

  const handleApprove = async (id) => {
    try {
      await api.put(`/membership/${id}/approve`)
      loadMemberships()
    } catch (err) {
      alert(err.response?.data?.message || 'Failed to approve')
    }
  }

  const handleReject = async (id) => {
    const notes = prompt('Reason for rejection (optional):')
    try {
      await api.put(`/membership/${id}/reject`, { notes: notes || undefined })
      loadMemberships()
    } catch (err) {
      alert(err.response?.data?.message || 'Failed to reject')
    }
  }

  const filters = ['', 'pending', 'approved', 'rejected', 'expired']

  return (
    <PageTransition>
      <section className="max-w-5xl mx-auto px-6 py-10">
        <h1 className="text-2xl font-bold text-gradient mb-8">Member Management</h1>

        <div className="flex flex-wrap items-center gap-3 mb-6">
          <div className="relative flex-1 min-w-[200px] max-w-xs">
            <svg className="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-dark-subtle" fill="none" stroke="currentColor" strokeWidth="2" viewBox="0 0 24 24"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.35-4.35"/></svg>
            <input type="text" value={searchQuery} onChange={(e) => setSearchQuery(e.target.value)}
              placeholder="Search members..." className="w-full pl-9 pr-3 py-2 rounded-xl bg-warm-card border border-glass-border text-sm text-dark-title outline-none focus:border-orange-accent/50 transition-colors" />
          </div>
        </div>

        <div className="flex gap-2 mb-6 flex-wrap">
          {filters.map((f) => (
            <button
              key={f}
              onClick={() => setStatusFilter(f)}
              className={`px-3 py-1.5 text-xs font-medium rounded-xl border transition-colors cursor-pointer ${
                statusFilter === f
                  ? 'bg-blue-accent/20 text-blue-accent border-blue-accent/40'
                  : 'bg-transparent text-dark-muted border-glass-border hover:text-dark-title hover:border-gray-500'
              }`}
            >
              {f || 'All'}
            </button>
          ))}
        </div>

        {error && <p className="text-red-400 text-sm mb-4">{error}</p>}
        {loading && <p className="text-dark-muted text-sm">Loading...</p>}
        {!loading && memberships.length === 0 && <p className="text-dark-subtle text-sm">No memberships found.</p>}

        <div className="space-y-3">
          {memberships.map((m) => (
            <GlassCard key={m._id} glow={m.status === 'pending'}>
              <div className="flex items-center justify-between gap-3">
                <div className="min-w-0 flex-1">
                  <div className="flex items-center gap-2">
                    <p className="text-sm font-semibold text-dark-title truncate">{m.user?.name || 'Unknown'}</p>
                    <span className={`text-[10px] font-semibold px-2 py-0.5 rounded-full border ${
                      m.status === 'approved' ? 'text-green-400 border-green-400/30' :
                      m.status === 'pending' ? 'text-amber-400 border-amber-400/30' :
                      m.status === 'rejected' ? 'text-red-400 border-red-400/30' :
                      'text-dark-muted border-gray-500/30'
                    }`}>{m.status}</span>
                  </div>
                  <p className="text-xs text-dark-subtle">{m.user?.email} · {m.user?.role}</p>
                  <p className="text-[10px] text-dark-subtle">Applied: {new Date(m.appliedAt).toLocaleDateString()}</p>
                  {m.notes && <p className="text-[10px] text-red-400 mt-0.5">Note: {m.notes}</p>}
                </div>
                {user?.role === 'Administrator' && m.status === 'pending' && (
                  <div className="flex gap-2 shrink-0">
                    <button onClick={() => handleApprove(m._id)} className="text-xs px-3 py-1.5 rounded-xl bg-green-500/20 text-green-400 border border-green-500/30 hover:bg-green-500/30 transition-colors cursor-pointer bg-transparent">Approve</button>
                    <button onClick={() => handleReject(m._id)} className="text-xs px-3 py-1.5 rounded-xl bg-red-500/20 text-red-400 border border-red-500/30 hover:bg-red-500/30 transition-colors cursor-pointer bg-transparent">Reject</button>
                  </div>
                )}
                {m.status === 'approved' && m.approvedBy && (
                  <span className="text-[10px] text-dark-subtle shrink-0">by {m.approvedBy?.name || 'Admin'}</span>
                )}
              </div>
            </GlassCard>
          ))}
        </div>
      </section>
    </PageTransition>
  )
}
