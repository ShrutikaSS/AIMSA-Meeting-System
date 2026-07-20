export function CollegeLogo({ className = 'w-10 h-10' }) {
  return (
    <svg className={className} viewBox="0 0 40 40" fill="none" xmlns="http://www.w3.org/2000/svg">
      <rect width="40" height="40" rx="8" fill="url(#clg)" />
      <text x="20" y="26" textAnchor="middle" fontSize="18" fontWeight="bold" fill="white" fontFamily="Arial">ZC</text>
      <defs>
        <linearGradient id="clg" x1="0" y1="0" x2="40" y2="40">
          <stop stopColor="#f0883e" />
          <stop offset="1" stopColor="#00b4d8" />
        </linearGradient>
      </defs>
    </svg>
  )
}

export function AimlLogo({ className = 'w-10 h-10' }) {
  return (
    <svg className={className} viewBox="0 0 40 40" fill="none" xmlns="http://www.w3.org/2000/svg">
      <rect width="40" height="40" rx="8" fill="url(#aiml)" />
      <text x="20" y="26" textAnchor="middle" fontSize="14" fontWeight="bold" fill="white" fontFamily="Arial">AI</text>
      <defs>
        <linearGradient id="aiml" x1="0" y1="0" x2="40" y2="40">
          <stop stopColor="#00b4d8" />
          <stop offset="1" stopColor="#f0883e" />
        </linearGradient>
      </defs>
    </svg>
  )
}
