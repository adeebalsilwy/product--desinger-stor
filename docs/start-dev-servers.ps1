# PowerShell startup script for D-Shirts application

Write-Host "Starting D-Shirts Development Servers..." -ForegroundColor Green
Write-Host ""

# Start Laravel server in the background
Write-Host "Starting Laravel server on http://127.0.0.1:8000" -ForegroundColor Yellow
Start-Process -FilePath "php" -ArgumentList "artisan", "serve", "--host=127.0.0.1", "--port=8000" -RedirectStandardOutput "laravel.log" -RedirectStandardError "laravel.log"

# Wait a moment for Laravel to start
Start-Sleep -Seconds 3

# Start Vite dev server in the background
Write-Host "Starting Vite dev server" -ForegroundColor Yellow
Start-Process -FilePath "npx" -ArgumentList "vite", "--host" -RedirectStandardOutput "vite.log" -RedirectStandardError "vite.log"

Write-Host ""
Write-Host "Servers started successfully!" -ForegroundColor Green
Write-Host "Laravel: http://127.0.0.1:8000" -ForegroundColor Cyan
Write-Host "Vite: Check vite.log for network address" -ForegroundColor Cyan
Write-Host ""
Write-Host "Logs are being written to laravel.log and vite.log" -ForegroundColor White
Write-Host ""
Write-Host "To stop servers, close this window or press Ctrl+C" -ForegroundColor Magenta
Write-Host ""

# Wait for user input to stop
Write-Host "Press any key to stop servers..."
$null = $Host.UI.RawUI.ReadKey("NoEcho,IncludeKeyDown")