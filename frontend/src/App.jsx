import { BrowserRouter as Router, Routes, Route, Navigate } from 'react-router-dom'
import { AuthProvider } from './context/AuthContext'
import PrivateRoute from './components/PrivateRoute'
import Layout from './components/Layout'
import SignIn from './pages/SignIn'
import UserList from './pages/Manager/UserList'
import UserForm from './pages/Manager/UserForm'
import VacationApprovals from './pages/Manager/VacationApprovals'
import MyRequests from './pages/Employee/MyRequests'
import RequestVacation from './pages/Employee/RequestVacation'

function App() {
  return (
    <AuthProvider>
      <Router future={{ v7_startTransition: true, v7_relativeSplatPath: true }}>
        <Routes>
          <Route path="/signin" element={<SignIn />} />
          
          {/* Manager Routes */}
          <Route path="/" element={<PrivateRoute><Layout /></PrivateRoute>}>
            <Route index element={<Navigate to="/users" replace />} />
            <Route path="users" element={<PrivateRoute role="manager"><UserList /></PrivateRoute>} />
            <Route path="users/create" element={<PrivateRoute role="manager"><UserForm /></PrivateRoute>} />
            <Route path="users/edit/:id" element={<PrivateRoute role="manager"><UserForm /></PrivateRoute>} />
            <Route path="approvals" element={<PrivateRoute role="manager"><VacationApprovals /></PrivateRoute>} />
            
            {/* Employee Routes */}
            <Route path="my-requests" element={<PrivateRoute role="employee"><MyRequests /></PrivateRoute>} />
            <Route path="request-vacation" element={<PrivateRoute role="employee"><RequestVacation /></PrivateRoute>} />
          </Route>

          <Route path="*" element={<Navigate to="/signin" replace />} />
        </Routes>
      </Router>
    </AuthProvider>
  )
}

export default App
