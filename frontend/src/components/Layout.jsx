import { Outlet, Link, useNavigate } from 'react-router-dom'
import { useAuth } from '../context/AuthContext'
import './Layout.css'

const Layout = () => {
  const { user, logout, isManager } = useAuth()
  const navigate = useNavigate()

  const handleLogout = () => {
    logout()
    navigate('/signin')
  }

  return (
    <div className="layout">
      <header className="header">
        <div className="header-content">
          <h1>Leaves Management System</h1>
          <div className="user-info">
            <span className="user-name">{user?.name}</span>
            <span className="user-role">({user?.role})</span>
            <button onClick={handleLogout} className="btn btn-secondary">
              Logout
            </button>
          </div>
        </div>
      </header>

      <nav className="navbar">
        <div className="nav-content">
          {isManager ? (
            <>
              <Link to="/users" className="nav-link">Users</Link>
              <Link to="/approvals" className="nav-link">Vacation Approvals</Link>
            </>
          ) : (
            <>
              <Link to="/my-requests" className="nav-link">My Requests</Link>
              <Link to="/request-vacation" className="nav-link">Request Vacation</Link>
            </>
          )}
        </div>
      </nav>

      <main className="main-content">
        <Outlet />
      </main>
    </div>
  )
}

export default Layout
