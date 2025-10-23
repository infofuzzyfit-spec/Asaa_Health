-- ASAA Healthcare Management System Sample Data
-- Seed data for testing and development
USE asaa_healthcare;
-- Insert sample doctors
INSERT INTO users (
        first_name,
        last_name,
        email,
        password_hash,
        mobile_number,
        nic,
        gender,
        role,
        status,
        position
    )
VALUES (
        'Dr. Sarah',
        'Johnson',
        'sarah.johnson@asaahealthcare.com',
        '$2y$12$LQv3c1yqBWVHxkd0LHAkCOYz6TtxMQJqhN8/LewdBPj4J8QzQzQzQ',
        '0771234568',
        '123456789V',
        'Female',
        'Doctor',
        'ACTIVE',
        'Senior Cardiologist'
    ),
    (
        'Dr. Michael',
        'Chen',
        'michael.chen@asaahealthcare.com',
        '$2y$12$LQv3c1yqBWVHxkd0LHAkCOYz6TtxMQJqhN8/LewdBPj4J8QzQzQzQ',
        '0771234569',
        '123456790V',
        'Male',
        'Doctor',
        'ACTIVE',
        'Dermatologist'
    ),
    (
        'Dr. Emily',
        'Rodriguez',
        'emily.rodriguez@asaahealthcare.com',
        '$2y$12$LQv3c1yqBWVHxkd0LHAkCOYz6TtxMQJqhN8/LewdBPj4J8QzQzQzQ',
        '0771234570',
        '123456791V',
        'Female',
        'Doctor',
        'ACTIVE',
        'Pediatrician'
    ),
    (
        'Dr. David',
        'Kim',
        'david.kim@asaahealthcare.com',
        '$2y$12$LQv3c1yqBWVHxkd0LHAkCOYz6TtxMQJqhN8/LewdBPj4J8QzQzQzQ',
        '0771234571',
        '123456792V',
        'Male',
        'Doctor',
        'ACTIVE',
        'Orthopedic Surgeon'
    ),
    (
        'Dr. Lisa',
        'Wang',
        'lisa.wang@asaahealthcare.com',
        '$2y$12$LQv3c1yqBWVHxkd0LHAkCOYz6TtxMQJqhN8/LewdBPj4J8QzQzQzQ',
        '0771234572',
        '123456793V',
        'Female',
        'Doctor',
        'ACTIVE',
        'Neurologist'
    );
-- Insert sample patients
INSERT INTO users (
        first_name,
        last_name,
        email,
        password_hash,
        mobile_number,
        nic,
        gender,
        role,
        status
    )
VALUES (
        'John',
        'Smith',
        'john.smith@email.com',
        '$2y$12$LQv3c1yqBWVHxkd0LHAkCOYz6TtxMQJqhN8/LewdBPj4J8QzQzQzQ',
        '0771234573',
        '123456794V',
        'Male',
        'Patient',
        'ACTIVE'
    ),
    (
        'Jane',
        'Doe',
        'jane.doe@email.com',
        '$2y$12$LQv3c1yqBWVHxkd0LHAkCOYz6TtxMQJqhN8/LewdBPj4J8QzQzQzQ',
        '0771234574',
        '123456795V',
        'Female',
        'Patient',
        'ACTIVE'
    ),
    (
        'Robert',
        'Brown',
        'robert.brown@email.com',
        '$2y$12$LQv3c1yqBWVHxkd0LHAkCOYz6TtxMQJqhN8/LewdBPj4J8QzQzQzQ',
        '0771234575',
        '123456796V',
        'Male',
        'Patient',
        'ACTIVE'
    ),
    (
        'Maria',
        'Garcia',
        'maria.garcia@email.com',
        '$2y$12$LQv3c1yqBWVHxkd0LHAkCOYz6TtxMQJqhN8/LewdBPj4J8QzQzQzQ',
        '0771234576',
        '123456797V',
        'Female',
        'Patient',
        'ACTIVE'
    ),
    (
        'James',
        'Wilson',
        'james.wilson@email.com',
        '$2y$12$LQv3c1yqBWVHxkd0LHAkCOYz6TtxMQJqhN8/LewdBPj4J8QzQzQzQ',
        '0771234577',
        '123456798V',
        'Male',
        'Patient',
        'ACTIVE'
    );
-- Insert sample staff
INSERT INTO users (
        first_name,
        last_name,
        email,
        password_hash,
        mobile_number,
        nic,
        gender,
        role,
        status,
        position
    )
VALUES (
        'Alice',
        'Thompson',
        'alice.thompson@asaahealthcare.com',
        '$2y$12$LQv3c1yqBWVHxkd0LHAkCOYz6TtxMQJqhN8/LewdBPj4J8QzQzQzQ',
        '0771234578',
        '123456799V',
        'Female',
        'Staff',
        'ACTIVE',
        'Receptionist'
    ),
    (
        'Bob',
        'Anderson',
        'bob.anderson@asaahealthcare.com',
        '$2y$12$LQv3c1yqBWVHxkd0LHAkCOYz6TtxMQJqhN8/LewdBPj4J8QzQzQzQ',
        '0771234579',
        '123456800V',
        'Male',
        'Staff',
        'ACTIVE',
        'Nurse'
    ),
    (
        'Carol',
        'Martinez',
        'carol.martinez@asaahealthcare.com',
        '$2y$12$LQv3c1yqBWVHxkd0LHAkCOYz6TtxMQJqhN8/LewdBPj4J8QzQzQzQ',
        '0771234580',
        '123456801V',
        'Female',
        'Staff',
        'ACTIVE',
        'Administrative Assistant'
    );
-- Insert doctor details
INSERT INTO doctor_details (
        user_id,
        specialization_id,
        consultation_fee,
        experience_years,
        qualifications,
        bio
    )
VALUES (
        2,
        1,
        2500.00,
        10,
        'MBBS, MD in Internal Medicine',
        'Dr. Sarah Johnson is a highly experienced cardiologist with over 10 years of practice in cardiovascular medicine.'
    ),
    (
        3,
        3,
        2000.00,
        8,
        'MBBS, MD in Dermatology',
        'Dr. Michael Chen specializes in dermatology and has extensive experience in treating various skin conditions.'
    ),
    (
        4,
        4,
        1800.00,
        6,
        'MBBS, MD in Pediatrics',
        'Dr. Emily Rodriguez is a dedicated pediatrician with a passion for children\'s health and development.'
    ),
    (
        5,
        5,
        3000.00,
        12,
        'MBBS, MS in Orthopedics',
        'Dr. David Kim is an orthopedic surgeon specializing in joint replacement and sports medicine.'
    ),
    (
        6,
        6,
        2800.00,
        9,
        'MBBS, MD in Neurology',
        'Dr. Lisa Wang is a neurologist with expertise in treating neurological disorders and conditions.'
    );
-- Insert sample appointments
INSERT INTO appointments (
        patient_id,
        doctor_id,
        appointment_date,
        time_slot,
        status,
        payment_status,
        notes,
        created_by
    )
VALUES (
        7,
        2,
        '2024-01-15',
        '09:00:00',
        'COMPLETED',
        'COMPLETED',
        'Regular checkup for heart condition',
        7
    ),
    (
        8,
        3,
        '2024-01-16',
        '10:00:00',
        'ACCEPTED',
        'PENDING',
        'Skin allergy consultation',
        8
    ),
    (
        9,
        4,
        '2024-01-17',
        '11:00:00',
        'REVIEW',
        'PENDING',
        'Child vaccination appointment',
        9
    ),
    (
        10,
        5,
        '2024-01-18',
        '14:00:00',
        'ACCEPTED',
        'COMPLETED',
        'Knee pain consultation',
        10
    ),
    (
        11,
        6,
        '2024-01-19',
        '15:00:00',
        'CONSULTING',
        'PENDING',
        'Headache evaluation',
        11
    ),
    (
        7,
        2,
        '2024-01-20',
        '16:00:00',
        'REVIEW',
        'PENDING',
        'Follow-up appointment',
        7
    ),
    (
        8,
        3,
        '2024-01-21',
        '09:30:00',
        'ACCEPTED',
        'COMPLETED',
        'Dermatology follow-up',
        8
    );
-- Insert sample medical records
INSERT INTO medical_records (
        appointment_id,
        patient_id,
        doctor_id,
        current_diagnosis,
        prescription,
        next_visit_date,
        notes
    )
VALUES (
        1,
        7,
        2,
        'Hypertension - Well controlled',
        'Continue current medication. Monitor blood pressure weekly.',
        '2024-02-15',
        'Patient responding well to treatment. Blood pressure stable.'
    ),
    (
        2,
        8,
        3,
        'Contact Dermatitis',
        'Topical corticosteroid cream. Avoid known allergens.',
        '2024-02-16',
        'Mild allergic reaction. Patient advised to avoid specific triggers.'
    ),
    (
        4,
        10,
        5,
        'Osteoarthritis of Right Knee',
        'Pain management with NSAIDs. Physical therapy recommended.',
        '2024-02-18',
        'X-ray shows mild osteoarthritis. Conservative treatment approach.'
    ),
    (
        5,
        11,
        6,
        'Tension Headache',
        'Stress management techniques. Over-the-counter pain relief as needed.',
        '2024-02-19',
        'No neurological abnormalities found. Headache likely stress-related.'
    );
-- Insert sample payments
INSERT INTO payments (
        appointment_id,
        patient_id,
        doctor_id,
        amount,
        payment_method,
        payment_status,
        payment_date,
        transaction_reference,
        paid_by
    )
VALUES (
        1,
        7,
        2,
        2500.00,
        'CARD',
        'COMPLETED',
        '2024-01-15 09:30:00',
        'TXN123456789',
        7
    ),
    (
        2,
        8,
        3,
        2000.00,
        'CASH',
        'COMPLETED',
        '2024-01-16 10:30:00',
        NULL,
        8
    ),
    (
        4,
        10,
        5,
        3000.00,
        'CARD',
        'COMPLETED',
        '2024-01-18 14:30:00',
        'TXN123456790',
        10
    ),
    (
        5,
        11,
        6,
        2800.00,
        'CASH',
        'PENDING',
        NULL,
        NULL,
        NULL
    );
-- Insert sample email logs
INSERT INTO email_logs (
        recipient_email,
        subject,
        status,
        sent_at,
        error_message
    )
VALUES (
        'john.smith@email.com',
        'Appointment Confirmed - ASAA Healthcare',
        'SENT',
        '2024-01-15 09:00:00',
        NULL
    ),
    (
        'jane.doe@email.com',
        'Appointment Under Review - ASAA Healthcare',
        'SENT',
        '2024-01-16 10:00:00',
        NULL
    ),
    (
        'robert.brown@email.com',
        'Appointment Confirmed - ASAA Healthcare',
        'SENT',
        '2024-01-17 11:00:00',
        NULL
    ),
    (
        'maria.garcia@email.com',
        'Appointment Confirmed - ASAA Healthcare',
        'SENT',
        '2024-01-18 14:00:00',
        NULL
    ),
    (
        'james.wilson@email.com',
        'Appointment Under Review - ASAA Healthcare',
        'FAILED',
        NULL,
        'SMTP connection timeout'
    );
-- Insert sample audit trail entries
INSERT INTO audit_trail (
        user_id,
        action,
        table_name,
        record_id,
        old_values,
        new_values,
        ip_address,
        user_agent
    )
VALUES (
        1,
        'CREATE',
        'users',
        2,
        NULL,
        '{"first_name": "Dr. Sarah", "last_name": "Johnson", "role": "Doctor"}',
        '192.168.1.100',
        'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36'
    ),
    (
        1,
        'CREATE',
        'appointments',
        1,
        NULL,
        '{"patient_id": 7, "doctor_id": 2, "status": "REVIEW"}',
        '192.168.1.100',
        'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36'
    ),
    (
        2,
        'UPDATE',
        'appointments',
        1,
        '{"status": "REVIEW"}',
        '{"status": "ACCEPTED"}',
        '192.168.1.101',
        'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36'
    ),
    (
        2,
        'UPDATE',
        'appointments',
        1,
        '{"status": "ACCEPTED"}',
        '{"status": "COMPLETED"}',
        '192.168.1.101',
        'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36'
    ),
    (
        1,
        'CREATE',
        'medical_records',
        1,
        NULL,
        '{"appointment_id": 1, "current_diagnosis": "Hypertension - Well controlled"}',
        '192.168.1.100',
        'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36'
    );
-- Update the todo list to mark Step 2 as completed