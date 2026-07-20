export default function GradientButton({ children, className = '', ...props }) {
  return (
    <button
      className={`bg-gradient-accent hover:opacity-90 transition-opacity duration-200 text-white font-semibold px-6 py-2.5 rounded-2xl cursor-pointer ${className}`}
      {...props}
    >
      {children}
    </button>
  )
}
