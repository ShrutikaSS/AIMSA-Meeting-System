import { useState, useEffect, useRef } from 'react'
import PageTransition from '../../components/motion/PageTransition'
import GlassCard from '../../components/ui/GlassCard'
import GradientButton from '../../components/ui/GradientButton'
import api from '../../lib/axios'

const REPORT_TYPES = [
  { key: 'member', label: 'Member Report', desc: 'All registered users by role' },
  { key: 'event', label: 'Event Report', desc: 'Events with status, category, registrations' },
  { key: 'attendance', label: 'Attendance Report', desc: 'Meeting attendance records' },
  { key: 'achievement', label: 'Achievement Report', desc: 'Student achievements with review status' },
  { key: 'certificate', label: 'Certificate Report', desc: 'All issued certificates' },
  { key: 'committee', label: 'Committee Report', desc: 'Committee members and designations' },
  { key: 'participation', label: 'Participation Stats', desc: 'Member event participation analysis' },
]

export default function ReportsPage() {
  const [selectedType, setSelectedType] = useState('member')
  const [reportData, setReportData] = useState(null)
  const [loading, setLoading] = useState(false)
  const [error, setError] = useState(null)
  const [filters, setFilters] = useState({})
  const printRef = useRef(null)

  const generateReport = async (format) => {
    setLoading(true); setError(null)
    try {
      const params = new URLSearchParams({ type: selectedType })
      if (format) params.set('format', format)
      Object.entries(filters).forEach(([k, v]) => { if (v) params.set(k, v) })

      if (format === 'pdf') {
        const token = localStorage.getItem('token')
        window.open(`http://localhost:5000/api/reports?${params}&token=${token}`, '_blank')
        setLoading(false)
        return
      }
      if (format === 'excel') {
        const token = localStorage.getItem('token')
        window.open(`http://localhost:5000/api/reports?${params}&token=${token}`, '_blank')
        setLoading(false)
        return
      }

      const { data } = await api.get('/reports?' + params.toString())
      setReportData(data.report)
    } catch (err) { setError(err.response?.data?.message || 'Failed') }
    finally { setLoading(false) }
  }

  const handlePrint = () => {
    const win = window.open('', '_blank')
    if (!win) return
    const html = buildPrintHTML()
    win.document.write(html)
    win.document.close()
    win.focus()
    setTimeout(() => win.print(), 500)
  }

  const buildPrintHTML = () => {
    if (!reportData) return ''
    const thead = reportData.headers.map((h) => '<th style="text-align:left;padding:8px 10px;border-bottom:2px solid #f97316;color:#f97316;font-size:12px;">' + h + '</th>').join('')
    const tbody = reportData.rows.map((row) =>
      '<tr>' + row.map((c) => '<td style="padding:6px 10px;border-bottom:1px solid #333;font-size:11px;color:#d1d5db;">' + (c || '') + '</td>').join('') + '</tr>'
    ).join('')
    return '<!DOCTYPE html><html><head><title>' + reportData.title + '</title><style>' +
      'body{font-family:Arial,sans-serif;background:#0d1117;padding:30px;color:#d1d5db;}' +
      'h1{color:#f97316;text-align:center;}h3{color:#3b82f6;text-align:center;font-weight:400;}' +
      '.meta{text-align:center;color:#6b7280;font-size:12px;margin-bottom:20px;}' +
      'table{width:100%;border-collapse:collapse;margin-top:15px;}' +
      'th{background:#111827;}tr:nth-child(even){background:#1a1f2e;}' +
      '.counts{display:flex;gap:15px;justify-content:center;margin-bottom:15px;}' +
      '.counts span{background:#1a1f2e;padding:8px 16px;border-radius:8px;font-size:12px;border:1px solid #333;}' +
      '.footer{text-align:center;color:#4b5563;font-size:10px;margin-top:30px;}' +
      '@media print{body{background:white;color:#333;padding:15px;}h1{color:#f97316;}td{color:#333;border-color:#ddd;}' +
      'th{color:#f97316;}tr:nth-child(even){background:#f9fafb;}.meta{color:#6b7280;}.footer{color:#9ca3af;}}</style></head><body>' +
      '<h1>AIMSA Report</h1><h3>' + reportData.title + '</h3>' +
      '<div class="meta">Generated: ' + new Date(reportData.generatedAt).toLocaleString() + '</div>' +
      (reportData.counts ? '<div class="counts">' + Object.entries(reportData.counts).map(([k, v]) => '<span><strong>' + k.replace(/([A-Z])/g, ' $1').replace(/^./, (s) => s.toUpperCase()) + ':</strong> ' + v + '</span>').join('') + '</div>' : '') +
      '<table><thead><tr>' + thead + '</tr></thead><tbody>' + tbody + '</tbody></table>' +
      '<div class="footer">Zeal College of Engineering & Research — AIMSA Portal</div></body></html>'
  }

  const rt = REPORT_TYPES.find((r) => r.key === selectedType)

  return (
    <PageTransition>
      <section className="max-w-7xl mx-auto px-6 py-10">
        <h1 className="text-2xl font-bold text-gradient mb-8">Reports</h1>

        <div className="grid grid-cols-1 lg:grid-cols-4 gap-6">
          <div className="lg:col-span-1 space-y-2">
            {REPORT_TYPES.map((r) => (
              <button key={r.key} onClick={() => { setSelectedType(r.key); setReportData(null); setError(null) }}
                className={`w-full text-left px-4 py-3 rounded-xl border transition-colors bg-transparent cursor-pointer ${selectedType === r.key ? 'border-orange-accent bg-orange-accent/5' : 'border-glass-border hover:bg-glass-hover'}`}>
                <p className={`text-xs font-semibold ${selectedType === r.key ? 'text-orange-accent' : 'text-dark-body'}`}>{r.label}</p>
                <p className="text-[10px] text-dark-subtle mt-0.5">{r.desc}</p>
              </button>
            ))}
          </div>

          <div className="lg:col-span-3">
            <GlassCard>
              <div className="flex items-center justify-between mb-6">
                <h3 className="text-sm font-semibold text-dark-title">{rt?.label || 'Select a report'}</h3>
                <div className="flex gap-2">
                  <GradientButton onClick={() => generateReport('')} disabled={loading} className="text-[11px]">Preview</GradientButton>
                  <button onClick={() => generateReport('pdf')} disabled={loading}
                    className="text-[11px] px-3 py-1.5 rounded-xl border border-blue-accent/30 text-blue-accent hover:bg-blue-accent/10 bg-transparent cursor-pointer disabled:opacity-40">PDF</button>
                  <button onClick={() => generateReport('excel')} disabled={loading}
                    className="text-[11px] px-3 py-1.5 rounded-xl border border-green-400/30 text-green-400 hover:bg-green-400/10 bg-transparent cursor-pointer disabled:opacity-40">Excel</button>
                  <button onClick={handlePrint} disabled={!reportData}
                    className="text-[11px] px-3 py-1.5 rounded-xl border border-purple-400/30 text-purple-400 hover:bg-purple-400/10 bg-transparent cursor-pointer disabled:opacity-40">Print</button>
                </div>
              </div>

              {selectedType === 'event' && (
                <div className="flex gap-2 mb-4">
                  {['all', 'published', 'draft', 'completed', 'cancelled'].map((s) => (
                    <button key={s} onClick={() => setFilters({ ...filters, status: s === 'all' ? '' : s })}
                      className={`text-[10px] px-2 py-1 rounded-lg border bg-transparent cursor-pointer ${(filters.status || '') === (s === 'all' ? '' : s) ? 'border-orange-accent text-orange-accent' : 'border-glass-border text-dark-muted'}`}>{s.charAt(0).toUpperCase() + s.slice(1)}</button>
                  ))}
                </div>
              )}
              {selectedType === 'achievement' && (
                <div className="flex gap-2 mb-4">
                  {['all', 'pending', 'approved', 'rejected'].map((s) => (
                    <button key={s} onClick={() => setFilters({ ...filters, status: s === 'all' ? '' : s })}
                      className={`text-[10px] px-2 py-1 rounded-lg border bg-transparent cursor-pointer ${(filters.status || '') === (s === 'all' ? '' : s) ? 'border-orange-accent text-orange-accent' : 'border-glass-border text-dark-muted'}`}>{s.charAt(0).toUpperCase() + s.slice(1)}</button>
                  ))}
                </div>
              )}
              {selectedType === 'member' && (
                <div className="flex gap-2 mb-4">
                  {['all', 'Student Member', 'Faculty Coordinator', 'Administrator'].map((r) => (
                    <button key={r} onClick={() => setFilters({ ...filters, role: r === 'all' ? '' : r })}
                      className={`text-[10px] px-2 py-1 rounded-lg border bg-transparent cursor-pointer ${(filters.role || '') === (r === 'all' ? '' : r) ? 'border-orange-accent text-orange-accent' : 'border-glass-border text-dark-muted'}`}>{r === 'all' ? 'All Roles' : r}</button>
                  ))}
                </div>
              )}

              {loading && <p className="text-dark-muted text-sm py-8">Generating...</p>}
              {error && <p className="text-red-400 text-sm">{error}</p>}

              {reportData && !loading && (
                <div ref={printRef}>
                  {reportData.counts && Object.keys(reportData.counts).length > 0 && (
                    <div className="flex flex-wrap gap-3 mb-6">
                      {Object.entries(reportData.counts).map(([k, v]) => (
                        <div key={k} className="bg-warm-card border border-glass-border rounded-xl px-4 py-2">
                          <p className="text-[10px] text-dark-subtle uppercase">{k.replace(/([A-Z])/g, ' $1').trim()}</p>
                          <p className="text-lg font-bold text-dark-title">{v}</p>
                        </div>
                      ))}
                    </div>
                  )}

                  <div className="overflow-x-auto">
                    <table className="w-full text-xs">
                      <thead>
                        <tr className="border-b border-glass-border">
                          {reportData.headers.map((h, i) => (
                            <th key={i} className="text-left py-3 px-3 text-dark-muted font-semibold text-[10px] uppercase tracking-wider">{h}</th>
                          ))}
                        </tr>
                      </thead>
                      <tbody>
                        {reportData.rows.map((row, ri) => (
                          <tr key={ri} className="border-b border-glass-border hover:bg-glass-hover transition-colors">
                            {row.map((cell, ci) => (
                              <td key={ci} className="py-2.5 px-3 text-dark-body text-[11px]">{cell}</td>
                            ))}
                          </tr>
                        ))}
                      </tbody>
                    </table>
                  </div>
                  <p className="text-[10px] text-gray-600 mt-4">{reportData.rows.length} records</p>
                </div>
              )}

              {!reportData && !loading && !error && (
                <p className="text-dark-subtle text-sm py-8 text-center">Select a report type and click Preview to generate.</p>
              )}
            </GlassCard>
          </div>
        </div>
      </section>
    </PageTransition>
  )
}
