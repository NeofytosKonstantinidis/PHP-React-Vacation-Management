-- Migration: Add schedule_id and vacation_days to users table
-- Run this on existing databases to update the schema

USE leaves_management;

-- Add schedule_id column
ALTER TABLE users 
ADD COLUMN schedule_id INT NOT NULL DEFAULT 1 AFTER role_id,
ADD COLUMN vacation_days INT DEFAULT 20 AFTER schedule_id;

-- Add foreign key constraint
ALTER TABLE users 
ADD CONSTRAINT fk_users_schedule 
FOREIGN KEY (schedule_id) REFERENCES schedule_types(id);

-- Update existing users to have a schedule (default: 5_days = id 1)
UPDATE users SET schedule_id = 1 WHERE schedule_id IS NULL OR schedule_id = 0;

-- Verify changes
SELECT id, name, role_id, schedule_id, vacation_days FROM users;
