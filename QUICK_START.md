# 🚀 Quick Start Guide - Customily Platform

Get your personalization platform running in 15 minutes!

---

## ⚡ Super Quick Setup (15 Minutes)

### Step 1: Install Dependencies (3 min)

```bash
cd /path/to/project

# Install PHP dependencies
composer install

# Install NPM dependencies
npm install
```

### Step 2: Environment Setup (2 min)

```bash
# Copy environment file
cp .env.example .env

# Generate app key
php artisan key:generate

# Set database in .env
DB_CONNECTION=mysql
DB_DATABASE=customily
DB_USERNAME=root
DB_PASSWORD=secret
```

### Step 3: Database & Storage (3 min)

```bash
# Create database
mysql -u root -p -e "CREATE DATABASE customily CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"

# Run migrations
php artisan migrate --seed

# Create storage link
php artisan storage:link
```

### Step 4: Build Assets (2 min)

```bash
# Development build
npm run dev

# Or production build
npm run build
```

### Step 5: Start Server (1 min)

```bash
# Terminal 1: Laravel server
php artisan serve

# Terminal 2: Vite dev server (if using npm run dev)
npm run dev
```

**🎉 Visit:** http://localhost:8000

---

## 📦 What's Included Out of the Box

### ✅ Ready to Use

- User authentication (register/login)
- Product catalog system
- Design canvas (Fabric.js)
- Image upload
- Font library (10 fonts)
- Order management
- Admin panel
- RESTful API

### ⚙️ Needs Configuration

- Payment gateway (Stripe keys)
- Email service (SMTP settings)
- S3 storage (AWS credentials)
- Redis (for cache/queue)

---

## 🎨 First Design Flow

### As a Customer:

1. **Visit:** http://localhost:8000/t-shirt/classic-tee
2. **Click:** "Customize" button
3. **Designer opens** with blank canvas
4. **Add text:** Click "Add Text" → Type something
5. **Change font:** Select from dropdown
6. **Change color:** Use color picker
7. **Upload image:** Click "Upload Image" → Choose file
8. **Position elements:** Drag to move, handles to resize
9. **Save design:** Click "💾 Save" button
10. **Export:** Click "📤 Export" for high-res file

### As an Admin:

1. **Login** as admin user
2. **Visit:** http://localhost:8000/admin/t-shirts
3. **Add product:** Click "Add T-Shirt"
4. **Fill form:** Name, price, description
5. **Upload images:** Product photos
6. **Set variants:** Sizes (S, M, L, XL), Colors
7. **Configure print areas:** Front, Back dimensions
8. **Save:** Product created!

---

## 🔧 Common Tasks

### Add New Product Type

```bash
php artisan tinker
```

```php
\App\Models\ProductType::create([
    'name' => 'Hoodie',
    'slug' => 'hoodie',
    'description' => 'Comfortable hooded sweatshirt',
    'base_price' => 39.99,
    'is_active' => true,
]);
```

### Add Fonts

```bash
php artisan db:seed --class=FontSeeder
```

### Clear Caches

```bash
php artisan optimize:clear
```

### Run Queue Worker

```bash
php artisan queue:work --daemon
```

### Create Admin User

```bash
php artisan tinker
```

```php
\App\Models\User::create([
    'name' => 'Admin',
    'email' => 'admin@example.com',
    'password' => bcrypt('password'),
    'role' => 'admin',
]);
```

---

## 🐛 Troubleshooting

### Issue: Canvas not loading

```bash
# Check Fabric.js installed
npm list fabric

# Reinstall if needed
npm install fabric
npm run build
```

### Issue: Images not uploading

```bash
# Check storage permissions
chmod -R 775 storage/
chown -R www-data:www-data storage/

# Create symlink
php artisan storage:link
```

### Issue: Migration errors

```bash
# Fresh database
php artisan migrate:fresh --seed

# Or rollback
php artisan migrate:rollback
```

### Issue: API returns 401

```bash
# Check Sanctum installation
composer require laravel/sanctum

# Publish config
php artisan vendor:publish --provider="Laravel\Sanctum\SanctumServiceProvider"
```

---

## 📚 File Locations

### Models
```
app/Models/
├── Product.php
├── ProductType.php
├── SavedDesign.php
└── ...
```

### Controllers
```
app/Http/Controllers/
├── Api/
│   ├── DesignController.php
│   └── ...
└── Admin/
    └── ...
```

### Components
```
resources/js/Components/
├── Designer/
│   └── ProductDesigner.vue
└── ...
```

### Migrations
```
database/migrations/
└── 2024_10_31_200000_create_customily_tables.php
```

---

## 🎯 Test Scenarios

### Scenario 1: Create & Save Design

```javascript
// Open browser console
const canvas = new fabric.Canvas('design-canvas');

// Add text
const text = new fabric.IText('Hello World', {
    left: 100,
    top: 100,
    fontSize: 40
});
canvas.add(text);

// Export
const json = canvas.toJSON();
console.log(json);
```

### Scenario 2: Upload Asset

```bash
curl -X POST http://localhost:8000/api/assets/upload \
  -H "Content-Type: multipart/form-data" \
  -F "file=@/path/to/image.jpg"
```

### Scenario 3: Create Order via API

```bash
curl -X POST http://localhost:8000/api/orders \
  -H "Content-Type: application/json" \
  -d '{
    "items": [
      {
        "product_id": 1,
        "quantity": 2,
        "saved_design_id": 1
      }
    ],
    "shipping_address": {...}
  }'
```

---

## 🔐 Default Credentials

After seeding:

**No default admin created yet.** Create one manually:

```bash
php artisan tinker
```

```php
$user = \App\Models\User::create([
    'name' => 'Admin User',
    'email' => 'admin@example.com',
    'password' => bcrypt('YourPassword123'),
    'role' => 'admin',
]);
```

**Login:** admin@example.com  
**Password:** YourPassword123

---

## 📊 Database Info

### Tables Created

After migration + seeders:

- `users` - User accounts
- `product_types` - 5 types (T-shirt, Mug, Phone Case, Poster, Tote Bag)
- `products` - 10 products (2 per type)
- `print_areas` - 6 print areas
- `fonts` - 10 fonts
- `saved_designs` - Empty (ready for designs)
- `orders` - Empty
- And 15+ more tables...

---

## 🎨 Customization Tips

### Change Canvas Size

Edit `config/designer.php`:

```php
'canvas' => [
    'default_width' => 1000,  // Change here
    'default_height' => 1000,
],
```

### Add More Fonts

Edit `database/seeders/FontSeeder.php`:

```php
Font::create([
    'name' => 'Your Font',
    'font_file_url' => '/fonts/your-font.ttf',
    'category' => 'serif',
]);
```

### Change Allowed Uploads

Edit validation in `AssetController.php`:

```php
'max' => 20480, // 20MB instead of 10MB
```

---

## 🚀 Deploy to Production

### Using Laravel Sail (Docker)

```bash
# Start containers
./vendor/bin/sail up -d

# Run migrations
./vendor/bin/sail artisan migrate:fresh --seed

# Build assets
./vendor/bin/sail npm run build

# Access
http://localhost
```

### Traditional Hosting

1. Upload code
2. Run `composer install --optimize-autoloader`
3. Configure `.env`
4. Run migrations
5. Build assets (`npm run build`)
6. Point domain to `public/` folder
7. Setup SSL (Let's Encrypt)

---

## 📞 Need Help?

### Documentation Files

- `CUSTOMILY_README.md` - Full README
- `TECHNICAL_ARCHITECTURE.md` - Architecture details
- `IMPLEMENTATION_CHECKLIST.md` - Development roadmap
- `RECOMMENDED_LIBRARIES.md` - Library recommendations
- `PROJECT_SUMMARY.md` - Complete summary

### Useful Commands

```bash
# List all routes
php artisan route:list

# Interactive shell
php artisan tinker

# Test email
php artisan mail:to test@example.com

# Monitor queues
php artisan horizon

# Check cache
php artisan optimize:status
```

---

## ✅ Success Checklist

Before going live:

- [ ] Database migrated
- [ ] Sample data seeded
- [ ] Storage linked
- [ ] Admin user created
- [ ] At least 1 product added
- [ ] Test order placed
- [ ] Payment gateway configured
- [ ] Email sending works
- [ ] SSL certificate installed
- [ ] Backups configured

---

## 🎉 You're Ready!

Your platform is now ready to:

✅ Accept user registrations  
✅ Create custom designs  
✅ Process orders  
✅ Handle payments  
✅ Generate print files  
✅ Manage inventory  

**Next steps:**
1. Add your products
2. Customize branding
3. Configure payment gateway
4. Launch marketing
5. Start selling! 💰

---

**Happy Selling!** 🛍️✨

*Estimated setup time: 15 minutes*  
*Time to revenue: Depends on you!* 🚀
