# Navigate to login-system directory
Set-Location "C:\Users\Uday Kiran\Desktop\ieee yesist 12\login-system"

Write-Host "========================================" -ForegroundColor Green
Write-Host "IEEE YESIST12 - Signup Fix Testing" -ForegroundColor Green  
Write-Host "========================================" -ForegroundColor Green
Write-Host ""
Write-Host "Current directory: " -NoNewline
Write-Host (Get-Location) -ForegroundColor Yellow
Write-Host ""
Write-Host "Starting PHP server from login-system folder..." -ForegroundColor Yellow
Write-Host ""
Write-Host "Test pages will open automatically:" -ForegroundColor Cyan
Write-Host ""
Write-Host "1. Signup Fix Test: " -NoNewline
Write-Host "http://localhost:8000/fix_signup_test.html" -ForegroundColor Cyan
Write-Host "2. Original Login Page: " -NoNewline  
Write-Host "http://localhost:8000/index.html" -ForegroundColor Cyan
Write-Host "3. Database Check: " -NoNewline
Write-Host "http://localhost:8000/check_existing_db.php" -ForegroundColor Cyan
Write-Host ""
Write-Host "Instructions:" -ForegroundColor Magenta
Write-Host "- Use the Signup Fix Test to diagnose the issue" -ForegroundColor White
Write-Host "- Try the complete signup test to see if data is stored" -ForegroundColor White
Write-Host "- Check the database records after testing" -ForegroundColor White
Write-Host ""
Write-Host "Press Ctrl+C to stop the server" -ForegroundColor Red
Write-Host ""

Start-Sleep -Seconds 2
Start-Process "http://localhost:8000/fix_signup_test.html"

try {
    php -c "../php.ini" -S localhost:8000
}
catch {
    Write-Host "Error starting PHP server: $_" -ForegroundColor Red
    Write-Host "Make sure PHP is installed and in your PATH" -ForegroundColor Yellow
}

Write-Host "Press any key to exit..."
$null = $Host.UI.RawUI.ReadKey("NoEcho,IncludeKeyDown")