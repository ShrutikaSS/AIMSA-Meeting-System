import { Navigate } from 'react-router-dom'
import { useAuth } from '../../context/AuthContext'

const presidentVpRoles = ['Association President', 'Vice President']

function roleMatches(allowedRoles, userRole) {
  const normalizedAllowed = allowedRoles.flatMap((r) =>
    r === 'President/VP' ? presidentVpRoles : [r]
  )
  return normalizedAllowed.includes(userRole)
}

export default function RoleGuard({ allowedRoles, children }) {
  const { user, getDashboardRoute } = useAuth()

  if (!roleMatches(allowedRoles, user?.role)) {
    return <Navigate to={getDashboardRoute(user?.role)} replace />
  }

  return children
}
