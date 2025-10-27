# He5 Framework Login Application - IEEE YESIST12

A clean, minimal implementation of the IEEE YESIST12 login system using the He5 Framework architecture.

## 🏗️ Architecture

This application follows the He5 Framework structure with clean separation of concerns:

```
he5-login-app/
├── config.php                 # Application configuration
├── He5Framework.php           # Minimal He5 Framework simulation
├── main.php                   # Application entry point
├── setup_database.php         # Database setup script
├── start_server.bat          # Windows batch startup script
├── start_server.ps1          # PowerShell startup script
├── public_html/              # Web root directory
│   ├── index.php            # Web entry point
│   └── .htaccess           # URL rewriting rules
├── src/
│   ├── Controllers/
│   │   └── AuthController.php    # Authentication controller
│   ├── Middleware/
│   │   └── AuthMiddleware.php     # Authentication middleware
│   └── Views/
│       ├── login.php             # Login page template
│       ├── signup.php            # Signup page template
│       └── dashboard.php         # Dashboard page template
└── logs/                     # Application logs (auto-created)
```

## 🚀 Features

- **Clean MVC Architecture**: Following He5 Framework patterns
- **Secure Authentication**: Password hashing with PHP's `password_hash()`
- **RESTful API**: JSON-based API endpoints
- **Middleware Protection**: Route-level access control
- **Comprehensive Logging**: Application and access logs
- **Responsive Design**: Clean, user-friendly interfaces
- **Session Management**: Secure session handling

## 📋 Prerequisites

- PHP 8.0 or higher
- MySQL 5.7 or higher
- Web server (Apache/Nginx) or PHP built-in server

## ⚡ Quick Start

1. **Navigate to the application directory:**
   ```bash
   cd "c:\Users\Uday Kiran\Desktop\ieee yesist 12\he5-login-app"
   ```

2. **Run the startup script:**
   
   **Windows (Batch):**
   ```bash
   start_server.bat
   ```
   
   **Windows (PowerShell):**
   ```powershell
   .\start_server.ps1
   ```

3. **Access the application:**
   - Login: http://localhost:8000/login
   - Signup: http://localhost:8000/signup
   - Dashboard: http://localhost:8000/dashboard

## 🔑 Test Credentials

The setup script creates a test user:
- **Email**: test@yesist12.com
- **Password**: password123

## 🛠️ Manual Setup

If you prefer manual setup:

1. **Setup Database:**
   ```bash
   php setup_database.php
   ```

2. **Start Server:**
   ```bash
   cd public_html
   php -S localhost:8000
   ```

## 📡 API Endpoints

| Method | Endpoint | Description | Authentication |
|--------|----------|-------------|----------------|
| POST | `/api/signup` | User registration | None |
| POST | `/api/signin` | User login | None |
| POST | `/api/logout` | User logout | Required |
| GET | `/api/profile` | Get user profile | Required |

### API Request/Response Examples

**Signup Request:**
```json
{
  "email": "user@example.com",
  "password": "password123",
  "fullName": "John Doe",
  "phone": "+1234567890"
}
```

**Signin Request:**
```json
{
  "email": "user@example.com",
  "password": "password123"
}
```

**Success Response:**
```json
{
  "success": true,
  "message": "Operation successful",
  "data": {...}
}
```

**Error Response:**
```json
{
  "success": false,
  "error": "Error message",
  "error_code": 400
}
```

## 🔒 Security Features

- **Password Hashing**: Using PHP's `PASSWORD_DEFAULT` algorithm
- **Input Validation**: Parameter validation with regex patterns
- **SQL Injection Protection**: Prepared statements
- **XSS Protection**: Output sanitization
- **CSRF Protection**: Built-in CSRF token handling
- **Session Security**: Secure session configuration

## 📊 Database Schema

### Users Table
```sql
CREATE TABLE users (
    id INT PRIMARY KEY AUTO_INCREMENT,
    email VARCHAR(255) UNIQUE NOT NULL,
    password_hash VARCHAR(255) NOT NULL,
    full_name VARCHAR(100) NOT NULL,
    phone VARCHAR(20),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);
```

### User Sessions Table
```sql
CREATE TABLE user_sessions (
    id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT NOT NULL,
    session_token VARCHAR(255) NOT NULL,
    expires_at TIMESTAMP NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);
```

## 📝 Configuration

Update `config.php` for your environment:

```php
// Database Configuration
define("DB_HOST", '127.0.0.1');
define("DB_NAME", 'yesist12_auth');
define("DB_USERNAME", 'root');
define("DB_PASSWORD", '');

// Application Configuration
define("SITE_PATH", 'http://localhost:8000');
define("PHP_TIMEZONE", 'Asia/Kolkata');
```

## 📋 Logging

Application logs are automatically created in the `logs/` directory:
- `logs/YYYY-MM-DD/application.log` - Application events and errors
- JSON format for easy parsing and analysis

## 🧪 Testing

The application includes built-in testing capabilities:
1. Database connection validation
2. User registration flow
3. Login authentication
4. Session management
5. API endpoint responses

## 🎯 He5 Framework Compliance

This implementation follows He5 Framework best practices:
- ✅ MVC Architecture
- ✅ Middleware System
- ✅ RESTful API Design
- ✅ Security Features
- ✅ Logging System
- ✅ Parameter Validation
- ✅ Exception Handling

## 📞 Support

For issues or questions regarding this He5 Framework implementation:
- Check the application logs in `logs/` directory
- Verify database connection and table structure
- Ensure PHP extensions are properly loaded
- Contact the internal development team for He5 Framework specific questions

## 📄 License

**HE5 INTERNAL & SERVICES USE**

© He5. All rights reserved.
This software is proprietary and confidential. Unauthorized copying, distribution, or modification is strictly prohibited.

Usage Terms:
- Only authorized He5 teams, employees, or partners may use this code
- Allowed for He5 internal applications and He5-created services/products
- Redistribution outside the permitted scope is forbidden
- Please contact He5 for permissions or licensing questions