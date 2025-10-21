@echo off
cd /d "C:\Users\Uday Kiran\Desktop\ieee yesist 12\login-system"

echo ======================================
echo IEEE YESIST12 - Signup Fix Testing
echo ======================================
echo.
echo Current directory: %CD%
echo.
echo Starting PHP server from login-system folder...
echo.
echo Test pages will open automatically:
echo.
echo 1. Signup Fix Test: http://localhost:8000/fix_signup_test.html
echo 2. Original Login Page: http://localhost:8000/index.html
echo 3. Database Check: http://localhost:8000/check_existing_db.php
echo.
echo Instructions:
echo - Use the Signup Fix Test to diagnose the issue
echo - Try the complete signup test to see if data is stored
echo - Check the database records after testing
echo.
echo Press Ctrl+C to stop the server
echo.

timeout /t 3 /nobreak > nul
start http://localhost:8000/fix_signup_test.html

php -c "../php.ini" -S localhost:8000