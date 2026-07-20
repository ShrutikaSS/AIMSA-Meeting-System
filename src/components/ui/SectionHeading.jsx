export default function SectionHeading({ children, className = '' }) {
  return (
    <h2 className={`text-3xl md:text-4xl font-bold text-gradient mb-2 ${className}`}>
      {children}
    </h2>
  )
}
