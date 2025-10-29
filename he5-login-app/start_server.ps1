Write-Host "🚀 Starting He5 Login Application Setup..." -ForegroundColor Green
Write-Host ""

# Start PHP development server
Write-Host "🌐 Starting PHP development server with MySQL support..." -ForegroundColor Yellow
Write-Host "Server will be available at: http://localhost:8000" -ForegroundColor Cyan
Write-Host ""
Write-Host "📄 Pages available:" -ForegroundColor White
Write-Host "- Login: http://localhost:8000/login" -ForegroundColor Gray
Write-Host "- Signup: http://localhost:8000/signup" -ForegroundColor Gray
Write-Host "- Dashboard: http://localhost:8000/dashboard (requires login)" -ForegroundColor Gray
Write-Host ""
Write-Host "📡 API endpoints:" -ForegroundColor White
Write-Host "- POST /api/signup - User registration" -ForegroundColor Gray
Write-Host "- POST /api/signin - User login" -ForegroundColor Gray
Write-Host "- POST /api/logout - User logout" -ForegroundColor Gray
Write-Host "- GET /api/profile - Get user profile" -ForegroundColor Gray
Write-Host "- GET /api/validate-token - Token validation" -ForegroundColor Gray
Write-Host ""
Write-Host "🔑 Test Credentials:" -ForegroundColor Cyan
Write-Host "Email: test@yesist12.com" -ForegroundColor Gray
Write-Host "Password: password123" -ForegroundColor Gray
Write-Host ""
Write-Host "🔐 Features:" -ForegroundColor Green
Write-Host "- Token-based authentication with encrypted user IDs" -ForegroundColor Gray
Write-Host "- Secure HMAC-SHA256 token signatures" -ForegroundColor Gray
Write-Host "- 24-hour token expiration" -ForegroundColor Gray
Write-Host "- Full database integration with PDO MySQL" -ForegroundColor Gray
Write-Host ""
Write-Host "Press Ctrl+C to stop the server" -ForegroundColor Red
Write-Host ""

php -c php.ini -S localhost:8000 main.php