-- ======================
-- SAMPLE SEED DATA
-- ======================

INSERT INTO role_types (name)
VALUES ('manager'), ('employee');

INSERT INTO schedule_types (name, description, work_days)
VALUES
('5 Day Schedule', 'Monday to Friday', 'Mon,Tue,Wed,Thu,Fri'),
('6 Day Schedule', 'Monday to Saturday', 'Mon,Tue,Wed,Thu,Fri,Sat'),
('Custom Schedule', 'Custom work days', 'Mon,Thu,Fri');


INSERT INTO vacation_status_types (name)
VALUES ('pending'), ('approved'), ('rejected');




-- Users
-- All users have password: password123
INSERT INTO users (name, email, username, password_hash, role_id, schedule_id, vacation_days)
VALUES 
('Alice Manager', 'alice@company.com', 'alice', '$2y$10$WY30bJ1K0pWSaZkuvDPz3On07covJVe47INgUkDSXdtFZxCGGDWR6', 1, 1, 25),
('Bob Employee', 'bob@company.com', 'bob', '$2y$10$WY30bJ1K0pWSaZkuvDPz3On07covJVe47INgUkDSXdtFZxCGGDWR6', 2, 1, 20);

-- Work Schedules
INSERT INTO work_schedule (employee_id, schedule_type_id, work_days)
VALUES
(2, 1, 'Mon,Tue,Wed,Thu,Fri');

-- Vacation Requests
INSERT INTO vacation_requests (employee_id, start_date, end_date, reason, status_id)
VALUES
(2, '2025-10-25', '2025-10-30', 'Family trip', 1);

-- Audit Logs
INSERT INTO audit_log (user_id, action, details)
VALUES
(1, 'user_created', 'Manager Alice created user Bob');
