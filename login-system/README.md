# YESIST12 Authentication System

A complete authentication system with Login, Register, and Forgot Password functionality, designed to match your IEEE YESIST12 branding.

## 🚀 Features

### ✅ Complete Authentication Flow
- **Sign In** - Email/Password login with validation
- **Sign Up** - User registration with email verification
- **Sign In with OTP** - One-time password login via email/SMS
- **Forgot Password** - Password reset via email link

### ✅ Modern UI/UX
- **Responsive Design** - Works on desktop, tablet, and mobile
- **Beautiful Animations** - Smooth transitions and effects
- **YESIST12 Branding** - Matches your IEEE theme colors
- **Tab-based Interface** - Easy switching between auth modes

### ✅ Security Features
- **Password Hashing** - Secure bcrypt encryption
- **OTP Verification** - Time-limited 6-digit codes
- **Session Management** - Secure user sessions
- **CSRF Protection** - Cross-site request forgery prevention
- **SQL Injection Prevention** - Prepared statements

### ✅ Database Features
- **MySQL Integration** - Complete database schema
- **Auto Cleanup** - Expired tokens and sessions
- **User Management** - Full user lifecycle
- **Audit Trail** - Login tracking and timestamps

## 📁 File Structure

```
login-system/
├── index.html              # Main authentication page
├── auth_handler.php         # Backend authentication logic
├── dashboard.php            # User dashboard after login
├── logout.php              # Logout functionality
├── setup_database.php      # Database setup script
├── config.php              # Configuration settings
└── README.md               # This file
```

## 🛠️ Setup Instructions

### Step 1: Database Setup

1. **Make sure MySQL is running**
   ```bash
   # Check MySQL service
   Get-Service -Name "*mysql*"
   ```

2. **Run the database setup**
   - Visit: `http://localhost:8000/login-system/setup_database.php`
   - This will create the database and tables automatically

### Step 2: Configuration

1. **Update database credentials** (if needed)
   - Edit `config.php`
   - Change `DB_HOST`, `DB_USER`, `DB_PASS` as needed

2. **Configure email settings** (for production)
   - Update SMTP settings in `config.php`
   - Add your email service credentials

### Step 3: Test the System

1. **Start PHP server**
   ```bash
   cd "C:\Users\Uday Kiran\Desktop\ieee yesist 12\login-system"
   php -S localhost:8000
   ```

2. **Visit the login page**
   - Open: `http://localhost:8000/index.html`

3. **Test with sample credentials**
   - **Email:** test@yesist12.com
   - **Password:** password123

## 🎯 How to Use

### For Users

1. **Sign Up**
   - Click "Sign Up" tab
   - Fill in all required fields
   - Create account (verification email sent)

2. **Sign In**
   - Use email and password
   - Or use the test credentials above

3. **Sign In with OTP**
   - Enter email or phone number
   - Receive 6-digit OTP
   - Enter OTP to login

4. **Forgot Password**
   - Click "Forgot Password?"
   - Enter email address
   - Check email for reset link

### For Developers

1. **Adding New Features**
   - Extend `auth_handler.php` for new endpoints
   - Update `dashboard.php` for new user features
   - Modify database schema if needed

2. **Customizing UI**
   - Edit `index.html` for authentication pages
   - Update CSS variables for different themes
   - Add new animations or effects

3. **Security Enhancements**
   - Enable SSL in production
   - Add rate limiting for login attempts
   - Implement 2FA for additional security

## 🗄️ Database Schema

### Tables Created

1. **users** - User accounts and profiles
2. **otp_codes** - One-time password storage
3. **password_resets** - Password reset tokens
4. **user_sessions** - Session management

### Sample Data

- Test user created automatically
- All tables have proper indexes
- Auto-cleanup procedures enabled

## 🔧 Configuration Options

### Security Settings
```php
PASSWORD_MIN_LENGTH = 8        # Minimum password length
OTP_EXPIRY_MINUTES = 10       # OTP expiration time
RESET_TOKEN_EXPIRY_HOURS = 1  # Reset token expiration
SESSION_TIMEOUT_MINUTES = 60  # Session timeout
```

### Email Settings
```php
SMTP_HOST = 'smtp.gmail.com'  # Email server
SMTP_PORT = 587               # Email port
SMTP_USERNAME = 'your-email'  # Your email
SMTP_PASSWORD = 'app-password' # App password
```

## 🚀 Production Deployment

### Before Going Live

1. **Update Configuration**
   - Set `DEBUG_MODE = false`
   - Update database credentials
   - Configure email/SMS services
   - Set proper domain URLs

2. **Security Checklist**
   - Enable SSL/HTTPS
   - Set secure session cookies
   - Configure firewall rules
   - Enable error logging

3. **Performance Optimization**
   - Enable database indexing
   - Configure caching
   - Optimize images and assets
   - Set up CDN if needed

## 🎨 Customization

### Changing Colors
```css
/* Update these CSS variables in index.html */
--primary-color: #00d4aa;
--secondary-color: #1e90ff;
--background-color: #2c3e50;
```

### Adding Social Login
1. Configure OAuth credentials in `config.php`
2. Implement social auth handlers
3. Add social login buttons to UI

### Email Templates
- Create HTML email templates
- Use email service provider (SendGrid, Mailgun)
- Add branding and styling

## 🐛 Troubleshooting

### Common Issues

1. **Database Connection Failed**
   - Check MySQL service is running
   - Verify credentials in `config.php`
   - Ensure database exists

2. **OTP Not Received**
   - Check email/SMS configuration
   - Verify contact information
   - Check spam folder

3. **Session Timeout**
   - Increase session timeout in config
   - Check server session settings
   - Clear browser cookies

### Debug Mode
- Enable `DEBUG_MODE = true` in config
- Check error logs in `logs/error.log`
- Use browser developer tools

## 📱 Mobile Responsiveness

The system is fully responsive and works on:
- ✅ Desktop (1920px+)
- ✅ Laptop (1366px+)
- ✅ Tablet (768px+)
- ✅ Mobile (320px+)

## 🔐 Security Features

- ✅ Password encryption (bcrypt)
- ✅ SQL injection prevention
- ✅ XSS protection
- ✅ CSRF tokens
- ✅ Session security
- ✅ Rate limiting ready
- ✅ Input validation
- ✅ Secure headers

## 📞 Support

For issues or questions:
1. Check this README
2. Review error logs
3. Test with sample credentials
4. Check database setup

## 🎉 Ready to Use!

Your YESIST12 authentication system is now ready! 

**Next Steps:**
1. Run `setup_database.php` to create the database
2. Test with the sample credentials
3. Customize colors and branding as needed
4. Configure email/SMS for production use

**Happy Coding! 🚀**