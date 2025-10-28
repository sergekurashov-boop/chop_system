CREATE DATABASE chop_system;
USE chop_system;

-- Таблица пользователей
CREATE TABLE users (
    id INT PRIMARY KEY AUTO_INCREMENT,
    username VARCHAR(50) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    full_name VARCHAR(100) NOT NULL,
    role ENUM('admin', 'senior', 'medic', 'guard', 'reports') NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    is_active BOOLEAN DEFAULT TRUE
);

-- Таблица сотрудников
CREATE TABLE employees (
    id INT PRIMARY KEY AUTO_INCREMENT,
    full_name VARCHAR(100) NOT NULL,
    position VARCHAR(100) NOT NULL,
    phone VARCHAR(20),
    email VARCHAR(100),
    medical_exam_expiry DATE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    is_active BOOLEAN DEFAULT TRUE
);

-- Таблица смен
CREATE TABLE shifts (
    id INT PRIMARY KEY AUTO_INCREMENT,
    shift_type ENUM('12_hours', '24_hours') NOT NULL,
    start_datetime DATETIME NOT NULL,
    end_datetime DATETIME NOT NULL,
    location VARCHAR(200) NOT NULL,
    route_description TEXT,
    required_guards_count INT NOT NULL,
    created_by INT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (created_by) REFERENCES users(id)
);

-- Таблица назначений на смену
CREATE TABLE shift_assignments (
    id INT PRIMARY KEY AUTO_INCREMENT,
    shift_id INT,
    employee_id INT,
    medical_status ENUM('pending', 'passed', 'failed') DEFAULT 'pending',
    route_familiarized BOOLEAN DEFAULT FALSE,
    briefing_completed BOOLEAN DEFAULT FALSE,
    assigned_by INT,
    assigned_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (shift_id) REFERENCES shifts(id) ON DELETE CASCADE,
    FOREIGN KEY (employee_id) REFERENCES employees(id),
    FOREIGN KEY (assigned_by) REFERENCES users(id)
);

-- Таблица медицинских осмотров
CREATE TABLE medical_exams (
    id INT PRIMARY KEY AUTO_INCREMENT,
    employee_id INT,
    exam_date DATE NOT NULL,
    exam_result ENUM('passed', 'failed') NOT NULL,
    notes TEXT,
    examined_by INT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (employee_id) REFERENCES employees(id),
    FOREIGN KEY (examined_by) REFERENCES users(id)
);

-- Начальные данные
INSERT INTO users (username, password, full_name, role) VALUES 
('admin', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Администратор Системы', 'admin'),
('senior1', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Старший Смены 1', 'senior'),
('medic1', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Медик 1', 'medic');