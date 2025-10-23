# CURSOR AI DEVELOPMENT PROMPT
## ASAA Healthcare Appointment Management System

### 🎯 PROJECT OVERVIEW
You are tasked with building a **healthcare appointment management system** using **Component-Based MVC Architecture** with the following tech stack:

**Frontend:**
- HTML5
- CSS3
- Bootstrap 5.3
- JavaScript (ES6+)
- jQuery
- Select2 (doctor search)
- FullCalendar.js
- Chart.js (for analytics)

**Backend:**
- PHP 8.2+
- MySQL 8.0+
- PHPMailer (email notifications)
- PayHere Payment Gateway (Sandbox mode)

**Development Environment:**
- Apache Server (XAMPP/WAMP - Local)
- Composer for dependency management

---

## 📋 CRITICAL BUSINESS RULES

### 1. **Time Slot Availability Logic** ⚠️ IMPORTANT
```
RULE: One time slot can ONLY be booked ONCE per doctor per day
- When patient selects a date, query database for already booked slots
- Display time slots 08:00-22:00 (exclude 12:00-13:00 lunch break)
- For each slot, check:
  1. Is it already booked by another patient? → Disable with 🔒 lock icon
  2. Has the time passed (only for today)? → Disable and grey out
  3. Otherwise → Enable (blue, clickable)
- Backend MUST use database lock (FOR UPDATE) to prevent race conditions
```

**SQL Query for Slot Availability:**
```sql
SELECT time_slot 
FROM appointments 
WHERE doctor_id = ? 
AND appointment_date = ? 
AND status NOT IN ('CANCELLED')
```

### 2. **Payment Methods** 💳💵
```
TWO payment options:
1. CARD Payment (Online via PayHere)
   - Patient selects "Pay by Card"
   - Redirect to PayHere gateway
   - Webhook receives payment confirmation
   - Auto-update: payment_status = COMPLETED, payment_method = CARD
   
2. CASH Payment (At Reception)
   - Patient pays cash at front desk
   - Staff manually marks payment in system
   - payment_status = COMPLETED, payment_method = CASH, paid_by = staff_user_id
```

### 3. **Appointment Cancellation Rules**
```
Patient can cancel appointment ONLY IF:
1. Time remaining until appointment > 3 hours
2. payment_status != COMPLETED

JavaScript validation AND backend re-validation required
```

---

## 🏗️ PROJECT STRUCTURE

Create this **exact folder structure**:

```
healthcare-appointment-system/
│
├── config/
│   ├── database.php          # PDO connection
│   ├── email.php             # PHPMailer config
│   ├── payhere.php           # PayHere sandbox credentials
│   └── constants.php         # System constants
│
├── core/                     # Framework core
│   ├── Database.php          # Singleton pattern
│   ├── Controller.php        # Base controller
│   ├── Model.php             # Base model
│   ├── Router.php            # URL routing
│   ├── Request.php
│   ├── Response.php
│   ├── Session.php
│   └── Validator.php
│
├── app/
│   ├── controllers/
│   │   ├── AuthController.php
│   │   ├── AppointmentController.php
│   │   ├── UserController.php
│   │   ├── PaymentController.php
│   │   ├── MedicalRecordController.php
│   │   └── ReportController.php
│   │
│   ├── models/
│   │   ├── User.php
│   │   ├── Appointment.php
│   │   ├── MedicalRecord.php
│   │   ├── Payment.php
│   │   └── Doctor.php
│   │
│   ├── services/             # Business logic layer
│   │   ├── AuthService.php
│   │   ├── AppointmentService.php
│   │   ├── EmailService.php
│   │   ├── PaymentService.php
│   │   └── ReportService.php
│   │
│   ├── repositories/         # Data access layer
│   │   ├── UserRepository.php
│   │   ├── AppointmentRepository.php
│   │   ├── PaymentRepository.php
│   │   └── MedicalRecordRepository.php
│   │
│   └── middleware/
│       ├── AuthMiddleware.php
│       ├── RBACMiddleware.php
│       └── CSRFMiddleware.php
│
├── components/               # ⭐ REUSABLE COMPONENTS
│   ├── Component.php         # Base component class
│   │
│   ├── ui/
│   │   ├── Card.php
│   │   ├── Table.php
│   │   ├── DataTable.php
│   │   ├── Modal.php
│   │   ├── Alert.php
│   │   ├── FormInput.php
│   │   ├── Select2.php
│   │   ├── DatePicker.php
│   │   ├── Pagination.php
│   │   ├── Badge.php
│   │   └── Button.php
│   │
│   ├── layout/
│   │   ├── Header.php
│   │   ├── Footer.php
│   │   ├── Sidebar.php
│   │   ├── Navbar.php
│   │   └── Breadcrumb.php
│   │
│   ├── forms/
│   │   ├── AppointmentBookingForm.php
│   │   ├── UserForm.php
│   │   ├── PaymentForm.php
│   │   └── LoginForm.php
│   │
│   └── charts/
│       ├── LineChart.php
│       ├── BarChart.php
│       └── PieChart.php
│
├── views/
│   ├── frontend/
│   │   ├── home.php
│   │   ├── login.php
│   │   ├── register.php
│   │   └── my-appointments.php
│   │
│   ├── admin/
│   │   ├── dashboard.php
│   │   ├── users/
│   │   ├── appointments/
│   │   ├── payments/
│   │   └── reports/
│   │
│   ├── doctor/
│   │   └── dashboard.php
│   │
│   ├── staff/
│   │   └── dashboard.php
│   │
│   └── email-templates/
│       ├── appointment-review.html
│       ├── appointment-accepted.html
│       ├── appointment-cancelled.html
│       ├── appointment-consulting.html
│       ├── appointment-completed.html
│       └── new-user-credentials.html
│
├── public/
│   ├── index.php             # Front controller
│   ├── assets/
│   │   ├── css/
│   │   │   ├── bootstrap.min.css
│   │   │   ├── custom.css
│   │   │   └── components.css
│   │   ├── js/
│   │   │   ├── bootstrap.bundle.min.js
│   │   │   ├── jquery.min.js
│   │   │   ├── chart.js
│   │   │   ├── select2.min.js
│   │   │   └── components/
│   │   │       ├── appointment-booking.js
│   │   │       ├── datatable.js
│   │   │       └── payment.js
│   │   └── images/
│   └── uploads/
│
├── database/
│   ├── schema.sql            # Complete database schema
│   ├── seeds.sql             # Sample data
│   └── backup/
│
├── logs/
│   ├── error.log
│   └── email.log
│
├── vendor/                   # Composer dependencies
│
├── .env                      # Environment config
├── .htaccess                 # URL rewriting
├── composer.json
└── README.md
```

---

## 🗄️ DATABASE SCHEMA

Create **ALL** these tables with proper relationships:

```sql
-- 1. Users (all user types)
CREATE TABLE users (
    user_id INT PRIMARY KEY AUTO_INCREMENT,
    first_name VARCHAR(100) NOT NULL,
    last_name VARCHAR(100) NOT NULL,
    email VARCHAR(150) UNIQUE NOT NULL,
    password_hash VARCHAR(255) NOT NULL,
    mobile_number VARCHAR(15) NOT NULL,
    nic VARCHAR(20) UNIQUE NOT NULL,
    gender ENUM('Male', 'Female', 'Other') NOT NULL,
    role ENUM('Admin', 'Staff', 'Doctor', 'Patient') NOT NULL,
    position VARCHAR(100) DEFAULT 'N/A',
    status ENUM('ACTIVE', 'INACTIVE') DEFAULT 'ACTIVE',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_email (email),
    INDEX idx_role (role)
) ENGINE=InnoDB;

-- 2. Specializations
CREATE TABLE specializations (
    specialization_id INT PRIMARY KEY AUTO_INCREMENT,
    specialization_name VARCHAR(100) NOT NULL UNIQUE,
    description TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- 3. Doctor Details
CREATE TABLE doctor_details (
    doctor_detail_id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT NOT NULL,
    specialization_id INT NOT NULL,
    license_number VARCHAR(50) UNIQUE,
    qualification TEXT,
    experience_years INT,
    consultation_fee DECIMAL(10,2),
    FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE CASCADE,
    FOREIGN KEY (specialization_id) REFERENCES specializations(specialization_id)
) ENGINE=InnoDB;

-- 4. Appointments
CREATE TABLE appointments (
    appointment_id INT PRIMARY KEY AUTO_INCREMENT,
    patient_id INT NOT NULL,
    doctor_id INT NOT NULL,
    appointment_date DATE NOT NULL,
    time_slot VARCHAR(20) NOT NULL,
    status ENUM('REVIEW', 'ACCEPTED', 'CONSULTING', 'COMPLETED', 'CANCELLED') DEFAULT 'REVIEW',
    payment_status ENUM('PENDING', 'COMPLETED') DEFAULT 'PENDING',
    notes TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    created_by INT,
    FOREIGN KEY (patient_id) REFERENCES users(user_id),
    FOREIGN KEY (doctor_id) REFERENCES users(user_id),
    INDEX idx_appointment_date (appointment_date),
    INDEX idx_status (status),
    UNIQUE KEY unique_doctor_slot (doctor_id, appointment_date, time_slot)
) ENGINE=InnoDB;

-- 5. Medical Records
CREATE TABLE medical_records (
    record_id INT PRIMARY KEY AUTO_INCREMENT,
    appointment_id INT NOT NULL,
    patient_id INT NOT NULL,
    doctor_id INT NOT NULL,
    current_diagnosis TEXT NOT NULL,
    prescription TEXT,
    next_visit_date DATE,
    notes TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (appointment_id) REFERENCES appointments(appointment_id),
    FOREIGN KEY (patient_id) REFERENCES users(user_id),
    FOREIGN KEY (doctor_id) REFERENCES users(user_id)
) ENGINE=InnoDB;

-- 6. Payments
CREATE TABLE payments (
    payment_id INT PRIMARY KEY AUTO_INCREMENT,
    appointment_id INT NOT NULL,
    patient_id INT NOT NULL,
    doctor_id INT NOT NULL,
    amount DECIMAL(10,2) NOT NULL,
    payment_method ENUM('CARD', 'CASH') DEFAULT NULL,
    payment_status ENUM('PENDING', 'COMPLETED') DEFAULT 'PENDING',
    transaction_reference VARCHAR(100),
    payment_date TIMESTAMP NULL,
    paid_by INT,
    notes TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (appointment_id) REFERENCES appointments(appointment_id),
    FOREIGN KEY (patient_id) REFERENCES users(user_id),
    FOREIGN KEY (doctor_id) REFERENCES users(user_id),
    FOREIGN KEY (paid_by) REFERENCES users(user_id),
    INDEX idx_payment_status (payment_status)
) ENGINE=InnoDB;

-- 7. Permissions (RBAC)
CREATE TABLE permissions (
    permission_id INT PRIMARY KEY AUTO_INCREMENT,
    permission_name VARCHAR(100) UNIQUE NOT NULL,
    description TEXT,
    module VARCHAR(50)
) ENGINE=InnoDB;

-- 8. Role Permissions
CREATE TABLE role_permissions (
    role_permission_id INT PRIMARY KEY AUTO_INCREMENT,
    role ENUM('Admin', 'Staff', 'Doctor', 'Patient') NOT NULL,
    permission_id INT NOT NULL,
    FOREIGN KEY (permission_id) REFERENCES permissions(permission_id),
    UNIQUE KEY unique_role_permission (role, permission_id)
) ENGINE=InnoDB;

-- 9. Email Logs
CREATE TABLE email_logs (
    log_id INT PRIMARY KEY AUTO_INCREMENT,
    recipient_email VARCHAR(150) NOT NULL,
    subject VARCHAR(255),
    message TEXT,
    status ENUM('SENT', 'FAILED') DEFAULT 'SENT',
    sent_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- 10. Audit Trail
CREATE TABLE audit_trail (
    audit_id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT,
    action VARCHAR(100) NOT NULL,
    table_name VARCHAR(100),
    record_id INT,
    old_value TEXT,
    new_value TEXT,
    ip_address VARCHAR(45),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(user_id)
) ENGINE=InnoDB;
```

---

## 🎨 COMPONENT-BASED ARCHITECTURE

### Base Component Class

Create `components/Component.php`:

```php
<?php
abstract class Component {
    protected $props = [];
    protected $classes = '';
    protected $attributes = [];
    
    public function __construct(array $props = []) {
        $this->props = $props;
        $this->classes = $props['class'] ?? '';
        $this->attributes = $props['attributes'] ?? [];
    }
    
    abstract public function render(): string;
    
    protected function buildAttributes(): string {
        $attrs = [];
        foreach ($this->attributes as $key => $value) {
            $attrs[] = htmlspecialchars($key) . '="' . htmlspecialchars($value) . '"';
        }
        return implode(' ', $attrs);
    }
    
    protected function escape($value): string {
        return htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
    }
    
    protected function prop($key, $default = null) {
        return $this->props[$key] ?? $default;
    }
}
```

### Example: DataTable Component

```php
<?php
require_once __DIR__ . '/../Component.php';

class DataTable extends Component {
    public function render(): string {
        $columns = $this->prop('columns', []);
        $data = $this->prop('data', []);
        $tableId = $this->prop('id', 'datatable-' . uniqid());
        
        $html = '<div class="table-responsive">';
        $html .= "<table id=\"{$tableId}\" class=\"table table-striped table-hover\">";
        
        // Header
        $html .= "<thead><tr>";
        foreach ($columns as $column) {
            $html .= "<th>" . $this->escape($column) . "</th>";
        }
        $html .= "</tr></thead>";
        
        // Body
        $html .= "<tbody>";
        foreach ($data as $row) {
            $html .= "<tr>";
            foreach ($row as $cell) {
                $html .= "<td>{$cell}</td>";
            }
            $html .= "</tr>";
        }
        $html .= "</tbody></table></div>";
        
        return $html;
    }
}
```

**Usage:**
```php
$table = new DataTable([
    'columns' => ['Date', 'Doctor', 'Status'],
    'data' => [
        ['2025-10-25', 'Dr. Smith', '<span class="badge bg-success">Accepted</span>'],
    ]
]);
echo $table->render();
```

---

## 🔐 SECURITY IMPLEMENTATION

### 1. Input Validation & Sanitization
```php
// Use prepared statements (PDO)
$stmt = $db->prepare("SELECT * FROM users WHERE email = ?");
$stmt->execute([$email]);
```

### 2. Password Hashing
```php
// Registration
$passwordHash = password_hash($password, PASSWORD_BCRYPT, ['cost' => 12]);

// Login verification
if (password_verify($inputPassword, $storedHash)) {
    // Success
}
```

### 3. CSRF Protection
```php
// Generate token
$_SESSION['csrf_token'] = bin2hex(random_bytes(32));

// Validate in forms
if ($_POST['csrf_token'] !== $_SESSION['csrf_token']) {
    die('CSRF validation failed');
}
```

### 4. XSS Prevention
```php
// Always escape output
echo htmlspecialchars($userInput, ENT_QUOTES, 'UTF-8');
```

---

## 📧 EMAIL NOTIFICATION SYSTEM

### PHPMailer Implementation

```php
// app/services/EmailService.php
use PHPMailer\PHPMailer\PHPMailer;

class EmailService {
    private $mailer;
    
    public function __construct() {
        $this->mailer = new PHPMailer(true);
        $this->configure();
    }
    
    private function configure() {
        $this->mailer->isSMTP();
        $this->mailer->Host = SMTP_HOST;
        $this->mailer->SMTPAuth = true;
        $this->mailer->Username = SMTP_USERNAME;
        $this->mailer->Password = SMTP_PASSWORD;
        $this->mailer->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $this->mailer->Port = 587;
    }
    
    public function sendAppointmentStatusEmail($appointmentId, $status) {
        // Get appointment details
        // Load email template
        // Send email
        // Log result
    }
}
```

**Email Templates Required:**
1. appointment-review.html → "Your appointment is under review"
2. appointment-accepted.html → "Appointment confirmed"
3. appointment-cancelled.html → "Appointment cancelled"
4. appointment-consulting.html → "Consultation in progress"
5. appointment-completed.html → "Appointment completed"
6. new-user-credentials.html → System-generated password

---

## 💳 PAYHERE INTEGRATION

### Configuration

```php
// config/payhere.php
define('PAYHERE_MERCHANT_ID', '1221149'); // Sandbox
define('PAYHERE_MERCHANT_SECRET', 'YOUR_SECRET');
define('PAYHERE_SANDBOX', true);
define('PAYHERE_CHECKOUT_URL', 'https://sandbox.payhere.lk/pay/checkout');
```

### Generate Payment Hash

```php
public function generateHash($orderId, $amount, $currency = 'LKR') {
    $hashedSecret = strtoupper(md5(PAYHERE_MERCHANT_SECRET));
    $amountFormatted = number_format($amount, 2, '.', '');
    
    return strtoupper(md5(
        PAYHERE_MERCHANT_ID . $orderId . $amountFormatted . $currency . $hashedSecret
    ));
}
```

### Payment Notification Webhook

```php
// public/payment/notify.php
$md5sig = $_POST['md5sig'];
$orderId = $_POST['order_id'];
$amount = $_POST['payhere_amount'];
$statusCode = $_POST['status_code'];

// Verify hash
if ($statusCode == 2) { // Success
    // Update payment record
    // Update appointment status
    // Send confirmation email
}
```

---

## 📊 FEATURES TO IMPLEMENT

### Module 1: User Management
- [x] User registration (Patient self-register)
- [x] User creation by Admin (Doctor/Staff)
- [x] System-generated passwords
- [x] Email credentials to new users
- [x] RBAC implementation
- [x] User profile management

### Module 2: Appointment Management
- [x] Smart booking form (doctor OR specialization search)
- [x] Real-time slot availability checking
- [x] Date picker (disable past dates)
- [x] Time slot grid (8AM-10PM, exclude 12-1PM)
- [x] Appointment status workflow (REVIEW → ACCEPTED → CONSULTING → COMPLETED)
- [x] Cancellation logic (>3 hours, unpaid only)
- [x] Email notifications for all status changes

### Module 3: Medical Record Management
- [x] Doctor adds diagnosis & prescription
- [x] Next visit date tracking
- [x] Patient medical history view
- [x] RBAC: Only assigned doctor can add records

### Module 4: Payment Management
- [x] Dual payment method (CARD/CASH)
- [x] PayHere integration for card payments
- [x] Manual cash payment marking by staff
- [x] Payment history
- [x] Receipt generation

### Module 5: Reports & Analytics
- [x] Dashboard statistics (appointments, revenue, patients)
- [x] Charts: Revenue (30 days), New patients, Appointment status
- [x] Export functionality (PDF/Excel)

### Module 6: Settings & Backup
- [x] System configuration
- [x] Database backup (manual/automatic)
- [x] Restore functionality

---

## 🎯 DEVELOPMENT WORKFLOW

### Phase 1: Setup (Week 1)
1. Install XAMPP/WAMP
2. Create database
3. Setup Composer
4. Install PHPMailer: `composer require phpmailer/phpmailer`
5. Configure .htaccess for URL rewriting
6. Create folder structure

### Phase 2: Core Framework (Week 1-2)
1. Build Router, Controller, Model base classes
2. Implement Component system
3. Create Database singleton class
4. Setup autoloading

### Phase 3: Components (Week 2-3)
1. Create all UI components (Card, Table, Alert, etc.)
2. Build form components
3. Develop layout components

### Phase 4: Module Development (Week 3-10)
Sprint-by-sprint implementation:
- Sprint 1: User Management
- Sprint 2: Appointment Booking
- Sprint 3: Email System
- Sprint 4: Medical Records
- Sprint 5: Payment System
- Sprint 6: Reports

### Phase 5: Testing (Week 11-12)
1. Unit testing
2. Integration testing
3. Security testing
4. User acceptance testing

---

## ⚠️ CRITICAL IMPLEMENTATION NOTES

### 1. Time Slot Booking - Prevent Double Booking

**Frontend (JavaScript):**
```javascript
function loadTimeSlots(doctorId, date) {
    $.ajax({
        url: 'api/get-available-slots.php',
        data: { doctor_id: doctorId, date: date },
        success: function(response) {
            let bookedSlots = response.booked_slots;
            
            allTimeSlots.forEach(function(slot) {
                let isBooked = bookedSlots.includes(slot);
                let isPast = checkIfPast(slot);
                
                if (isBooked || isPast) {
                    // Disable button, add lock icon
                } else {
                    // Enable button
                }
            });
        }
    });
}
```

**Backend (PHP):**
```php
// api/get-available-slots.php
$stmt = $db->prepare("
    SELECT time_slot 
    FROM appointments 
    WHERE doctor_id = ? AND appointment_date = ? 
    AND status NOT IN ('CANCELLED')
");
$stmt->execute([$doctorId, $date]);
$bookedSlots = $stmt->fetchAll(PDO::FETCH_COLUMN);

echo json_encode(['booked_slots' => $bookedSlots]);
```

**Database Lock (Critical!):**
```php
// When booking appointment
$stmt = $db->prepare("
    SELECT COUNT(*) as count 
    FROM appointments 
    WHERE doctor_id = ? AND appointment_date = ? AND time_slot = ? 
    AND status NOT IN ('CANCELLED')
    FOR UPDATE
");

if ($count > 0) {
    throw new Exception('Slot just booked by another patient');
}
```

### 2. Cancellation Button State

```javascript
function checkCancelEligibility(appointmentDateTime, paymentStatus) {
    let appointmentTime = new Date(appointmentDateTime);
    let now = new Date();
    let hoursDiff = (appointmentTime - now) / (1000 * 60 * 60);
    
    return hoursDiff > 3 && paymentStatus !== 'COMPLETED';
}
```

---

## 🚀 EXECUTION INSTRUCTIONS FOR CURSOR

### Step 1: Generate Core Structure
"Create the complete folder structure as specified, with all necessary folders and placeholder files."

### Step 2: Database Setup
"Generate the complete database schema with all 10 tables and proper foreign key relationships."

### Step 3: Component System
"Build the Component base class and create all UI components (Card, DataTable, Alert, Button, Select2, DatePicker)."

### Step 4: Authentication System
"Implement complete user authentication with registration, login, password hashing, and session management."

### Step 5: Appointment Booking
"Create the appointment booking system with:
- Doctor search (Select2 with AJAX)
- Date picker (disable past dates)
- Time slot grid with availability checking
- Double-booking prevention
- Email notifications"

### Step 6: Payment System
"Implement dual payment system:
- PayHere card payment integration
- Manual cash payment processing by staff
- Payment status tracking"

### Step 7: Medical Records
"Build medical record management with CRUD operations and RBAC."

### Step 8: Dashboards
"Create role-based dashboards for Admin, Staff, Doctor, and Patient with appropriate charts and data."

---

## ✅ TESTING CHECKLIST

- [ ] User registration and login work
- [ ] Password hashing implemented
- [ ] CSRF protection on all forms
- [ ] SQL injection prevention (PDO prepared statements)
- [ ] XSS prevention (output escaping)
- [ ] Time slots show correct availability
- [ ] Double booking prevented
- [ ] Email notifications sent for all status changes
- [ ] PayHere payment integration works
- [ ] Cash payment marking by staff works
- [ ] Cancellation rules enforced (>3 hours, unpaid)
- [ ] RBAC properly restricts access
- [ ] All components reusable
- [ ] Responsive design (mobile-friendly)
- [ ] Error logging enabled

---

## 📚 REFERENCES & DOCUMENTATION

- Bootstrap 5 Docs: https://getbootstrap.com/docs/5.3/
- PHPMailer GitHub: https://github.com/PHPMailer/PHPMailer
- PayHere API Docs: https://support.payhere.lk/api-&-mobile-sdk
- Select2 Docs: https://select2.org/
- Chart.js Docs: https://www.chartjs.org/

---

## 🎓 DEVELOPMENT BEST PRACTICES

1. **DRY Principle**: Don't Repeat Yourself - Use components
2. **Separation of Concerns**: Business logic in Services, data access in Repositories
3. **Security First**: Always validate input, escape output, use prepared statements
4. **Error Handling**: Try-catch blocks, log errors, user-friendly messages
5. **Code Comments**: Document complex logic
6. **Git Commits**: Frequent commits with clear messages
7. **Testing**: Test each feature thoroughly before moving to next

---

## 💡 ADDITIONAL RECOMMENDATIONS

1. Use `.env` file for sensitive configuration (database, email, PayHere credentials)
2. Implement rate limiting for login attempts
3. Add CAPTCHA for registration form
4. Setup automated database backups (cron job)
5. Use CDN for Bootstrap, jQuery (faster loading)
6. Implement logging for all database operations
7. Add pagination for large data tables
8. Create API endpoints for future mobile app integration

---

## 🏁 FINAL DELIVERABLES

1. Complete source code with component-based architecture
2. Database schema and seed data
3. User documentation (how to use the system)
4. Developer documentation (code structure, components)
5. Deployment guide
6. Test cases and results

---

**Now execute this prompt step-by-step in Cursor AI and build the complete system!**
