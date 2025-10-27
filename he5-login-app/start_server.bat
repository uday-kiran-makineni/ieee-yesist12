@echo off
echo Starting He5 Login Application Setup...
echo.

REM Setup database
echo Setting up database...
php setup_database.php
echo.

REM Start PHP development server
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
echo.
echo Press Ctrl+C to stop the server
echo.

cd public_html
php -S localhost:8000