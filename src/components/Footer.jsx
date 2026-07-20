import { Link } from 'react-router-dom'
import { CollegeLogo, AimlLogo } from './Logo'

export default function Footer() {
  return (
    <footer className="border-t border-glass-border bg-warm-alt/50">
      <div className="max-w-7xl mx-auto px-6 py-12">
        <div className="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-8">

          {/* Brand */}
          <div className="space-y-3">
            <div className="flex items-center gap-2">
              <CollegeLogo className="w-8 h-8" />
              <AimlLogo className="w-8 h-8" />
              <span className="text-lg font-bold text-gradient">AIMSA</span>
            </div>
            <p className="text-xs text-dark-subtle leading-relaxed">
              AIML Student Association<br />
              Zeal College of Engineering & Research
            </p>
          </div>

          {/* Contact */}
          <div className="space-y-2">
            <h4 className="text-sm font-semibold text-dark-title">Contact</h4>
            <p className="text-xs text-dark-muted">support@aimsa.zeal.edu.in</p>
            <p className="text-xs text-dark-muted">+91 20 1234 5678</p>
            <p className="text-xs text-dark-subtle">Narhe, Pune – 411041, Maharashtra</p>
          </div>

          {/* Links */}
          <div className="space-y-2">
            <h4 className="text-sm font-semibold text-dark-title">Links</h4>
            <div className="flex flex-col gap-1.5">
              <Link to="/privacy" className="text-xs text-dark-muted hover:text-blue-accent transition-colors no-underline">Privacy Policy</Link>
              <Link to="/terms" className="text-xs text-dark-muted hover:text-blue-accent transition-colors no-underline">Terms &amp; Conditions</Link>
            </div>
          </div>

          {/* Version */}
          <div className="space-y-2">
            <h4 className="text-sm font-semibold text-dark-title">Portal</h4>
            <p className="text-xs text-dark-muted">Version 1.0.0</p>
            <p className="text-xs text-dark-subtle">Last updated: July 2026</p>
            <p className="text-xs text-dark-subtle">Dept. of Artificial Intelligence &amp; Machine Learning</p>
          </div>
        </div>

        <div className="mt-10 pt-6 border-t border-glass-border text-center">
          <p className="text-xs text-dark-subtle">
            &copy; {new Date().getFullYear()} AIMSA – AIML Student Association. Zeal College of Engineering &amp; Research. All rights reserved.
          </p>
        </div>
      </div>
    </footer>
  )
}
