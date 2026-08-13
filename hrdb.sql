CREATE DATABASE IF NOT EXISTS hrdb;
USE hrdb;

CREATE TABLE IF NOT EXISTS managers (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    failed_attempts INT DEFAULT 0,
    locked_until DATETIME NULL
);

CREATE TABLE IF NOT EXISTS eoi (
    eoi_id INT AUTO_INCREMENT PRIMARY KEY,
    job_ref VARCHAR(10),
    first_name VARCHAR(50),
    last_name VARCHAR(50),
    email VARCHAR(100),
    phone VARCHAR(20),
    status VARCHAR(20) DEFAULT 'New',
    UNIQUE KEY unique_eoi (job_ref, email)
);

INSERT INTO managers (username, password) VALUES
('admin', 'swin123');

INSERT INTO eoi (job_ref, first_name, last_name, email, phone, status) VALUES
('DEV01','Nhu','Tran','nhu@example.com','0123456789','New'),
('HR02','Trang','Le','trang@example.com','0987654321','New'),
('QA03','Alan','Nguyen','alan@example.com','0987123456','In Progress');
