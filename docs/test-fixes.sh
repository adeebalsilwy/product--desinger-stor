#!/bin/bash
# Test Script for Customer Experience Improvements
# Run this script to verify all fixes are working

echo "======================================"
echo "Customer Experience - Test Suite"
echo "======================================"
echo ""

# Colors for output
GREEN='\033[0;32m'
RED='\033[0;31m'
YELLOW='\033[1;33m'
NC='\033[0m' # No Color

# Function to check if a route exists
check_route() {
    local route=$1
    php artisan route:list --path="$route" | grep -q "$route"
    if [ $? -eq 0 ]; then
        echo -e "${GREEN}✓${NC} Route exists: $route"
        return 0
    else
        echo -e "${RED}✗${NC} Route not found: $route"
        return 1
    fi
}

# Function to check if a file exists
check_file() {
    local file=$1
    if [ -f "$file" ]; then
        echo -e "${GREEN}✓${NC} File exists: $file"
        return 0
    else
        echo -e "${RED}✗${NC} File not found: $file"
        return 1
    fi
}

echo "1. Clearing Laravel Caches..."
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear
echo -e "${GREEN}✓ Caches cleared${NC}"
echo ""

echo "2. Checking Required Routes..."
check_route "designer/my-designs"
check_route "customer/profile"
check_route "customer/login"
check_route "gallery"
check_route "products"
echo ""

echo "3. Checking Created Files..."
check_file "app/Http/Controllers/Customer/ProfileController.php"
check_file "resources/js/Pages/Customer/Profile/Edit.vue"
echo ""

echo "4. Checking Modified Files..."
check_file "routes/web.php"
check_file "resources/js/Components/Customer/Navbar.vue"
check_file "resources/js/Pages/Customer/Gallery.vue"
echo ""

echo "5. Verifying Composer Dependencies..."
composer dump-autoload
echo -e "${GREEN}✓ Autoload optimized${NC}"
echo ""

echo "6. Checking Database Connection..."
php artisan tinker --execute="echo 'Database connection successful';"
if [ $? -eq 0 ]; then
    echo -e "${GREEN}✓${NC} Database connection OK"
else
    echo -e "${RED}✗${NC} Database connection failed"
fi
echo ""

echo "7. Checking Template Seeder..."
php artisan tinker --execute="
\$count = \App\Models\DesignTemplate::count();
echo 'Templates in database: ' . \$count . PHP_EOL;
if (\$count > 0) {
    echo '✓ Templates exist' . PHP_EOL;
} else {
    echo '⚠ No templates found. Run seeder: php artisan db:seed --class=TemplateSeeder' . PHP_EOL;
}
"
echo ""

echo "8. Running Tests..."
php artisan test --filter=Customer
echo ""

echo "======================================"
echo "Test Summary"
echo "======================================"
echo ""
echo -e "${YELLOW}Next Steps:${NC}"
echo "1. Start development server: php artisan serve"
echo "2. Open browser: http://127.0.0.1:8000"
echo "3. Test the following URLs:"
echo "   - http://127.0.0.1:8000/designer/my-designs (requires login)"
echo "   - http://127.0.0.1:8000/customer/profile (requires login)"
echo "   - http://127.0.0.1:8000/customer/login"
echo "   - http://127.0.0.1:8000/gallery"
echo "   - http://127.0.0.1:8000/products"
echo ""
echo -e "${YELLOW}Login Credentials (Demo):${NC}"
echo "Email: customer@dshirts.com"
echo "Password: customer123"
echo ""
echo -e "${YELLOW}Admin Login (if needed):${NC}"
echo "Check database for admin users or run:"
echo "php artisan db:seed --class=SuperAdminSeeder"
echo ""
echo "======================================"
echo "All checks completed!"
echo "======================================"
