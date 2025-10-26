import { useState, useEffect } from 'react'
import { useNavigate } from 'react-router-dom'
import api from '../../api/axios'
import { formatDate } from '../../utils/dateFormat'
import './RequestVacation.css'

const RequestVacation = () => {
  const navigate = useNavigate()
  const [formData, setFormData] = useState({
    start_date: '',
    end_date: '',
    reason: ''
  })
  const [calculatedDays, setCalculatedDays] = useState(null)
  const [remainingDays, setRemainingDays] = useState(null)
  const [loading, setLoading] = useState(false)
  const [error, setError] = useState('')
  const [success, setSuccess] = useState('')

  useEffect(() => {
    fetchRemainingDays()
  }, [])

  useEffect(() => {
    if (formData.start_date && formData.end_date) {
      calculateVacationDays()
    } else {
      setCalculatedDays(null)
    }
  }, [formData.start_date, formData.end_date])

  const fetchRemainingDays = async () => {
    try {
      const response = await api.get('/api/requests?action=remaining')
      setRemainingDays(response.data)
    } catch (err) {
      console.error('Failed to fetch remaining days:', err)
    }
  }

  const calculateVacationDays = async () => {
    try {
      const response = await api.get('/api/requests', {
        params: {
          action: 'calculate',
          start_date: formData.start_date,
          end_date: formData.end_date
        }
      })
      setCalculatedDays(response.data)
    } catch (err) {
      console.error('Failed to calculate days:', err)
      setCalculatedDays(null)
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

    // Validation
    const startDate = new Date(formData.start_date)
    const endDate = new Date(formData.end_date)
    const today = new Date()
    today.setHours(0, 0, 0, 0)

    if (startDate < today) {
      setError('Start date must be in the future')
      return
    }

    if (endDate < startDate) {
      setError('End date must be after start date')
      return
    }

    if (calculatedDays && remainingDays && calculatedDays.days > remainingDays.remaining) {
      setError(`You only have ${remainingDays.remaining} vacation days remaining`)
      return
    }

    setLoading(true)

    try {
      await api.post('/api/requests', formData)
      setSuccess('Vacation request submitted successfully!')
      
      setTimeout(() => {
        navigate('/my-requests')
      }, 1500)
    } catch (err) {
      setError(err.response?.data?.error || 'Failed to submit vacation request')
    } finally {
      setLoading(false)
    }
  }

  return (
    <div className="container">
      <div className="page-header">
        <h2>Request Vacation</h2>
        <button 
          className="btn btn-secondary"
          onClick={() => navigate('/my-requests')}
        >
          Back to My Requests
        </button>
      </div>

      {error && <div className="alert alert-error">{error}</div>}
      {success && <div className="alert alert-success">{success}</div>}

      {remainingDays && (
        <div className="card vacation-summary">
          <h3>Your Vacation Balance</h3>
          <div className="balance-info">
            <div className="balance-item">
              <span className="label">Allocated:</span>
              <span className="value">{remainingDays.allocated} days</span>
            </div>
            <div className="balance-item">
              <span className="label">Used:</span>
              <span className="value">{remainingDays.used} days</span>
            </div>
            <div className="balance-item highlight">
              <span className="label">Remaining:</span>
              <span className="value">{remainingDays.remaining} days</span>
            </div>
          </div>
        </div>
      )}

      <div className="card request-form-card">
        <form onSubmit={handleSubmit} className="request-form">
          <div className="form-row">
            <div className="form-group">
              <label htmlFor="start_date">Start Date *</label>
              <input
                type="date"
                id="start_date"
                name="start_date"
                className="form-control"
                value={formData.start_date}
                onChange={handleChange}
                required
                disabled={loading}
                min={new Date().toISOString().split('T')[0]}
              />
            </div>

            <div className="form-group">
              <label htmlFor="end_date">End Date *</label>
              <input
                type="date"
                id="end_date"
                name="end_date"
                className="form-control"
                value={formData.end_date}
                onChange={handleChange}
                required
                disabled={loading}
                min={formData.start_date || new Date().toISOString().split('T')[0]}
              />
            </div>
          </div>

          {formData.start_date && formData.end_date && (
            <div style={{marginBottom: '15px', padding: '10px', backgroundColor: '#f0f9ff', borderRadius: '6px', fontSize: '14px'}}>
              <strong>Selected Period:</strong> {formatDate(formData.start_date)} → {formatDate(formData.end_date)}
            </div>
          )}

          {calculatedDays && calculatedDays.days > 0 && (
            <div className="days-info">
              <strong>Work Days Requested:</strong> {calculatedDays.days} {calculatedDays.days === 1 ? 'day' : 'days'}
              {remainingDays && (
                <span style={{color: '#2563eb', marginLeft: '10px'}}>
                  (You have {remainingDays.remaining} available)
                </span>
              )}
              <br />
              <small style={{color: '#666'}}>
                (Based on your work schedule: {calculatedDays.schedule?.join(', ')})
              </small>
            </div>
          )}

          <div className="form-group">
            <label htmlFor="reason">Reason</label>
            <textarea
              id="reason"
              name="reason"
              className="form-control"
              value={formData.reason}
              onChange={handleChange}
              rows="4"
              placeholder="Optional: Provide a reason for your vacation request"
              disabled={loading}
            />
          </div>

          <div className="form-actions">
            <button 
              type="button" 
              className="btn btn-secondary"
              onClick={() => navigate('/my-requests')}
              disabled={loading}
            >
              Cancel
            </button>
            <button 
              type="submit" 
              className="btn btn-primary"
              disabled={loading}
            >
              {loading ? 'Submitting...' : 'Submit Request'}
            </button>
          </div>
        </form>
      </div>

      <div className="card info-card">
        <h3>Vacation Request Guidelines</h3>
        <ul>
          <li>Requests must be submitted at least 24 hours in advance</li>
          <li>Your manager will review and approve/decline your request</li>
          <li>You cannot have overlapping vacation requests</li>
          <li>You will be notified once your request is processed</li>
        </ul>
      </div>
    </div>
  )
}

export default RequestVacation
