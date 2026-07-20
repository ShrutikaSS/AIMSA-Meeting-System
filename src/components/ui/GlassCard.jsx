export default function GlassCard({ children, className = '', glow = false, hover3d = false, ...props }) {
  return (
    <div
      className={`glass p-6 transition-all duration-300 ${glow ? 'glow-border' : ''} ${hover3d ? 'card-3d cursor-pointer' : ''} ${className}`}
      {...props}
    >
      {children}
    </div>
  )
}
