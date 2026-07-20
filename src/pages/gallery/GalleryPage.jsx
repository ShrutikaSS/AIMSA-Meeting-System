import { useState, useEffect } from 'react'
import PageTransition from '../../components/motion/PageTransition'
import GlassCard from '../../components/ui/GlassCard'
import GradientButton from '../../components/ui/GradientButton'
import { useAuth } from '../../context/AuthContext'
import api from '../../lib/axios'

const CAN_MANAGE = ['Administrator', 'Faculty Coordinator', 'Association President', 'Vice President']

export default function GalleryPage() {
  const { user } = useAuth()
  const [albums, setAlbums] = useState([])
  const [selected, setSelected] = useState(null)
  const [loading, setLoading] = useState(true)
  const [error, setError] = useState(null)
  const [showForm, setShowForm] = useState(false)
  const [albumForm, setAlbumForm] = useState({ name: '', description: '' })
  const [uploadItem, setUploadItem] = useState(null)
  const [itemTitle, setItemTitle] = useState('')
  const [submitting, setSubmitting] = useState(false)

  const canManage = CAN_MANAGE.includes(user?.role)

  const fetchAlbums = async () => {
    setLoading(true); setError(null)
    try {
      const { data } = await api.get('/gallery')
      setAlbums(data.albums || [])
    } catch (err) { setError(err.response?.data?.message || 'Failed') }
    finally { setLoading(false) }
  }

  useEffect(() => { fetchAlbums() }, [])

  const openAlbum = async (id) => {
    try {
      const { data } = await api.get(`/gallery/${id}`)
      setSelected(data.album)
    } catch (_) {}
  }

  const handleCreateAlbum = async (e) => {
    e.preventDefault(); if (!albumForm.name) return
    setSubmitting(true)
    try {
      await api.post('/gallery', albumForm)
      setAlbumForm({ name: '', description: '' })
      setShowForm(false)
      fetchAlbums()
    } catch (err) { alert(err.response?.data?.message || 'Failed') }
    finally { setSubmitting(false) }
  }

  const handleUploadItem = async () => {
    if (!uploadItem && !itemTitle) return
    setSubmitting(true)
    try {
      const fd = new FormData()
      if (uploadItem) fd.append('file', uploadItem)
      if (itemTitle) fd.append('title', itemTitle)
      fd.append('type', uploadItem?.type?.startsWith('video') ? 'video' : 'image')
      await api.post(`/gallery/${selected._id}/items`, fd, { headers: { 'Content-Type': 'multipart/form-data' } })
      setUploadItem(null); setItemTitle('')
      openAlbum(selected._id)
    } catch (err) { alert(err.response?.data?.message || 'Upload failed') }
    finally { setSubmitting(false) }
  }

  const handleDeleteItem = async (itemId) => {
    if (!window.confirm('Delete this item?')) return
    try {
      await api.delete(`/gallery/${selected._id}/items/${itemId}`)
      openAlbum(selected._id)
    } catch (err) { alert(err.response?.data?.message || 'Delete failed') }
  }

  const handleDeleteAlbum = async (id) => {
    if (!window.confirm('Delete this entire album?')) return
    try {
      await api.delete(`/gallery/${id}`)
      setSelected(null)
      fetchAlbums()
    } catch (err) { alert(err.response?.data?.message || 'Delete failed') }
  }

  if (selected) {
    return (
      <PageTransition>
        <section className="max-w-6xl mx-auto px-6 py-10">
          <button onClick={() => setSelected(null)}
            className="text-xs text-blue-accent hover:text-blue-glow mb-4 bg-transparent border-0 cursor-pointer">&larr; Back to Albums</button>
          <div className="flex items-center justify-between mb-6">
            <div>
              <h1 className="text-xl font-bold text-gradient">{selected.name}</h1>
              {selected.description && <p className="text-xs text-dark-muted mt-1">{selected.description}</p>}
              <p className="text-[10px] text-dark-subtle mt-1">{selected.items?.length || 0} items</p>
            </div>
            {canManage && user?.role === 'Administrator' && (
              <button onClick={() => handleDeleteAlbum(selected._id)}
                className="text-xs px-3 py-1.5 rounded-xl border border-red-400/30 text-red-400 hover:bg-red-400/10 bg-transparent cursor-pointer">Delete Album</button>
            )}
          </div>

          {canManage && (
            <GlassCard className="mb-6">
              <div className="flex flex-wrap items-end gap-3">
                <div>
                  <label className="block text-[10px] text-dark-muted mb-1">Upload Image/Video</label>
                  <input type="file" accept="image/*,video/*" onChange={(e) => setUploadItem(e.target.files[0])}
                    className="text-xs text-dark-muted file:mr-2 file:py-1 file:px-3 file:rounded-xl file:border-0 file:text-[10px] file:font-semibold file:bg-orange-accent file:text-white file:cursor-pointer" />
                </div>
                <div>
                  <input type="text" placeholder="Title (optional)" value={itemTitle}
                    onChange={(e) => setItemTitle(e.target.value)}
                    className="bg-warm-card border border-glass-border rounded-xl px-3 py-1.5 text-xs text-dark-title outline-none focus:border-orange-accent/50 w-40" />
                </div>
                <GradientButton onClick={handleUploadItem} disabled={submitting} className="text-[11px]">{submitting ? '...' : 'Upload'}</GradientButton>
              </div>
            </GlassCard>
          )}

          <div className="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">
            {selected.items?.map((item) => (
              <div key={item._id} className="relative group rounded-xl overflow-hidden border border-glass-border bg-warm-card">
                {item.type === 'video' ? (
                  <video src={`http://localhost:5000${item.url}`} controls className="w-full h-40 object-cover" />
                ) : (
                  <img src={`http://localhost:5000${item.url}`} alt={item.title || ''} className="w-full h-40 object-cover" />
                )}
                <div className="p-2">
                  {item.title && <p className="text-[11px] text-dark-body truncate">{item.title}</p>}
                  <p className="text-[9px] text-gray-600">{(item.uploadedBy && item.uploadedBy.name) || ''}</p>
                </div>
                {canManage && (
                  <button onClick={() => handleDeleteItem(item._id)}
                    className="absolute top-2 right-2 w-6 h-6 rounded-full bg-black/60 text-dark-title text-xs opacity-0 group-hover:opacity-100 transition-opacity border-0 cursor-pointer hover:bg-red-500/80">&times;</button>
                )}
              </div>
            ))}
          </div>
        </section>
      </PageTransition>
    )
  }

  return (
    <PageTransition>
      <section className="max-w-6xl mx-auto px-6 py-10">
        <div className="flex items-center justify-between mb-8">
          <h1 className="text-2xl font-bold text-gradient">Photo Gallery</h1>
          {canManage && (
            <GradientButton onClick={() => setShowForm(!showForm)} className="text-sm">
              {showForm ? 'Cancel' : '+ New Album'}
            </GradientButton>
          )}
        </div>

        {showForm && (
          <GlassCard className="mb-8">
            <form onSubmit={handleCreateAlbum} className="space-y-3">
              <div>
                <label className="block text-xs text-dark-muted mb-1">Album Name *</label>
                <input type="text" value={albumForm.name} onChange={(e) => setAlbumForm({ ...albumForm, name: e.target.value })}
                  className="w-full bg-warm-card border border-glass-border rounded-xl px-3 py-2 text-sm text-dark-title outline-none focus:border-orange-accent/50" required />
              </div>
              <div>
                <label className="block text-xs text-dark-muted mb-1">Description</label>
                <textarea value={albumForm.description} onChange={(e) => setAlbumForm({ ...albumForm, description: e.target.value })}
                  className="w-full bg-warm-card border border-glass-border rounded-xl px-3 py-2 text-sm text-dark-title outline-none focus:border-orange-accent/50" rows={2} />
              </div>
              <GradientButton type="submit" disabled={submitting} className="text-sm">{submitting ? '...' : 'Create Album'}</GradientButton>
            </form>
          </GlassCard>
        )}

        {loading && <p className="text-dark-muted text-sm">Loading...</p>}
        {error && <p className="text-red-400 text-sm">{error}</p>}
        {!loading && !error && albums.length === 0 && <p className="text-dark-subtle text-sm">No albums yet.</p>}

        <div className="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-6">
          {albums.map((a) => (
            <GlassCard key={a._id} className="hover:bg-glass-hover transition-colors cursor-pointer" onClick={() => openAlbum(a._id)}>
              <div className="h-40 rounded-xl overflow-hidden mb-3 bg-warm-card flex items-center justify-center">
                {a.coverImage ? (
                  <img src={`http://localhost:5000${a.coverImage}`} alt={a.name} className="w-full h-full object-cover" />
                ) : (
                  <svg className="w-12 h-12 text-gray-600" fill="none" stroke="currentColor" strokeWidth="1.5" viewBox="0 0 24 24"><rect x="3" y="3" width="18" height="18" rx="2" ry="2"/><circle cx="8.5" cy="8.5" r="1.5"/><polyline points="21 15 16 10 5 21"/></svg>
                )}
              </div>
              <h3 className="text-sm font-semibold text-dark-title">{a.name}</h3>
              {a.description && <p className="text-[11px] text-dark-muted mt-1 truncate">{a.description}</p>}
              <div className="flex items-center justify-between mt-2 text-[10px] text-dark-subtle">
                <span>{a.itemCount} items</span>
                <span>{new Date(a.createdAt).toLocaleDateString()}</span>
              </div>
            </GlassCard>
          ))}
        </div>
      </section>
    </PageTransition>
  )
}
