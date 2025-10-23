-- ASAA Healthcare Management System Database Schema
-- MySQL 8.0+ with utf8mb4 character set

CREATE DATABASE IF NOT EXISTS asaa_healthcare CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE asaa_healthcare;

-- 1. Users (all user types)
CREATE TABLE users (
    user_id INT PRIMARY KEY AUTO_INCREMENT,
    first_name VARCHAR(50) NOT NULL,
    last_name VARCHAR(50) NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    password_hash VARCHAR(255) NOT NULL,
    mobile_number VARCHAR(15) NOT NULL,
    nic VARCHAR(12) UNIQUE NOT NULL,
    gender ENUM('Male', 'Female', 'Other') NOT NULL,
    role ENUM('Admin', 'Staff', 'Doctor', 'Patient') NOT NULL,
    position VARCHAR(100) NULL,
    status ENUM('ACTIVE', 'INACTIVE', 'SUSPENDED') DEFAULT 'ACTIVE',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    created_by INT NULL,
    FOREIGN KEY (created_by) REFERENCES users(user_id) ON DELETE SET NULL
);

-- 2. Specializations
CREATE TABLE specializations (
    specialization_id INT PRIMARY KEY AUTO_INCREMENT,
    specialization_name VARCHAR(100) NOT NULL,
    description TEXT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- 3. Doctor Details
CREATE TABLE doctor_details (
    doctor_detail_id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT NOT NULL,
    specialization_id INT NOT NULL,
    consultation_fee DECIMAL(10,2) NOT NULL DEFAULT 0.00,
    experience_years INT DEFAULT 0,
    qualifications TEXT NULL,
    bio TEXT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE CASCADE,
    FOREIGN KEY (specialization_id) REFERENCES specializations(specialization_id) ON DELETE RESTRICT
);

-- 4. Appointments
CREATE TABLE appointments (
    appointment_id INT PRIMARY KEY AUTO_INCREMENT,
    patient_id INT NOT NULL,
    doctor_id INT NOT NULL,
    appointment_date DATE NOT NULL,
    time_slot TIME NOT NULL,
    status ENUM('REVIEW', 'ACCEPTED', 'CONSULTING', 'COMPLETED', 'CANCELLED') DEFAULT 'REVIEW',
    payment_status ENUM('PENDING', 'COMPLETED') DEFAULT 'PENDING',
    notes TEXT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    created_by INT NOT NULL,
    FOREIGN KEY (patient_id) REFERENCES users(user_id) ON DELETE CASCADE,
    FOREIGN KEY (doctor_id) REFERENCES users(user_id) ON DELETE CASCADE,
    FOREIGN KEY (created_by) REFERENCES users(user_id) ON DELETE CASCADE,
    UNIQUE KEY unique_appointment (doctor_id, appointment_date, time_slot)
);

-- 5. Medical Records
CREATE TABLE medical_records (
    record_id INT PRIMARY KEY AUTO_INCREMENT,
    appointment_id INT NOT NULL,
    patient_id INT NOT NULL,
    doctor_id INT NOT NULL,
    current_diagnosis TEXT NOT NULL,
    prescription TEXT NULL,
    next_visit_date DATE NULL,
    notes TEXT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (appointment_id) REFERENCES appointments(appointment_id) ON DELETE CASCADE,
    FOREIGN KEY (patient_id) REFERENCES users(user_id) ON DELETE CASCADE,
    FOREIGN KEY (doctor_id) REFERENCES users(user_id) ON DELETE CASCADE
);

-- 6. Payments
CREATE TABLE payments (
    payment_id INT PRIMARY KEY AUTO_INCREMENT,
    appointment_id INT NOT NULL,
    patient_id INT NOT NULL,
    doctor_id INT NOT NULL,
    amount DECIMAL(10,2) NOT NULL,
    payment_method ENUM('CARD', 'CASH') NOT NULL,
    payment_status ENUM('PENDING', 'COMPLETED', 'FAILED') DEFAULT 'PENDING',
    payment_date TIMESTAMP NULL,
    transaction_reference VARCHAR(100) NULL,
    paid_by INT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (appointment_id) REFERENCES appointments(appointment_id) ON DELETE CASCADE,
    FOREIGN KEY (patient_id) REFERENCES users(user_id) ON DELETE CASCADE,
    FOREIGN KEY (doctor_id) REFERENCES users(user_id) ON DELETE CASCADE,
    FOREIGN KEY (paid_by) REFERENCES users(user_id) ON DELETE SET NULL
);

-- 7. Permissions
CREATE TABLE permissions (
    permission_id INT PRIMARY KEY AUTO_INCREMENT,
    permission_name VARCHAR(100) NOT NULL,
    description TEXT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- 8. Role Permissions
CREATE TABLE role_permissions (
    role_permission_id INT PRIMARY KEY AUTO_INCREMENT,
    role ENUM('Admin', 'Staff', 'Doctor', 'Patient') NOT NULL,
    permission_id INT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (permission_id) REFERENCES permissions(permission_id) ON DELETE CASCADE,
    UNIQUE KEY unique_role_permission (role, permission_id)
);

-- 9. Email Logs
CREATE TABLE email_logs (
    email_log_id INT PRIMARY KEY AUTO_INCREMENT,
    recipient_email VARCHAR(100) NOT NULL,
    subject VARCHAR(255) NOT NULL,
    status ENUM('SENT', 'FAILED', 'PENDING') NOT NULL,
    sent_at TIMESTAMP NULL,
    error_message TEXT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- 10. Audit Trail
CREATE TABLE audit_trail (
    audit_id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT NULL,
    action VARCHAR(100) NOT NULL,
    table_name VARCHAR(50) NOT NULL,
    record_id INT NULL,
    old_values JSON NULL,
    new_values JSON NULL,
    ip_address VARCHAR(45) NULL,
    user_agent TEXT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE SET NULL
);

-- Indexes for performance optimization
CREATE INDEX idx_users_email ON users(email);
CREATE INDEX idx_users_role ON users(role);
CREATE INDEX idx_users_status ON users(status);
CREATE INDEX idx_appointments_patient ON appointments(patient_id);
CREATE INDEX idx_appointments_doctor ON appointments(doctor_id);
CREATE INDEX idx_appointments_date ON appointments(appointment_date);
CREATE INDEX idx_appointments_status ON appointments(status);
CREATE INDEX idx_medical_records_patient ON medical_records(patient_id);
CREATE INDEX idx_medical_records_doctor ON medical_records(doctor_id);
CREATE INDEX idx_payments_appointment ON payments(appointment_id);
CREATE INDEX idx_payments_patient ON payments(patient_id);
CREATE INDEX idx_audit_trail_user ON audit_trail(user_id);
CREATE INDEX idx_audit_trail_table ON audit_trail(table_name);
CREATE INDEX idx_audit_trail_created ON audit_trail(created_at);

-- Views for common queries
CREATE VIEW v_appointment_details AS
SELECT 
    a.appointment_id,
    a.appointment_date,
    a.time_slot,
    a.status,
    a.payment_status,
    a.notes,
    p.first_name as patient_first_name,
    p.last_name as patient_last_name,
    p.email as patient_email,
    p.mobile_number as patient_mobile,
    d.first_name as doctor_first_name,
    d.last_name as doctor_last_name,
    s.specialization_name,
    dd.consultation_fee
FROM appointments a
JOIN users p ON a.patient_id = p.user_id
JOIN users d ON a.doctor_id = d.user_id
LEFT JOIN doctor_details dd ON d.user_id = dd.user_id
LEFT JOIN specializations s ON dd.specialization_id = s.specialization_id;

CREATE VIEW v_medical_record_details AS
SELECT 
    mr.record_id,
    mr.current_diagnosis,
    mr.prescription,
    mr.next_visit_date,
    mr.notes,
    mr.created_at,
    a.appointment_date,
    a.time_slot,
    p.first_name as patient_first_name,
    p.last_name as patient_last_name,
    d.first_name as doctor_first_name,
    d.last_name as doctor_last_name,
    s.specialization_name
FROM medical_records mr
JOIN appointments a ON mr.appointment_id = a.appointment_id
JOIN users p ON mr.patient_id = p.user_id
JOIN users d ON mr.doctor_id = d.user_id
LEFT JOIN doctor_details dd ON d.user_id = dd.user_id
LEFT JOIN specializations s ON dd.specialization_id = s.specialization_id;

-- Stored Procedures
DELIMITER //

-- Procedure to get available time slots for a doctor on a specific date
CREATE PROCEDURE GetAvailableSlots(IN p_doctor_id INT, IN p_date DATE)
BEGIN
    DECLARE v_start_time TIME DEFAULT '08:00:00';
    DECLARE v_end_time TIME DEFAULT '22:00:00';
    DECLARE v_lunch_start TIME DEFAULT '12:00:00';
    DECLARE v_lunch_end TIME DEFAULT '13:00:00';
    DECLARE v_current_time TIME;
    DECLARE v_slot_count INT DEFAULT 0;
    
    -- Create temporary table for available slots
    CREATE TEMPORARY TABLE temp_slots (
        time_slot TIME,
        is_available BOOLEAN DEFAULT TRUE
    );
    
    -- Generate all possible time slots
    SET v_current_time = v_start_time;
    WHILE v_current_time < v_end_time DO
        -- Skip lunch break
        IF v_current_time < v_lunch_start OR v_current_time >= v_lunch_end THEN
            INSERT INTO temp_slots (time_slot) VALUES (v_current_time);
        END IF;
        SET v_current_time = ADDTIME(v_current_time, '01:00:00');
    END WHILE;
    
    -- Mark booked slots as unavailable
    UPDATE temp_slots ts
    SET is_available = FALSE
    WHERE EXISTS (
        SELECT 1 FROM appointments a
        WHERE a.doctor_id = p_doctor_id
        AND a.appointment_date = p_date
        AND a.time_slot = ts.time_slot
        AND a.status NOT IN ('CANCELLED')
    );
    
    -- Return available slots
    SELECT time_slot FROM temp_slots WHERE is_available = TRUE ORDER BY time_slot;
    
    DROP TEMPORARY TABLE temp_slots;
END //

-- Procedure to update appointment status with audit trail
CREATE PROCEDURE UpdateAppointmentStatus(
    IN p_appointment_id INT,
    IN p_new_status VARCHAR(20),
    IN p_user_id INT
)
BEGIN
    DECLARE v_old_status VARCHAR(20);
    DECLARE v_patient_id INT;
    DECLARE v_doctor_id INT;
    
    -- Get current status and related IDs
    SELECT status, patient_id, doctor_id INTO v_old_status, v_patient_id, v_doctor_id
    FROM appointments WHERE appointment_id = p_appointment_id;
    
    -- Update appointment status
    UPDATE appointments 
    SET status = p_new_status, updated_at = CURRENT_TIMESTAMP
    WHERE appointment_id = p_appointment_id;
    
    -- Log audit trail
    INSERT INTO audit_trail (user_id, action, table_name, record_id, old_values, new_values)
    VALUES (p_user_id, 'UPDATE', 'appointments', p_appointment_id, 
            JSON_OBJECT('status', v_old_status), 
            JSON_OBJECT('status', p_new_status));
END //

DELIMITER ;

-- Triggers for audit trail
DELIMITER //

-- Trigger for user updates
CREATE TRIGGER tr_users_update_audit
AFTER UPDATE ON users
FOR EACH ROW
BEGIN
    INSERT INTO audit_trail (user_id, action, table_name, record_id, old_values, new_values)
    VALUES (NEW.user_id, 'UPDATE', 'users', NEW.user_id,
            JSON_OBJECT('first_name', OLD.first_name, 'last_name', OLD.last_name, 'email', OLD.email, 'status', OLD.status),
            JSON_OBJECT('first_name', NEW.first_name, 'last_name', NEW.last_name, 'email', NEW.email, 'status', NEW.status));
END //

-- Trigger for appointment updates
CREATE TRIGGER tr_appointments_update_audit
AFTER UPDATE ON appointments
FOR EACH ROW
BEGIN
    INSERT INTO audit_trail (user_id, action, table_name, record_id, old_values, new_values)
    VALUES (NEW.created_by, 'UPDATE', 'appointments', NEW.appointment_id,
            JSON_OBJECT('status', OLD.status, 'payment_status', OLD.payment_status),
            JSON_OBJECT('status', NEW.status, 'payment_status', NEW.payment_status));
END //

DELIMITER ;

-- Sample data insertion
INSERT INTO specializations (specialization_name, description) VALUES
('General Medicine', 'General medical practice and primary care'),
('Cardiology', 'Heart and cardiovascular system specialist'),
('Dermatology', 'Skin, hair, and nail specialist'),
('Pediatrics', 'Medical care for infants, children, and adolescents'),
('Orthopedics', 'Musculoskeletal system specialist'),
('Neurology', 'Nervous system specialist'),
('Gynecology', 'Women\'s reproductive health specialist'),
('Psychiatry', 'Mental health and behavioral disorders specialist');

-- Insert default admin user
INSERT INTO users (first_name, last_name, email, password_hash, mobile_number, nic, gender, role, status) VALUES
('Admin', 'User', 'admin@asaahealthcare.com', '$2y$12$LQv3c1yqBWVHxkd0LHAkCOYz6TtxMQJqhN8/LewdBPj4J8QzQzQzQ', '0771234567', '123456789V', 'Male', 'Admin', 'ACTIVE');

-- Insert sample permissions
INSERT INTO permissions (permission_name, description) VALUES
('manage_users', 'Create, read, update, delete users'),
('manage_appointments', 'Create, read, update, delete appointments'),
('manage_medical_records', 'Create, read, update, delete medical records'),
('manage_payments', 'Create, read, update, delete payments'),
('view_reports', 'View analytics and reports'),
('manage_specializations', 'Manage doctor specializations'),
('send_emails', 'Send email notifications'),
('view_audit_trail', 'View system audit trail');

-- Insert role permissions
INSERT INTO role_permissions (role, permission_id) VALUES
('Admin', 1), ('Admin', 2), ('Admin', 3), ('Admin', 4), ('Admin', 5), ('Admin', 6), ('Admin', 7), ('Admin', 8),
('Staff', 2), ('Staff', 3), ('Staff', 4), ('Staff', 5), ('Staff', 7),
('Doctor', 2), ('Doctor', 3), ('Doctor', 5),
('Patient', 2), ('Patient', 3);