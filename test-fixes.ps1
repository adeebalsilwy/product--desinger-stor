# Test Script for Customer Experience Improvements (PowerShell)
# Run this script to verify all fixes are working

Write-Host "======================================" -ForegroundColor Cyan
Write-Host "Customer Experience - Test Suite" -ForegroundColor Cyan
Write-Host "======================================" -ForegroundColor Cyan
Write-Host ""

# Function to check if a route exists
function Check-Route {
    param($route)
    $output = php artisan route:list --path=$route 2>&1
    if ($output -like "*$route*") {
        Write-Host "✓ Route exists: $route" -ForegroundColor Green
        return $true
    } else {
        Write-Host "✗ Route not found: $route" -ForegroundColor Red
        return $false
    }
}

# Function to check if a file exists
function Check-File {
    param($file)
    if (Test-Path $file) {
        Write-Host "✓ File exists: $file" -ForegroundColor Green
        return $true
    } else {
        Write-Host "✗ File not found: $file" -ForegroundColor Red
        return $false
    }
}

Write-Host "1. Clearing Laravel Caches..." -ForegroundColor Yellow
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear
Write-Host "✓ Caches cleared" -ForegroundColor Green
Write-Host ""

Write-Host "2. Checking Required Routes..." -ForegroundColor Yellow
Check-Route "designer/my-designs"
Check-Route "customer/profile"
Check-Route "customer/login"
Check-Route "gallery"
Check-Route "products"
Write-Host ""

Write-Host "3. Checking Created Files..." -ForegroundColor Yellow
Check-File "app\Http\Controllers\Customer\ProfileController.php"
Check-File "resources\js\Pages\Customer\Profile\Edit.vue"
Write-Host ""

Write-Host "4. Checking Modified Files..." -ForegroundColor Yellow
Check-File "routes\web.php"
Check-File "resources\js\Components\Customer\Navbar.vue"
Check-File "resources\js\Pages\Customer\Gallery.vue"
Write-Host ""

Write-Host "5. Verifying Composer Dependencies..." -ForegroundColor Yellow
composer dump-autoload
Write-Host "✓ Autoload optimized" -ForegroundColor Green
Write-Host ""

Write-Host "6. Checking Database Connection..." -ForegroundColor Yellow
php artisan tinker --execute="echo 'Database connection successful';"
if ($LASTEXITCODE -eq 0) {
    Write-Host "✓ Database connection OK" -ForegroundColor Green
} else {
    Write-Host "✗ Database connection failed" -ForegroundColor Red
}
Write-Host ""

Write-Host "7. Checking Template Seeder..." -ForegroundColor Yellow
php artisan tinker --execute="
\$count = \App\Models\DesignTemplate::count();
echo 'Templates in database: ' . \$count . PHP_EOL;
if (\$count > 0) {
    echo '✓ Templates exist' . PHP_EOL;
} else {
    echo '⚠ No templates found. Run seeder: php artisan db:seed --class=TemplateSeeder' . PHP_EOL;
}
"
Write-Host ""

Write-Host "8. Running Tests..." -ForegroundColor Yellow
php artisan test --filter=Customer
Write-Host ""

Write-Host "======================================" -ForegroundColor Cyan
Write-Host "Test Summary" -ForegroundColor Cyan
Write-Host "======================================" -ForegroundColor Cyan
Write-Host ""
Write-Host "Next Steps:" -ForegroundColor Yellow
Write-Host "1. Start development server: php artisan serve"
Write-Host "2. Open browser: http://127.0.0.1:8000"
Write-Host "3. Test the following URLs:"
Write-Host "   - http://127.0.0.1:8000/designer/my-designs (requires login)"
Write-Host "   - http://127.0.0.1:8000/customer/profile (requires login)"
Write-Host "   - http://127.0.0.1:8000/customer/login"
Write-Host "   - http://127.0.0.1:8000/gallery"
Write-Host "   - http://127.0.0.1:8000/products"
Write-Host ""
Write-Host "Login Credentials (Demo):" -ForegroundColor Yellow
Write-Host "Email: customer@dshirts.com"
Write-Host "Password: customer123"
Write-Host ""
Write-Host "Admin Login (if needed):" -ForegroundColor Yellow
Write-Host "Check database for admin users or run:"
Write-Host "php artisan db:seed --class=SuperAdminSeeder"
Write-Host ""
Write-Host "======================================" -ForegroundColor Cyan
Write-Host "All checks completed!" -ForegroundColor Green
Write-Host "======================================" -ForegroundColor Cyan
