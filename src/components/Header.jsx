import { useState, useEffect, useRef } from 'react'
import { Link, useLocation, useNavigate } from 'react-router-dom'
import { useAuth } from '../context/AuthContext'
import NotificationsPanel from './NotificationsPanel'
import useReducedMotion from '../hooks/useReducedMotion'
import { CollegeLogo, AimlLogo } from './Logo'

const roleBadgeColors = {
  Administrator: 'bg-red-500/20 text-red-400 border-red-500/30',
  'Faculty Coordinator': 'bg-purple-500/20 text-purple-400 border-purple-500/30',
  'Association President': 'bg-amber-500/20 text-amber-400 border-amber-500/30',
  'Vice President': 'bg-amber-500/20 text-amber-400 border-amber-500/30',
  'Committee Member': 'bg-blue-500/20 text-blue-400 border-blue-500/30',
  'Student Member': 'bg-green-500/20 text-green-400 border-green-500/30',
}

const roleDashboardMap = {
  Administrator: '/dashboard/administrator',
  'Faculty Coordinator': '/dashboard/faculty',
  'Association President': '/dashboard/president',
  'Vice President': '/dashboard/vice-president',
  'Committee Member': '/dashboard/committee',
  'Student Member': '/dashboard/student',
}

export default function Header() {
  const { user, logout } = useAuth()
  const { pathname } = useLocation()
  const navigate = useNavigate()
  const [scrolled, setScrolled] = useState(false)
  const [profileOpen, setProfileOpen] = useState(false)
  const [lang, setLang] = useState('en')
  const [searchOpen, setSearchOpen] = useState(false)
  const [notifOpen, setNotifOpen] = useState(false)
  const prefersReduced = useReducedMotion()
  const [searchQuery, setSearchQuery] = useState('')
  const [mobileOpen, setMobileOpen] = useState(false)
  const menuRef = useRef(null)

  const isLanding = pathname === '/'

  useEffect(() => {
    const onScroll = () => setScrolled(window.scrollY > 40)
    window.addEventListener('scroll', onScroll, { passive: true })
    return () => window.removeEventListener('scroll', onScroll)
  }, [])

  useEffect(() => {
    const handleClick = (e) => {
      if (menuRef.current && !menuRef.current.contains(e.target)) setProfileOpen(false)
    }
    document.addEventListener('mousedown', handleClick)
    return () => document.removeEventListener('mousedown', handleClick)
  }, [])

  useEffect(() => {
    setProfileOpen(false)
    setSearchOpen(false)
  }, [pathname])

  const headerBg = isLanding && !scrolled
    ? 'bg-transparent'
    : 'bg-warm/70 backdrop-blur-xl border-b border-glass-border'

  return (
    <header
      className={`fixed top-0 left-0 right-0 z-50 h-16 transition-all duration-300 ${headerBg}`}
    >
      <div className="max-w-7xl mx-auto px-4 sm:px-6 h-full flex items-center justify-between gap-3">
        {/* Left: Logos + Title */}
        <Link to={user ? (roleDashboardMap[user.role] || '/') : '/'} className="flex items-center gap-2 no-underline shrink-0">
          <CollegeLogo className="w-8 h-8 sm:w-9 sm:h-9" />
          <AimlLogo className="w-8 h-8 sm:w-9 sm:h-9" />
          <span className="hidden sm:inline text-base sm:text-lg font-bold text-gradient leading-none">
            AIMSA
          </span>
        </Link>

        {/* Center: Navigation */}
        <nav className="hidden md:flex items-center gap-5">
          {!user ? (
            <>
              <Link to="/" className={`text-sm font-medium transition-colors no-underline ${pathname === '/' ? 'text-blue-accent' : 'text-dark-muted hover:text-dark-title'}`}>Home</Link>
              <Link to="/login" className={`text-sm font-medium transition-colors no-underline ${pathname === '/login' ? 'text-blue-accent' : 'text-dark-muted hover:text-dark-title'}`}>Login</Link>
            </>
          ) : (
            <>
              <Link to="/announcements" className="text-sm font-medium text-dark-muted hover:text-dark-title transition-colors no-underline">Notices</Link>
              <Link to="/events" className="text-sm font-medium text-dark-muted hover:text-dark-title transition-colors no-underline">Events</Link>
              <Link to="/meetings" className="text-sm font-medium text-dark-muted hover:text-dark-title transition-colors no-underline">Meetings</Link>
              {user?.role === 'Student Member' && <Link to="/achievements" className="text-sm font-medium text-dark-muted hover:text-dark-title transition-colors no-underline">Achievements</Link>}
              {['Administrator', 'Faculty Coordinator', 'Association President', 'Vice President'].includes(user?.role) && <Link to="/achievements/review" className="text-sm font-medium text-dark-muted hover:text-dark-title transition-colors no-underline">Achievements</Link>}
              <Link to="/certificates" className="text-sm font-medium text-dark-muted hover:text-dark-title transition-colors no-underline">Certificates</Link>
              {['Administrator', 'Association President', 'Vice President', 'Faculty Coordinator'].includes(user?.role) && <Link to="/certificates/issue" className="text-sm font-medium text-dark-muted hover:text-dark-title transition-colors no-underline">Issue Cert</Link>}
              <Link to="/gallery" className="text-sm font-medium text-dark-muted hover:text-dark-title transition-colors no-underline">Gallery</Link>
              {['Administrator', 'Faculty Coordinator', 'Association President', 'Vice President'].includes(user?.role) && <Link to="/reports" className="text-sm font-medium text-dark-muted hover:text-dark-title transition-colors no-underline">Reports</Link>}
              {user?.role === 'Administrator' && <Link to="/committee" className="text-sm font-medium text-dark-muted hover:text-dark-title transition-colors no-underline">Committee</Link>}
              {['Administrator', 'Faculty Coordinator'].includes(user?.role) && <Link to="/membership/manage" className="text-sm font-medium text-dark-muted hover:text-dark-title transition-colors no-underline">Members</Link>}
              {user?.role === 'Student Member' && <Link to="/membership/status" className="text-sm font-medium text-dark-muted hover:text-dark-title transition-colors no-underline">Membership</Link>}
              {!pathname.startsWith('/dashboard') && (
                <Link to={roleDashboardMap[user.role] || '/'} className="text-sm font-medium text-blue-accent hover:text-blue-glow transition-colors no-underline">Dashboard</Link>
              )}
              <Link to="/" className="text-sm font-medium text-dark-muted hover:text-dark-title transition-colors no-underline">Home</Link>
            </>
          )}
        </nav>

        {/* Right: Search + Notifications + Profile + Lang */}
        <div className="flex items-center gap-2 sm:gap-3">

          {/* Search */}
          <div className="relative">
            <button
              onClick={() => setSearchOpen(!searchOpen)}
              className="text-dark-muted hover:text-dark-title transition-colors p-1.5 rounded-xl hover:bg-glass-hover cursor-pointer bg-transparent border-0"
              aria-label="Search"
            >
              <svg className="w-5 h-5" fill="none" stroke="currentColor" strokeWidth="2" viewBox="0 0 24 24"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.35-4.35"/></svg>
            </button>
            {searchOpen && (
              <div className="absolute right-0 top-full mt-2 w-72 glass p-2 shadow-soft">
                  <input
                    type="text"
                    value={searchQuery}
                    onChange={(e) => setSearchQuery(e.target.value)}
                    onKeyDown={(e) => {
                      if (e.key === 'Enter' && searchQuery.trim()) {
                        const q = encodeURIComponent(searchQuery.trim())
                        window.location.href = '/events?search=' + q
                        setSearchOpen(false)
                      }
                    }}
                    placeholder="Search events..."
                    className="w-full px-3 py-2 rounded-xl bg-warm-card border border-glass-border text-dark-title text-sm placeholder-dark-subtle focus:outline-none focus:border-blue-accent"
                    autoFocus
                  />
              </div>
            )}
          </div>

          {/* Notifications */}
          {user && (
            <div className="relative">
              <button onClick={() => setNotifOpen(!notifOpen)}
                className="relative text-dark-muted hover:text-dark-title transition-colors p-1.5 rounded-xl hover:bg-glass-hover cursor-pointer bg-transparent border-0" aria-label="Notifications">
                <svg className="w-5 h-5" fill="none" stroke="currentColor" strokeWidth="2" viewBox="0 0 24 24"><path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"/><path d="M13.73 21a2 2 0 0 1-3.46 0"/></svg>
                <span className="absolute -top-0.5 -right-0.5 w-2 h-2 bg-red-500 rounded-full" />
              </button>
              <NotificationsPanel open={notifOpen} onClose={() => setNotifOpen(false)} />
            </div>
          )}

          {/* Language Toggle */}
          <button
            onClick={() => setLang(lang === 'en' ? 'mr' : 'en')}
            className="text-xs font-semibold text-dark-muted hover:text-dark-title transition-colors px-2 py-1 rounded-lg border border-glass-border hover:bg-glass-hover cursor-pointer bg-transparent"
            aria-label="Toggle language"
          >
            {lang === 'en' ? 'EN' : 'मराठी'}
          </button>

          {/* Mobile Menu Toggle */}
          <button
            onClick={() => setMobileOpen(!mobileOpen)}
            className="md:hidden text-dark-muted hover:text-dark-title transition-colors p-1.5 rounded-xl hover:bg-glass-hover cursor-pointer bg-transparent border-0"
            aria-label="Menu"
          >
            <svg className="w-5 h-5" fill="none" stroke="currentColor" strokeWidth="2" viewBox="0 0 24 24">
              {mobileOpen ? <><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></> : <><line x1="4" y1="6" x2="20" y2="6"/><line x1="4" y1="12" x2="20" y2="12"/><line x1="4" y1="18" x2="20" y2="18"/></>}
            </svg>
          </button>

          {/* Profile */}
          {user && (
            <div className="relative" ref={menuRef}>
              <button
                onClick={() => setProfileOpen(!profileOpen)}
                className="flex items-center gap-2 text-sm text-dark-body hover:text-dark-title transition-colors p-1.5 rounded-xl hover:bg-glass-hover cursor-pointer bg-transparent border-0"
              >
                <div className="w-8 h-8 rounded-full bg-gradient-accent flex items-center justify-center text-white font-bold text-xs">
                  {user.name.charAt(0).toUpperCase()}
                </div>
              </button>
              {profileOpen && (
                <div className="absolute right-0 top-full mt-2 w-64 glass p-3 shadow-soft space-y-2">
                  <div className="px-2 py-1.5">
                    <p className="text-sm font-medium text-dark-title truncate">{user.name}</p>
                    <span className={`inline-block mt-1 text-[10px] font-semibold px-2 py-0.5 rounded-full border ${roleBadgeColors[user.role] || 'text-dark-muted border-gray-500/30'}`}>
                      {user.role}
                    </span>
                  </div>
                  <hr className="border-glass-border" />
                  <Link to="/change-password" className="block px-2 py-1.5 text-sm text-dark-muted hover:text-dark-title hover:bg-glass-hover rounded-xl transition-colors no-underline">
                    Change Password
                  </Link>
                  <button
                    onClick={() => { logout(); navigate('/login'); }}
                    className="w-full text-left px-2 py-1.5 text-sm text-red-400 hover:text-red-300 hover:bg-glass-hover rounded-xl transition-colors cursor-pointer bg-transparent border-0"
                  >
                    Logout
                  </button>
                </div>
              )}
            </div>
          )}
        </div>
      </div>
      {/* Mobile Navigation Panel */}
      {mobileOpen && (
        <div className="md:hidden glass border-t border-glass-border shadow-soft">
          <div className="max-w-7xl mx-auto px-4 py-4 flex flex-col gap-3">
            {!user ? (
              <>
                <Link to="/" onClick={() => setMobileOpen(false)} className={`text-sm font-medium no-underline ${pathname === '/' ? 'text-blue-accent' : 'text-dark-muted'}`}>Home</Link>
                <Link to="/login" onClick={() => setMobileOpen(false)} className={`text-sm font-medium no-underline ${pathname === '/login' ? 'text-blue-accent' : 'text-dark-muted'}`}>Login</Link>
              </>
            ) : (
              <>
                <Link to="/announcements" onClick={() => setMobileOpen(false)} className="text-sm font-medium text-dark-muted no-underline">Notices</Link>
                <Link to="/events" onClick={() => setMobileOpen(false)} className="text-sm font-medium text-dark-muted no-underline">Events</Link>
              <Link to="/meetings" onClick={() => setMobileOpen(false)} className="text-sm font-medium text-dark-muted no-underline">Meetings</Link>
                {user?.role === 'Student Member' && <Link to="/achievements" onClick={() => setMobileOpen(false)} className="text-sm font-medium text-dark-muted no-underline">Achievements</Link>}
                {['Administrator', 'Faculty Coordinator', 'Association President', 'Vice President'].includes(user?.role) && <Link to="/achievements/review" onClick={() => setMobileOpen(false)} className="text-sm font-medium text-dark-muted no-underline">Achievements</Link>}
                <Link to="/certificates" onClick={() => setMobileOpen(false)} className="text-sm font-medium text-dark-muted no-underline">Certificates</Link>
                {['Administrator', 'Association President', 'Vice President', 'Faculty Coordinator'].includes(user?.role) && <Link to="/certificates/issue" onClick={() => setMobileOpen(false)} className="text-sm font-medium text-dark-muted no-underline">Issue Cert</Link>}
                <Link to="/gallery" onClick={() => setMobileOpen(false)} className="text-sm font-medium text-dark-muted no-underline">Gallery</Link>
                {['Administrator', 'Faculty Coordinator', 'Association President', 'Vice President'].includes(user?.role) && <Link to="/reports" onClick={() => setMobileOpen(false)} className="text-sm font-medium text-dark-muted no-underline">Reports</Link>}
                {user?.role === 'Administrator' && <Link to="/committee" onClick={() => setMobileOpen(false)} className="text-sm font-medium text-dark-muted no-underline">Committee</Link>}
                {['Administrator', 'Faculty Coordinator'].includes(user?.role) && <Link to="/membership/manage" onClick={() => setMobileOpen(false)} className="text-sm font-medium text-dark-muted no-underline">Members</Link>}
                {user?.role === 'Student Member' && <Link to="/membership/status" onClick={() => setMobileOpen(false)} className="text-sm font-medium text-dark-muted no-underline">Membership</Link>}
                {!pathname.startsWith('/dashboard') && (
                  <Link to={roleDashboardMap[user.role] || '/'} onClick={() => setMobileOpen(false)} className="text-sm font-medium text-blue-accent no-underline">Dashboard</Link>
                )}
                <Link to="/" onClick={() => setMobileOpen(false)} className="text-sm font-medium text-dark-muted no-underline">Home</Link>
                <Link to="/change-password" onClick={() => setMobileOpen(false)} className="text-sm font-medium text-dark-muted no-underline">Change Password</Link>
                <button onClick={() => { setMobileOpen(false); logout(); navigate('/login'); }} className="text-left text-sm font-medium text-red-400 bg-transparent border-0 cursor-pointer">Logout</button>
              </>
            )}
          </div>
        </div>
      )}
    </header>
  )
}
