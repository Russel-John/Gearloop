-- Database schema for UCLM GearLoop
CREATE DATABASE IF NOT EXISTS gearloop_db;
USE gearloop_db;

-- Table for users (verified students/staff)
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    student_id VARCHAR(20) NOT NULL UNIQUE,
    department VARCHAR(50) NOT NULL,
    full_name VARCHAR(100) DEFAULT NULL,
    bio TEXT DEFAULT NULL,
    profile_picture VARCHAR(255) DEFAULT NULL,
    role ENUM('student', 'admin') DEFAULT 'student',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Table for items (Sale/Swap listings)
CREATE TABLE IF NOT EXISTS items (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    title VARCHAR(100) NOT NULL,
    description TEXT,
    category ENUM('Uniform', 'Book', 'Equipment', 'Other') NOT NULL,
    item_condition ENUM('A', 'B', 'C', 'D') NOT NULL COMMENT 'A=Mint, B=Good, C=Fair, D=Damaged',
    tag ENUM('For Sale', 'For Swap', 'Both') NOT NULL,
    price DECIMAL(10, 2) DEFAULT 0.00,
    department VARCHAR(50) NOT NULL,
    image_path VARCHAR(255) DEFAULT NULL,
    status ENUM('Available', 'Sold', 'Reserved') DEFAULT 'Available',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- Initial sample user
INSERT IGNORE INTO users (username, password, student_id, department, role) 
VALUES ('student123', '$2y$10$M3ZpDpJDHzTwcXBRCDoZx..e9zrRsyQkmwUOjDXzSC4RfwH55n.C.', '2023-12345', 'Maritime', 'student');
-- password is 'password'
