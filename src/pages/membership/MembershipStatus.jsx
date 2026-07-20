import { useState, useEffect } from 'react'
import { Link } from 'react-router-dom'
import PageTransition from '../../components/motion/PageTransition'
import GlassCard from '../../components/ui/GlassCard'
import GradientButton from '../../components/ui/GradientButton'
import { useAuth } from '../../context/AuthContext'
import api from '../../lib/axios'

export default function MembershipStatus() {
  const { user } = useAuth()
  const [membership, setMembership] = useState(null)
  const [loading, setLoading] = useState(true)

  useEffect(() => {
    api.get('/membership/my').then(({ data }) => {
      setMembership(data.membership)
    }).catch(() => {}).finally(() => setLoading(false))
  }, [])

  if (loading) return <div className="max-w-lg mx-auto px-6 py-10 text-dark-muted text-sm">Loading...</div>

  const statusColors = {
    pending: 'text-amber-400 border-amber-400/30 bg-amber-400/10',
    approved: 'text-green-400 border-green-400/30 bg-green-400/10',
    rejected: 'text-red-400 border-red-400/30 bg-red-400/10',
    expired: 'text-dark-muted border-gray-500/30 bg-gray-500/10',
  }

  return (
    <PageTransition>
      <section className="max-w-lg mx-auto px-6 py-10">
        <h1 className="text-2xl font-bold text-gradient mb-6">My Membership</h1>

        {!membership ? (
          <GlassCard className="text-center">
            <p className="text-sm text-dark-muted mb-4">You haven't applied for membership yet.</p>
            <Link to="/membership/apply">
              <GradientButton className="text-sm">Apply Now</GradientButton>
            </Link>
          </GlassCard>
        ) : (
          <GlassCard glow className="space-y-4">
            <div className="text-center">
              {membership.photo && (
                <img src={membership.photo} alt="Profile" className="w-24 h-24 rounded-full mx-auto object-cover border-2 border-glass-border mb-3" />
              )}
              <p className="text-sm font-semibold text-dark-title">{user?.name}</p>
              <p className="text-xs text-dark-subtle">{user?.email}</p>
              <span className={`inline-block mt-2 text-xs font-semibold px-3 py-1 rounded-full border ${statusColors[membership.status] || ''}`}>
                {membership.status}
              </span>
            </div>

            <hr className="border-glass-border" />

            <div className="text-xs space-y-2">
              <div className="flex justify-between"><span className="text-dark-subtle">Applied:</span><span className="text-dark-body">{new Date(membership.appliedAt).toLocaleDateString()}</span></div>
              {membership.renewedAt && <div className="flex justify-between"><span className="text-dark-subtle">Last Renewed:</span><span className="text-dark-body">{new Date(membership.renewedAt).toLocaleDateString()}</span></div>}
              {membership.approvedBy && <div className="flex justify-between"><span className="text-dark-subtle">Approved by:</span><span className="text-dark-body">{membership.approvedBy.name}</span></div>}
              {membership.notes && <div className="flex justify-between"><span className="text-dark-subtle">Notes:</span><span className="text-dark-body">{membership.notes}</span></div>}
            </div>

            {membership.status === 'approved' && (
              <div className="pt-2">
                <Link to="/membership/apply">
                  <button className="w-full text-xs px-4 py-2 rounded-2xl border border-amber-400/30 text-amber-400 hover:bg-amber-400/10 transition-colors cursor-pointer bg-transparent">Renew Membership</button>
                </Link>
              </div>
            )}
          </GlassCard>
        )}
      </section>
    </PageTransition>
  )
}
