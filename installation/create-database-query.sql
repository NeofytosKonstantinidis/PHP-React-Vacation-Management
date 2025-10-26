-- Drop tables in dependency order

CREATE DATABASE IF NOT EXISTS leaves_management;
USE leaves_management;

DROP TABLE IF EXISTS audit_log;
DROP TABLE IF EXISTS vacation_requests;
DROP TABLE IF EXISTS work_schedule;
DROP TABLE IF EXISTS users;
DROP TABLE IF EXISTS role_types;
DROP TABLE IF EXISTS schedule_types;
DROP TABLE IF EXISTS vacation_status_types;

-- ======================
-- ROLE TYPES (for users)
-- ======================
CREATE TABLE role_types (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(50) NOT NULL UNIQUE
);


-- ======================
-- SCHEDULE TYPES (for work_schedule)
-- ======================
CREATE TABLE schedule_types (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(50) NOT NULL UNIQUE,
    description VARCHAR(255),
    work_days SET('Mon','Tue','Wed','Thu','Fri','Sat','Sun') DEFAULT 'Mon,Tue,Wed,Thu,Fri'
);


-- ======================
-- VACATION STATUS TYPES
-- ======================
CREATE TABLE vacation_status_types (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(50) NOT NULL UNIQUE
);



-- ======================
-- USERS TABLE
-- ======================
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    username VARCHAR(50) NOT NULL UNIQUE,
    password_hash VARCHAR(255) NOT NULL,
    role_id INT NOT NULL,
    schedule_id INT NOT NULL,
    vacation_days INT DEFAULT 20,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (role_id) REFERENCES role_types(id),
    FOREIGN KEY (schedule_id) REFERENCES schedule_types(id)
);

-- ======================
-- WORK SCHEDULE TABLE
-- ======================
CREATE TABLE work_schedule (
    id INT AUTO_INCREMENT PRIMARY KEY,
    employee_id INT NOT NULL,
    schedule_type_id INT NOT NULL,
    work_days SET('Mon','Tue','Wed','Thu','Fri','Sat','Sun') DEFAULT 'Mon,Tue,Wed,Thu,Fri',
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (employee_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (schedule_type_id) REFERENCES schedule_types(id)
);

-- ======================
-- VACATION REQUESTS TABLE
-- ======================
CREATE TABLE vacation_requests (
    id INT AUTO_INCREMENT PRIMARY KEY,
    employee_id INT NOT NULL,
    start_date DATE NOT NULL,
    end_date DATE NOT NULL,
    reason TEXT,
    status_id INT NOT NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (employee_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (status_id) REFERENCES vacation_status_types(id)
);

-- ======================
-- AUDIT LOG TABLE
-- ======================
CREATE TABLE audit_log (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT,
    action VARCHAR(255) NOT NULL,
    details TEXT,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL
);


