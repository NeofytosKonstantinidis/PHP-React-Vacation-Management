import { useState, useEffect } from 'react'
import { useNavigate, useParams } from 'react-router-dom'
import api from '../../api/axios'
import './UserForm.css'

const UserForm = () => {
  const { id } = useParams()
  const navigate = useNavigate()
  const isEdit = !!id

  const [formData, setFormData] = useState({
    name: '',
    username: '',
    email: '',
    password: '',
    role_id: '2',
    schedule_id: '1',
    vacation_days: '20'
  })
  const [roles, setRoles] = useState([])
  const [schedules, setSchedules] = useState([])
  const [loading, setLoading] = useState(false)
  const [error, setError] = useState('')
  const [success, setSuccess] = useState('')

  useEffect(() => {
    fetchRoles()
    fetchSchedules()
    if (isEdit) {
      fetchUser()
    }
  }, [id])

  const fetchRoles = async () => {
    try {
      const response = await api.get('/api/lookups?type=roles')
      setRoles(Array.isArray(response.data) ? response.data : [])
    } catch (err) {
      setError('Failed to fetch roles')
    }
  }

  const fetchSchedules = async () => {
    try {
      const response = await api.get('/api/lookups?type=schedule_types')
      setSchedules(Array.isArray(response.data) ? response.data : [])
    } catch (err) {
      setError('Failed to fetch schedules')
    }
  }

  const fetchUser = async () => {
    try {
      setLoading(true)
      const response = await api.get(`/api/users?id=${id}`)
      const user = response.data
      setFormData({
        name: user.name || '',
        username: user.username || '',
        email: user.email || '',
        password: '',
        role_id: user.role_id?.toString() || '2',
        schedule_id: user.schedule_id?.toString() || '1',
        vacation_days: user.vacation_days?.toString() || '20'
      })
    } catch (err) {
      setError(err.response?.data?.error || 'Failed to fetch user')
    } finally {
      setLoading(false)
    }
  }

  const handleChange = (e) => {
    setFormData({
      ...formData,
      [e.target.name]: e.target.value
    })
  }

  const handleSubmit = async (e) => {
    e.preventDefault()
    setError('')
    setSuccess('')
    setLoading(true)

    try {
      const payload = { ...formData }
      
      if (isEdit && !payload.password) {
        delete payload.password
      }

      if (isEdit) {
        await api.put(`/api/users?id=${id}`, payload)
        setSuccess('User updated successfully')
      } else {
        await api.post('/api/users', payload)
        setSuccess('User created successfully')
      }

      setTimeout(() => {
        navigate('/users')
      }, 1500)
    } catch (err) {
      setError(err.response?.data?.error || 'Failed to edit user')
    } finally {
      setLoading(false)
    }
  }

  if (loading && isEdit) {
    return <div className="container"><div className="loading">Loading user...</div></div>
  }

  return (
    <div className="container">
      <div className="page-header">
        <h2>{isEdit ? 'Edit User' : 'Create User'}</h2>
        <button 
          className="btn btn-secondary"
          onClick={() => navigate('/users')}
        >
          Back to List
        </button>
      </div>

      {error && <div className="alert alert-error">{error}</div>}
      {success && <div className="alert alert-success">{success}</div>}

      <div className="card">
        <form onSubmit={handleSubmit} className="user-form">
          <div className="form-row">
            <div className="form-group">
              <label htmlFor="name">Full Name *</label>
              <input
                type="text"
                id="name"
                name="name"
                className="form-control"
                value={formData.name}
                onChange={handleChange}
                required
                disabled={loading}
              />
            </div>

            <div className="form-group">
              <label htmlFor="email">Email *</label>
              <input
                type="email"
                id="email"
                name="email"
                className="form-control"
                value={formData.email}
                onChange={handleChange}
                required
                disabled={loading}
              />
            </div>
          </div>

          <div className="form-row">
            <div className="form-group">
              <label htmlFor="username">Username *</label>
              <input
                type="text"
                id="username"
                name="username"
                className="form-control"
                value={formData.username}
                onChange={handleChange}
                required
                disabled={loading}
              />
            </div>

            <div className="form-group">
              <label htmlFor="password">Password {isEdit ? '(leave blank to keep current)' : '*'}</label>
              <input
                type="password"
                id="password"
                name="password"
                className="form-control"
                value={formData.password}
                onChange={handleChange}
                required={!isEdit}
                disabled={loading}
                autoComplete="new-password"
              />
            </div>
          </div>

          <div className="form-row">
            <div className="form-group">
              <label htmlFor="role_id">Role *</label>
              <select
                id="role_id"
                name="role_id"
                className="form-control"
                value={formData.role_id}
                onChange={handleChange}
                required
                disabled={loading}
              >
                <option value="">Select Role</option>
                {roles.map(role => (
                  <option key={role.id} value={role.id}>
                    {role.name}
                  </option>
                ))}
              </select>
            </div>

            <div className="form-group">
              <label htmlFor="schedule_id">Work Schedule *</label>
              <select
                id="schedule_id"
                name="schedule_id"
                className="form-control"
                value={formData.schedule_id}
                onChange={handleChange}
                required
                disabled={loading}
              >
                <option value="">Select Schedule</option>
                {schedules.map(schedule => (
                  <option key={schedule.id} value={schedule.id}>
                    {schedule.name} {schedule.description && `- ${schedule.description}`}
                  </option>
                ))}
              </select>
            </div>
          </div>

          <div className="form-row">
            <div className="form-group">
              <label htmlFor="vacation_days">Vacation Days (per year) *</label>
              <input
                type="number"
                id="vacation_days"
                name="vacation_days"
                className="form-control"
                value={formData.vacation_days}
                onChange={handleChange}
                required
                min="0"
                max="365"
                disabled={loading}
              />
            </div>
          </div>

          <div className="form-actions">
            <button 
              type="button" 
              className="btn btn-secondary"
              onClick={() => navigate('/users')}
              disabled={loading}
            >
              Cancel
            </button>
            <button 
              type="submit" 
              className="btn btn-primary"
              disabled={loading}
            >
              {loading ? 'Saving...' : (isEdit ? 'Update User' : 'Create User')}
            </button>
          </div>
        </form>
      </div>
    </div>
  )
}

export default UserForm