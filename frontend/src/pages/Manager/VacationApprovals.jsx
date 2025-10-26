import { useState, useEffect } from 'react'
import api from '../../api/axios'
import { formatDate } from '../../utils/dateFormat'
import './VacationApprovals.css'

const VacationApprovals = () => {
  const [requests, setRequests] = useState([])
  const [loading, setLoading] = useState(true)
  const [error, setError] = useState('')
  const [processing, setProcessing] = useState(null)

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

  const handleStatusChange = async (requestId, statusId) => {
    try {
      setProcessing(requestId)
      await api.put(`/api/requests?id=${requestId}`, { status_id: statusId })
      
      // Update local state
      setRequests(requests.map(req => 
        req.id === requestId 
          ? { ...req, status_id: statusId, status_name: statusId === 2 ? 'approved' : 'rejected' }
          : req
      ))
      setError('')
    } catch (err) {
      setError(err.response?.data?.error || 'Failed to update request status')
    } finally {
      setProcessing(null)
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
    return <div className="container"><div className="loading">Loading vacation requests...</div></div>
  }

  return (
    <div className="container">
      <div className="page-header">
        <h2>Vacation Request Approvals</h2>
        <button className="btn btn-secondary" onClick={fetchRequests}>
          Refresh
        </button>
      </div>

      {error && <div className="alert alert-error">{error}</div>}

      <div className="card">
        {requests.length === 0 ? (
          <p className="no-data">No vacation requests found.</p>
        ) : (
          <table className="table">
            <thead>
              <tr>
                <th>Employee</th>
                <th>Start Date</th>
                <th>End Date</th>
                <th>Days</th>
                <th>Reason</th>
                <th>Status</th>
                <th>Requested</th>
                <th>Actions</th>
              </tr>
            </thead>
            <tbody>
              {requests.map(request => {
                const isPending = request.status_name?.toLowerCase() === 'pending'

                return (
                  <tr key={request.id}>
                    <td>
                      <div>
                        <strong>{request.employee_name}</strong>
                      </div>
                    </td>
                    <td>{formatDate(request.start_date)}</td>
                    <td>{formatDate(request.end_date)}</td>
                    <td>
                      <strong>{request.calculated_days || 0}</strong> {request.calculated_days === 1 ? 'day' : 'days'}
                      <br />
                      <small style={{color: '#666'}}>
                        ({request.remaining_days || 0} available)
                      </small>
                    </td>
                    <td className="reason-cell">{request.reason || 'N/A'}</td>
                    <td>
                      <span className={`badge ${getStatusBadgeClass(request.status_name)}`}>
                        {request.status_name}
                      </span>
                    </td>
                    <td>{formatDate(request.created_at)}</td>
                    <td>
                      {isPending ? (
                        <div className="action-buttons">
                          <button
                            className="btn btn-sm btn-success"
                            onClick={() => handleStatusChange(request.id, 2)}
                            disabled={processing === request.id}
                          >
                            {processing === request.id ? '...' : 'Accept'}
                          </button>
                          <button
                            className="btn btn-sm btn-danger"
                            onClick={() => handleStatusChange(request.id, 3)}
                            disabled={processing === request.id}
                          >
                            {processing === request.id ? '...' : 'Decline'}
                          </button>
                        </div>
                      ) : (
                        <span className="text-muted">—</span>
                      )}
                    </td>
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

export default VacationApprovals
