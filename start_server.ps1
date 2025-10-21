# IEEE YESIST12 Authentication System Startup Script
Write-Host "Starting IEEE YESIST12 Authentication System..." -ForegroundColor Green
Write-Host ""

# Set the correct directory
Set-Location "C:\Users\Uday Kiran\Desktop\ieee yesist 12"

# Display startup information
Write-Host "Starting PHP development server..." -ForegroundColor Yellow
Write-Host "Using custom php.ini configuration..." -ForegroundColor Yellow
Write-Host ""
Write-Host "Server will be available at: " -NoNewline
Write-Host "http://localhost:8000" -ForegroundColor Cyan
Write-Host "Login page: " -NoNewline  
Write-Host "http://localhost:8000/login-system/index.html" -ForegroundColor Cyan
Write-Host ""
Write-Host "Test credentials:" -ForegroundColor Magenta
Write-Host "Email: test@yesist12.com" -ForegroundColor White
Write-Host "Password: password123" -ForegroundColor White
Write-Host ""
Write-Host "Press Ctrl+C to stop the server" -ForegroundColor Red
Write-Host ""

# Start PHP server
try {
    php -c "php.ini" -S localhost:8000
}
catch {
    Write-Host "Error starting PHP server: $_" -ForegroundColor Red
    Write-Host "Make sure PHP is installed and in your PATH" -ForegroundColor Yellow
}

Write-Host "Press any key to exit..."
$null = $Host.UI.RawUI.ReadKey("NoEcho,IncludeKeyDown")