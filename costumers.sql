-- Make sure you're using the correct database
USE customer_db;

-- -- Optional: First, create a test user (if not already created)
-- INSERT INTO users (username, password) VALUES (
--   'admin',
--   '$2y$10$K5UO92Ay8kFAJjEKuSnhkeosxMeh3c5tuXJYVnQxW9z1EoQaHAbDq'
-- );
-- -- password = "1234"

-- -- Now insert some customers (assuming admin's user_id = 1)
-- INSERT INTO customers (user_id, name, phone, address, notes) VALUES
-- (1, 'John Doe', '555-1234', '123 Main St', 'First client'),
-- (1, 'Jane Smith', '555-5678', '456 Oak Ave', 'Needs follow-up'),
-- (1, 'Carlos Rivera', '555-0000', '789 Pine Rd', 'Interested in upgrades');

ALTER TABLE users
ADD COLUMN first_name VARCHAR(50) NOT NULL,
ADD COLUMN last_name VARCHAR(50) NOT NULL,
ADD COLUMN usertag VARCHAR(50) NOT NULL UNIQUE;

-- Create a new table to manage user-specific customer tables
CREATE TABLE tables (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    title VARCHAR(100) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- Update the customers table to associate it with a specific table
ALTER TABLE customers
MODIFY COLUMN id INT AUTO_INCREMENT PRIMARY KEY;
