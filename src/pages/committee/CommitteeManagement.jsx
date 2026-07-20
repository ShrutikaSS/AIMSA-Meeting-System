import { useState, useEffect } from 'react'
import { useNavigate } from 'react-router-dom'
import PageTransition from '../../components/motion/PageTransition'
import GlassCard from '../../components/ui/GlassCard'
import GradientButton from '../../components/ui/GradientButton'
import { useAuth } from '../../context/AuthContext'
import api from '../../lib/axios'

const DESIGNATIONS = ['President', 'Vice President', 'Secretary', 'Treasurer', 'Technical Head', 'Event Coordinator', 'PRO']

export default function CommitteeManagement() {
  const { user } = useAuth()
  const navigate = useNavigate()
  const [members, setMembers] = useState([])
  const [users, setUsers] = useState([])
  const [loading, setLoading] = useState(true)
  const [showForm, setShowForm] = useState(false)
  const [form, setForm] = useState({ userId: '', designation: 'President', termEnd: '' })
  const [editId, setEditId] = useState(null)
  const [editDesignation, setEditDesignation] = useState('')
  const [error, setError] = useState(null)

  useEffect(() => {
    if (user?.role !== 'Administrator') { navigate('/'); return }
    loadData()
  }, [user, navigate])

  const loadData = async () => {
    try {
      const [mRes, uRes] = await Promise.all([
        api.get('/committee'),
        api.get('/users'),
      ])
      setMembers(mRes.data.members || [])
      setUsers(uRes.data.users || [])
    } catch (err) {
      setError('Failed to load data')
    } finally {
      setLoading(false)
    }
  }

  const handleAdd = async (e) => {
    e.preventDefault()
    setError(null)
    try {
      await api.post('/committee', form)
      setShowForm(false)
      setForm({ userId: '', designation: 'President', termEnd: '' })
      loadData()
    } catch (err) {
      setError(err.response?.data?.message || 'Failed to add member')
    }
  }

  const handleUpdateDesignation = async (id) => {
    try {
      await api.put(`/committee/${id}`, { designation: editDesignation })
      setEditId(null)
      loadData()
    } catch (err) {
      alert(err.response?.data?.message || 'Update failed')
    }
  }

  const handleRemove = async (id) => {
    if (!window.confirm('Remove this member from committee?')) return
    try {
      await api.delete(`/committee/${id}`)
      loadData()
    } catch (err) {
      alert(err.response?.data?.message || 'Remove failed')
    }
  }

  const availableUsers = users.filter(
    (u) => !members.some((m) => m.user?._id === u._id) && u.role !== 'Administrator'
  )

  if (loading) return <div className="max-w-4xl mx-auto px-6 py-10 text-dark-muted text-sm">Loading...</div>

  return (
    <PageTransition>
      <section className="max-w-4xl mx-auto px-6 py-10">
        <div className="flex items-center justify-between mb-8">
          <h1 className="text-2xl font-bold text-gradient">Committee Management</h1>
          <button onClick={() => setShowForm(!showForm)} className="text-sm px-4 py-2 rounded-2xl bg-gradient-accent text-white font-semibold cursor-pointer border-0">
            {showForm ? 'Cancel' : '+ Add Member'}
          </button>
        </div>

        {error && <p className="text-red-400 text-sm mb-4">{error}</p>}

        {showForm && (
          <GlassCard glow className="mb-6">
            <form onSubmit={handleAdd} className="grid grid-cols-1 sm:grid-cols-3 gap-4">
              <div>
                <label className="block text-xs text-dark-muted mb-1">User *</label>
                <select value={form.userId} onChange={(e) => setForm({ ...form, userId: e.target.value })} required className="w-full px-3 py-2 rounded-2xl bg-warm-card border border-glass-border text-dark-title text-sm focus:outline-none focus:border-blue-accent">
                  <option value="">Select user</option>
                  {availableUsers.map((u) => <option key={u._id} value={u._id}>{u.name} ({u.role})</option>)}
                </select>
              </div>
              <div>
                <label className="block text-xs text-dark-muted mb-1">Designation *</label>
                <select value={form.designation} onChange={(e) => setForm({ ...form, designation: e.target.value })} className="w-full px-3 py-2 rounded-2xl bg-warm-card border border-glass-border text-dark-title text-sm focus:outline-none focus:border-blue-accent">
                  {DESIGNATIONS.map((d) => <option key={d} value={d}>{d}</option>)}
                </select>
              </div>
              <div>
                <label className="block text-xs text-dark-muted mb-1">Term End (optional)</label>
                <input type="date" value={form.termEnd} onChange={(e) => setForm({ ...form, termEnd: e.target.value })} className="w-full px-3 py-2 rounded-2xl bg-warm-card border border-glass-border text-dark-title text-sm focus:outline-none focus:border-blue-accent" />
              </div>
              <div className="sm:col-span-3">
                <GradientButton type="submit" className="text-sm">Add to Committee</GradientButton>
              </div>
            </form>
          </GlassCard>
        )}

        {members.length === 0 && <p className="text-dark-subtle text-sm">No committee members yet.</p>}

        <div className="space-y-3">
          {members.map((m) => (
            <GlassCard key={m._id}>
              <div className="flex items-center justify-between gap-3">
                <div>
                  <p className="text-sm font-semibold text-dark-title">{m.user?.name || 'Unknown'}</p>
                  <p className="text-xs text-dark-subtle">{m.user?.email} · {m.user?.role}</p>
                </div>
                <div className="text-right shrink-0">
                  {editId === m._id ? (
                    <div className="flex gap-2 items-center">
                      <select value={editDesignation} onChange={(e) => setEditDesignation(e.target.value)} className="px-2 py-1 rounded-xl bg-warm-card border border-glass-border text-dark-title text-xs focus:outline-none">
                        {DESIGNATIONS.map((d) => <option key={d} value={d}>{d}</option>)}
                      </select>
                      <button onClick={() => handleUpdateDesignation(m._id)} className="text-xs px-2 py-1 rounded-lg bg-green-500/20 text-green-400 border border-green-500/30 cursor-pointer bg-transparent">Save</button>
                      <button onClick={() => setEditId(null)} className="text-xs px-2 py-1 rounded-lg border border-glass-border text-dark-muted cursor-pointer bg-transparent">Cancel</button>
                    </div>
                  ) : (
                    <div className="flex gap-2 items-center">
                      <span className="text-xs font-medium text-amber-400 border border-amber-400/30 px-2 py-0.5 rounded-full">{m.designation}</span>
                      <button onClick={() => { setEditId(m._id); setEditDesignation(m.designation) }} className="text-xs px-2 py-1 rounded-lg border border-glass-border text-dark-muted hover:text-dark-title transition-colors cursor-pointer bg-transparent">Edit</button>
                      <button onClick={() => handleRemove(m._id)} className="text-xs px-2 py-1 rounded-lg border border-red-400/30 text-red-400 hover:bg-red-400/10 transition-colors cursor-pointer bg-transparent">Remove</button>
                    </div>
                  )}
                </div>
              </div>
              {m.termEnd && <p className="text-[10px] text-dark-subtle mt-1">Term ends: {new Date(m.termEnd).toLocaleDateString()}</p>}
            </GlassCard>
          ))}
        </div>
      </section>
    </PageTransition>
  )
}
