import { Navigate } from 'react-router-dom'
import { useAuth } from '../context/AuthContext'

const PrivateRoute = ({ children, role }) => {
  const { isAuthenticated, user, loading } = useAuth()

  if (loading) {
    return <div className="loading">Loading...</div>
  }

  if (!isAuthenticated) {
    return <Navigate to="/signin" replace />
  }

  // Check role-based access
  if (role && user?.role !== role) {
    // Redirect to appropriate page based on user role
    if (user?.role === 'manager') {
      return <Navigate to="/users" replace />
    } else {
      return <Navigate to="/my-requests" replace />
    }
  }

  return children
}

export default PrivateRoute
