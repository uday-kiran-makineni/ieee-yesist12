@echo off
echo Starting IEEE YESIST12 Authentication System...
echo.

:: Set the correct directory
cd /d "C:\Users\Uday Kiran\Desktop\ieee yesist 12"

:: Start PHP built-in server with custom php.ini
echo Starting PHP development server...
echo Using custom php.ini configuration...
echo.
echo Server will be available at: http://localhost:8000
echo Login page: http://localhost:8000/login-system/index.html
echo.
echo Test credentials:
echo Email: test@yesist12.com
echo Password: password123
echo.
echo Press Ctrl+C to stop the server
echo.

php -c "php.ini" -S localhost:8000

pause