# Customily Clone - Laravel Product Personalization Platform

A complete open-source product personalization platform built with Laravel, similar to Customily. Allow customers to design and customize products in real-time before purchasing.

![Laravel](https://img.shields.io/badge/Laravel-11.x-FF2D20?style=flat&logo=laravel)
![PHP](https://img.shields.io/badge/PHP-8.2+-777BB4?style=flat&logo=php)
![Vue.js](https://img.shields.io/badge/Vue.js-3.x-4FC08D?style=flat&logo=vue.js)
![License](https://img.shields.io/badge/license-MIT-blue.svg)

## 🚀 Features

### Customer Features
- ✅ Real-time product designer (drag & drop)
- ✅ Add text with custom fonts
- ✅ Upload images
- ✅ Change colors and fonts
- ✅ Resize and rotate objects
- ✅ Live preview
- ✅ Save designs
- ✅ Order customized products
- ✅ Shopping cart & checkout

### Admin Features
- ✅ Manage products & product types
- ✅ Manage templates
- ✅ Manage fonts & cliparts
- ✅ Configure print areas
- ✅ Order management
- ✅ Export print-ready files
- ✅ Analytics dashboard

### Designer Engine
- ✅ Canvas editor powered by Fabric.js
- ✅ Layer system
- ✅ Text editor with font selection
- ✅ Color picker
- ✅ Image upload
- ✅ SVG support
- ✅ High-resolution export
- ✅ PDF generation

### Advanced Features
- ✅ Multi-store capability
- ✅ Multi-language support
- ✅ Multi-currency
- ✅ Theme system
- ✅ Plugin architecture
- ✅ S3 storage integration
- ✅ Queue-based image processing

---

## 📋 Requirements

- PHP 8.2 or higher
- MySQL 8.0+ or PostgreSQL 13+
- Composer
- Node.js 18+ & NPM
- Redis (recommended for caching & queues)
- GD or Imagick PHP extension
- Laravel Sail (for Docker deployment)

---

## 🛠️ Installation

### 1. Clone the Repository

```bash
git clone https://github.com/yourusername/customily-laravel.git
cd customily-laravel
```

### 2. Install Dependencies

```bash
composer install
npm install
```

### 3. Environment Setup

```bash
cp .env.example .env
php artisan key:generate
```

Update your `.env` file:

```env
APP_NAME="CustomShop"
APP_URL=http://localhost:8000

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=customily
DB_USERNAME=root
DB_PASSWORD=secret

# File Storage
FILESYSTEM_DISK=public
AWS_ACCESS_KEY_ID=
AWS_SECRET_ACCESS_KEY=
AWS_DEFAULT_REGION=us-east-1
AWS_BUCKET=your-bucket-name
AWS_URL=https://your-bucket-name.s3.amazonaws.com

# Queue Driver (redis recommended)
QUEUE_CONNECTION=redis

# Cache Driver
CACHE_DRIVER=redis

# Stripe (optional)
STRIPE_KEY=
STRIPE_SECRET=
```

### 4. Run Migrations & Seeders

```bash
php artisan migrate --seed
```

This will create all necessary tables and seed:
- Product types (Ahlam's Girls Products, Mugs, Phone Cases, etc.)
- Sample fonts
- Clipart categories
- Sample products

### 5. Build Assets

```bash
npm run build
```

For development with hot reload:

```bash
npm run dev
```

### 6. Create Storage Symlink

```bash
php artisan storage:link
```

### 7. Start Development Server

```bash
php artisan serve
```

Visit: `http://localhost:8000`

---

## 🏗️ Architecture

### Tech Stack

**Backend:**
- Laravel 11.x
- Laravel Breeze (Authentication)
- Laravel Sanctum (API Authentication)
- Inertia.js (Server-side SPA)
- Intervention Image (Image Processing)

**Frontend:**
- Vue.js 3
- Inertia.js
- Tailwind CSS
- Fabric.js (Canvas Editor)
- Axios

**Database:**
- MySQL / PostgreSQL

**Storage:**
- Local + Amazon S3 Compatible

---

## 📁 Project Structure

```
customily-laravel/
├── app/
│   ├── Models/              # Database models
│   │   ├── Product.php
│   │   ├── ProductType.php
│   │   ├── PrintArea.php
│   │   ├── SavedDesign.php
│   │   └── ...
│   ├── Services/            # Business logic services
│   │   ├── Image/
│   │   │   ├── ImageService.php
│   │   │   └── ImageProcessor.php
│   │   ├── Design/
│   │   │   ├── ExportService.php
│   │   │   └── CanvasRenderer.php
│   │   └── ...
│   ├── Http/Controllers/
│   │   ├── Api/            # RESTful API controllers
│   │   ├── Admin/          # Admin panel controllers
│   │   └── Designer/       # Designer canvas controllers
│   └── Jobs/               # Queue jobs
│       ├── GenerateDesignPreview.php
│       └── ...
├── resources/
│   ├── js/
│   │   ├── Components/
│   │   │   ├── Designer/   # Canvas editor components
│   │   │   ├── Products/
│   │   │   └── ...
│   │   └── Pages/
│   ├── css/
│   └── views/
├── database/
│   ├── migrations/
│   ├── seeders/
│   └── factories/
├── routes/
│   ├── api.php            # API routes
│   ├── web.php            # Web routes
│   └── admin.php          # Admin routes
└── storage/
    └── app/
        ├── public/        # Public files
        ├── private/       # Private files
        └── temp/          # Temporary files
```

---

## 🔧 Configuration

### Designer Settings (`config/designer.php`)

```php
return [
    'canvas' => [
        'default_width' => 800,
        'default_height' => 800,
        'max_width' => 2000,
    ],
    'allowed_formats' => ['jpg', 'png', 'svg', 'webp'],
    'max_upload_size' => 10, // MB
    'print' => [
        'dpi' => 300,
        'color_mode' => 'RGB',
    ],
];
```

---

## 🎨 Using the Designer

### Frontend Integration

```vue
<template>
  <ProductDesigner 
    :product-type-id="1"
    @saved="handleDesignSaved"
  />
</template>

<script>
import ProductDesigner from '@/Components/Designer/ProductDesigner.vue';

export default {
  components: { ProductDesigner },
  
  methods: {
    handleDesignSaved(design) {
      console.log('Design saved:', design);
      // Add to cart or continue shopping
    }
  }
}
</script>
```

### API Endpoints

#### Create Design
```bash
POST /api/designs
Content-Type: application/json
Authorization: Bearer {token}

{
  "product_type_id": 1,
  "name": "My Custom Design",
  "design_data": {...} // Fabric.js JSON
}
```

#### Export Design
```bash
POST /api/designs/{id}/export
{
  "format": "high_res" // or "preview", "pdf", "layers"
}
```

#### Upload Asset
```bash
POST /api/assets/upload
Content-Type: multipart/form-data

file: (image file)
```

---

## 🖼️ Image Generation Workflow

1. **User creates design** on canvas (Fabric.js)
2. **Design JSON saved** to database
3. **Queue job triggered** to generate previews
4. **Low-res preview** generated for web display
5. **High-res export** generated when order placed
6. **Print-ready files** sent to production

---

## 📦 Database Schema

### Core Tables

- `product_types` - Ahlam's Girls Products, mugs, phone cases, etc.
- `products` - Specific product instances
- `print_areas` - Configurable print regions
- `saved_designs` - Customer designs
- `design_templates` - Pre-made templates
- `order_items` - Order line items with designs
- `user_assets` - Uploaded images
- `fonts` - Font library
- `cliparts` - Clipart library

See full schema in: `database/migrations/2024_10_31_200000_create_customily_tables.php`

---

## 🚀 Deployment

### Docker Deployment (Recommended)

```bash
# Using Laravel Sail
./vendor/bin/sail up -d

# Run migrations
./vendor/bin/sail artisan migrate:fresh --seed

# Build assets
./vendor/bin/sail npm run build
```

### Production Server

1. **Clone & Setup**
```bash
git clone https://github.com/yourusername/customily-laravel.git
cd customily-laravel
composer install --optimize-autoloader --no-dev
```

2. **Configure Environment**
```bash
cp .env.example .env
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

3. **Run Migrations**
```bash
php artisan migrate --force
```

4. **Setup Queue Worker**
```bash
php artisan queue:work --daemon
```

5. **Setup Supervisor** (for queue workers)

`/etc/supervisor/conf.d/customily-worker.conf`:
```ini
[program:customily-worker]
process_name=%(program_name)s_%(process_num)02d
command=php /path/to/customily/artisan queue:work database --sleep=3 --tries=3 --max-time=3600
autostart=true
autorestart=true
stopasgroup=true
killasgroup=true
user=www-data
numprocs=2
redirect_stderr=true
stdout_logfile=/path/to/customily/storage/logs/worker.log
stopwaitsecs=3600
```

---

## 🔐 Security

### Best Practices Implemented

- ✅ CSRF Protection
- ✅ SQL Injection Prevention (Eloquent ORM)
- ✅ XSS Protection
- ✅ Input Validation
- ✅ Sanitization
- ✅ Secure Authentication (Laravel Breeze)
- ✅ API Token Authentication (Sanctum)
- ✅ Rate Limiting
- ✅ Encrypted Sessions

### File Upload Security

```php
// Validated in AssetController
$allowedMimeTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp', 'image/svg+xml'];
$maxSize = 10 * 1024 * 1024; // 10MB
```

---

## ⚡ Performance Optimization

### Caching Strategy

```php
// Cache product listings
Cache::remember('products.active', 3600, function () {
    return Product::active()->get();
});

// Cache fonts & cliparts
Cache::remember('fonts.list', 86400, function () {
    return Font::active()->get();
});
```

### Image Optimization

- Use WebP format for web
- Lazy loading images
- CDN for static assets
- Responsive images

### Database Optimization

- Indexed columns
- Eager loading relationships
- Query optimization
- Database replication (for scale)

---

## 🧪 Testing

```bash
# Run tests
php artisan test

# Run specific test suite
php artisan test --filter=DesignTest

# With coverage
php artisan test --coverage
```

---

## 🤝 Contributing

Contributions are welcome! Please read our contributing guidelines first.

1. Fork the repository
2. Create your feature branch (`git checkout -b feature/AmazingFeature`)
3. Commit your changes (`git commit -m 'Add some AmazingFeature'`)
4. Push to the branch (`git push origin feature/AmazingFeature`)
5. Open a Pull Request

---

## 📄 License

This project is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).

---

## 🙏 Credits

- Built with [Laravel](https://laravel.com)
- Canvas editor powered by [Fabric.js](http://fabricjs.com/)
- Icons from various open-source libraries

---

## 📞 Support

For support, email support@customily.com or join our Discord community.

---

## 🎯 Roadmap

- [ ] QR Code generator
- [ ] Barcode support
- [ ] AI-powered design suggestions
- [ ] Mobile apps (iOS/Android)
- [ ] Shopify integration
- [ ] Etsy marketplace sync
- [ ] Advanced analytics
- [ ] A/B testing for designs

---

## 💼 Commercial Use

This platform is designed for commercial use. You can:
- ✅ Deploy for your own business
- ✅ Offer as SaaS to customers
- ✅ Customize for clients
- ✅ Sell personalized products

Attribution appreciated but not required.

---

Made with ❤️ using Laravel & Vue.js
