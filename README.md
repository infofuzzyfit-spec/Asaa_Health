# ASAA Healthcare Management System

A comprehensive healthcare appointment management system built with PHP, MySQL, and modern web technologies.

## Features

- **User Management**: Multi-role system (Admin, Staff, Doctor, Patient)
- **Appointment Booking**: Smart booking with real-time slot availability
- **Payment Processing**: Dual payment system (PayHere + Cash)
- **Medical Records**: Complete patient medical history management
- **Email Notifications**: Automated email system for all status changes
- **Reports & Analytics**: Comprehensive reporting and dashboard system
- **Security**: CSRF protection, input validation, and secure authentication

## Technology Stack

- **Backend**: PHP 8.2+, MySQL 8.0+
- **Frontend**: HTML5, CSS3, Bootstrap 5.3, JavaScript (ES6+)
- **Libraries**: jQuery, Select2, FullCalendar.js, Chart.js
- **Payment**: PayHere Payment Gateway
- **Email**: PHPMailer
- **Architecture**: Component-Based MVC

## Installation

### Prerequisites

- PHP 8.2 or higher
- MySQL 8.0 or higher
- Apache/Nginx web server
- Composer (for dependency management)

### Setup Instructions

1. **Clone the repository**
   ```bash
   git clone <repository-url>
   cd Asaa_Health_Care
   ```

2. **Install dependencies**
   ```bash
   composer install
   ```

3. **Database Setup**
   - Create a MySQL database named `asaa_healthcare`
   - Import the database schema:
     ```bash
     mysql -u root -p asaa_healthcare < database/schema.sql
     ```
   - Import sample data:
     ```bash
     mysql -u root -p asaa_healthcare < database/seeds.sql
     ```

4. **Configuration**
   - Copy `env.example` to `.env`
   - Update database credentials in `.env`
   - Configure email settings in `config/email.php`
   - Configure PayHere settings in `config/payhere.php`

5. **Web Server Configuration**
   - Point your web server document root to the `public` directory
   - Ensure URL rewriting is enabled (mod_rewrite for Apache)

6. **Permissions**
   ```bash
   chmod 755 public/uploads
   chmod 755 logs
   ```

## Default Login Credentials

- **Admin**: admin@asaahealthcare.com / password
- **Staff**: staff@asaahealthcare.com / password
- **Doctor**: sarah.johnson@asaahealthcare.com / password
- **Patient**: alice.smith@email.com / password

## Project Structure

```
Asaa_Health_Care/
├── app/                    # Application logic
│   ├── controllers/        # Controllers
│   ├── models/            # Data models
│   ├── services/          # Business logic
│   ├── repositories/      # Data access layer
│   └── middleware/        # Middleware
├── components/            # Reusable UI components
├── config/               # Configuration files
├── core/                 # Framework core
├── database/             # Database files
├── public/               # Web root
│   ├── assets/           # CSS, JS, images
│   └── index.php         # Front controller
├── views/                # View templates
└── logs/                 # Log files
```

## Key Features

### 1. Appointment Booking System
- Real-time slot availability checking
- Doctor search with Select2
- Date picker with past date restrictions
- Double-booking prevention with database locks
- Email notifications for all status changes

### 2. Payment System
- PayHere integration for card payments
- Manual cash payment processing
- Payment status tracking
- Receipt generation

### 3. Medical Records
- Complete patient medical history
- Doctor notes and prescriptions
- Next visit date tracking
- Role-based access control

### 4. User Management
- Multi-role system (Admin, Staff, Doctor, Patient)
- Self-registration for patients
- Admin-created accounts for staff/doctors
- System-generated passwords with email delivery

### 5. Security Features
- CSRF protection on all forms
- SQL injection prevention (PDO prepared statements)
- XSS prevention (output escaping)
- Password hashing with bcrypt
- Session management

## API Endpoints

- `GET /api/available-slots` - Get available time slots for a doctor/date
- `GET /api/doctors/search` - Search doctors by name or specialization
- `POST /payment/notify` - PayHere webhook for payment notifications

## Configuration

### Database Configuration
Update `config/database.php` with your database credentials.

### Email Configuration
Update `config/email.php` with your SMTP settings.

### PayHere Configuration
Update `config/payhere.php` with your PayHere merchant credentials.

## Development

### Running Tests
```bash
composer test
```

### Code Style
The project follows PSR-12 coding standards.

### Contributing
1. Fork the repository
2. Create a feature branch
3. Make your changes
4. Add tests if applicable
5. Submit a pull request

## License

This project is licensed under the MIT License.

## Support

For support and questions, please contact:
- Email: support@asaahealthcare.com
- Phone: +94 11 234 5678

## Changelog

### Version 1.0.0
- Initial release
- Complete appointment management system
- Payment integration
- Medical records management
- User management system
- Email notifications
- Reports and analytics
