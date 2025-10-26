import { useState, useEffect } from 'react'
import { useNavigate } from 'react-router-dom'
import api from '../../api/axios'
import { formatDate } from '../../utils/dateFormat'
import './UserList.css'

const UserList = () => {
  const [users, setUsers] = useState([])
  const [loading, setLoading] = useState(true)
  const [error, setError] = useState('')
  const [deleteConfirm, setDeleteConfirm] = useState(null)
  const navigate = useNavigate()

  useEffect(() => {
    fetchUsers()
  }, [])

  const fetchUsers = async () => {
    try {
      setLoading(true)
      const response = await api.get('/api/users')
      setUsers(Array.isArray(response.data) ? response.data : [])
      setError('')
    } catch (err) {
      setError(err.response?.data?.error || 'Failed to fetch users')
    } finally {
      setLoading(false)
    }
  }

  const handleDelete = async (userId) => {
    try {
      await api.delete(`/api/users?id=${userId}`)
      setUsers(users.filter(user => user.id !== userId))
      setDeleteConfirm(null)
      setError('')
    } catch (err) {
      setError(err.response?.data?.error || 'Failed to delete user')
    }
  }

  if (loading) {
    return <div className="container"><div className="loading">Loading users...</div></div>
  }

  return (
    <div className="container">
      <div className="page-header">
        <h2>User Management</h2>
        <button 
          className="btn btn-primary"
          onClick={() => navigate('/users/create')}
        >
          + Create User
        </button>
      </div>

      {error && <div className="alert alert-error">{error}</div>}

      <div className="card">
        {users.length === 0 ? (
          <p className="no-data">No users found.</p>
        ) : (
          <table className="table">
            <thead>
              <tr>
                <th>ID</th>
                <th>Name</th>
                <th>Username</th>
                <th>Email</th>
                <th>Role</th>
                <th>Schedule</th>
                <th>Vacation Days</th>
                <th>Created At</th>
                <th>Actions</th>
              </tr>
            </thead>
            <tbody>
              {users.map(user => (
                <tr key={user.id}>
                  <td>{user.id}</td>
                  <td>{user.name}</td>
                  <td>{user.username}</td>
                  <td>{user.email}</td>
                  <td>
                    <span className={`badge badge-${user.role_name}`}>
                      {user.role_name}
                    </span>
                  </td>
                  <td>{user.schedule_name || 'N/A'}</td>
                  <td>{user.vacation_days || 20}</td>
                  <td>{formatDate(user.created_at)}</td>
                  <td>
                    <div className="action-buttons">
                      <button
                        className="btn btn-sm btn-primary"
                        onClick={() => navigate(`/users/edit/${user.id}`)}
                      >
                        Edit
                      </button>
                      <button
                        className="btn btn-sm btn-danger"
                        onClick={() => setDeleteConfirm(user.id)}
                      >
                        Delete
                      </button>
                    </div>
                  </td>
                </tr>
              ))}
            </tbody>
          </table>
        )}
      </div>

      {deleteConfirm && (
        <div className="modal-overlay" onClick={() => setDeleteConfirm(null)}>
          <div className="modal" onClick={(e) => e.stopPropagation()}>
            <h3>Confirm Delete</h3>
            <p>Are you sure you want to delete this user? This action cannot be undone.</p>
            <div className="modal-actions">
              <button 
                className="btn btn-secondary"
                onClick={() => setDeleteConfirm(null)}
              >
                Cancel
              </button>
              <button 
                className="btn btn-danger"
                onClick={() => handleDelete(deleteConfirm)}
              >
                Delete
              </button>
            </div>
          </div>
        </div>
      )}
    </div>
  )
}

export default UserList
