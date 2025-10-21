@echo off
echo ================================
echo IEEE YESIST12 Network Debug Tool
echo ================================
echo.
echo Server is starting...
echo.
echo Opening debugging pages in your browser:
echo.
echo 1. Signup Debug Test: http://localhost:8000/login-system/signup_debug.html
echo 2. Network Debug Tool: http://localhost:8000/login-system/network_debug.html
echo 3. Main Login Page: http://localhost:8000/login-system/index.html
echo 4. System Check: http://localhost:8000/login-system/system_check.php
echo.
echo Instructions:
echo - Use Signup Debug Test first to isolate the issue
echo - Check browser developer console (F12) for additional errors
echo - Look for error messages in the debug output
echo.
echo Press Ctrl+C to stop the server
echo.

timeout /t 2 /nobreak > nul
start http://localhost:8000/login-system/signup_debug.html
cd /d "C:\Users\Uday Kiran\Desktop\ieee yesist 12"
php -c "php.ini" -S localhost:8000