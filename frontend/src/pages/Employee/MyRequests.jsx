import { useState, useEffect } from 'react'
import { useNavigate } from 'react-router-dom'
import api from '../../api/axios'
import { formatDate } from '../../utils/dateFormat'
import './MyRequests.css'

const MyRequests = () => {
  const [requests, setRequests] = useState([])
  const [loading, setLoading] = useState(true)
  const [error, setError] = useState('')
  const navigate = useNavigate()

  useEffect(() => {
    fetchRequests()
  }, [])

  const fetchRequests = async () => {
    try {
      setLoading(true)
      const response = await api.get('/api/requests')
      setRequests(Array.isArray(response.data) ? response.data : [])
      setError('')
    } catch (err) {
      setError(err.response?.data?.error || 'Failed to fetch vacation requests')
    } finally {
      setLoading(false)
    }
  }

  const getStatusBadgeClass = (status) => {
    switch(status?.toLowerCase()) {
      case 'approved': return 'badge-approved'
      case 'rejected': return 'badge-rejected'
      default: return 'badge-pending'
    }
  }

  if (loading) {
    return <div className="container"><div className="loading">Loading your requests...</div></div>
  }

  return (
    <div className="container">
      <div className="page-header">
        <h2>My Vacation Requests</h2>
        <button 
          className="btn btn-primary"
          onClick={() => navigate('/request-vacation')}
        >
          + Request Vacation
        </button>
      </div>

      {error && <div className="alert alert-error">{error}</div>}

      <div className="card">
        {requests.length === 0 ? (
          <div className="empty-state">
            <p>You haven't made any vacation requests yet.</p>
            <button 
              className="btn btn-primary"
              onClick={() => navigate('/request-vacation')}
            >
              Request Your First Vacation
            </button>
          </div>
        ) : (
          <table className="table">
            <thead>
              <tr>
                <th>Start Date</th>
                <th>End Date</th>
                <th>Days</th>
                <th>Reason</th>
                <th>Status</th>
                <th>Requested On</th>
              </tr>
            </thead>
            <tbody>
              {requests.map(request => {
                const startDate = new Date(request.start_date)
                const endDate = new Date(request.end_date)
                const days = Math.ceil((endDate - startDate) / (1000 * 60 * 60 * 24)) + 1

                return (
                  <tr key={request.id}>
                    <td>{formatDate(request.start_date)}</td>
                    <td>{formatDate(request.end_date)}</td>
                    <td>{days} {days === 1 ? 'day' : 'days'}</td>
                    <td className="reason-cell">{request.reason || 'N/A'}</td>
                    <td>
                      <span className={`badge ${getStatusBadgeClass(request.status_name)}`}>
                        {request.status_name}
                      </span>
                    </td>
                    <td>{formatDate(request.created_at)}</td>
                  </tr>
                )
              })}
            </tbody>
          </table>
        )}
      </div>
    </div>
  )
}

export default MyRequests
