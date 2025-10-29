@echo off
echo Starting He5 Login Application with MySQL Support...
echo.

REM Start PHP development server with php.ini
echo Starting PHP development server...
echo Server will be available at: http://localhost:8000
echo.
echo Pages available:
echo - Login: http://localhost:8000/login
echo - Signup: http://localhost:8000/signup  
echo - Dashboard: http://localhost:8000/dashboard (requires login)
echo.
echo API endpoints:
echo - POST /api/signup - User registration
echo - POST /api/signin - User login
echo - POST /api/logout - User logout
echo - GET /api/profile - Get user profile
echo - GET /api/validate-token - Token validation
echo.
echo Features:
echo - Token-based authentication with encrypted user IDs
echo - Secure HMAC-SHA256 token signatures
echo - 24-hour token expiration
echo - Full database integration with PDO MySQL
echo.
echo Press Ctrl+C to stop the server
echo.

php -c php.ini -S localhost:8000 main.php