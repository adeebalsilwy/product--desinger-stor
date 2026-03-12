# Technical Architecture Documentation

## System Overview

This document provides detailed technical architecture for the Customily-like product personalization platform.

---

## 1. High-Level Architecture

### Architecture Pattern: Modular Monolith

```
┌─────────────────────────────────────────────────────────────┐
│                      Presentation Layer                      │
│  ┌──────────────┐  ┌──────────────┐  ┌──────────────┐      │
│  │   Vue.js 3   │  │   Inertia    │  │  Tailwind    │      │
│  │   SPA        │  │   SSR        │  │   CSS        │      │
│  └──────────────┘  └──────────────┘  └──────────────┘      │
└─────────────────────────────────────────────────────────────┘
                              │
                              ▼
┌─────────────────────────────────────────────────────────────┐
│                    Application Layer                         │
│  ┌──────────────┐  ┌──────────────┐  ┌──────────────┐      │
│  │  Controllers │  │   Services   │  │    Jobs      │      │
│  │   (HTTP)     │  │  (Business)  │  │  (Queue)     │      │
│  └──────────────┘  └──────────────┘  └──────────────┘      │
│  ┌──────────────┐  ┌──────────────┐  ┌──────────────┐      │
│  │    Events    │  │  Listeners   │  │   Middleware │      │
│  └──────────────┘  └──────────────┘  └──────────────┘      │
└─────────────────────────────────────────────────────────────┘
                              │
                              ▼
┌─────────────────────────────────────────────────────────────┐
│                      Domain Layer                            │
│  ┌──────────────┐  ┌──────────────┐  ┌──────────────┐      │
│  │    Models    │  │   Repositories│  │    Rules     │      │
│  │  (Eloquent)  │  │  (Optional)  │  │ (Validation) │      │
│  └──────────────┘  └──────────────┘  └──────────────┘      │
└─────────────────────────────────────────────────────────────┘
                              │
                              ▼
┌─────────────────────────────────────────────────────────────┐
│                   Infrastructure Layer                       │
│  ┌──────────────┐  ┌──────────────┐  ┌──────────────┐      │
│  │   MySQL/     │  │    Redis     │  │  File Storage│      │
│  │  PostgreSQL  │  │   (Cache)    │  │  (S3/Local)  │      │
│  └──────────────┘  └──────────────┘  └──────────────┘      │
└─────────────────────────────────────────────────────────────┘
```

---

## 2. Core Modules

### 2.1 Product Management Module

**Purpose:** Manage product catalog, types, and variants

**Key Components:**
- `ProductType` - Categories of products (T-shirt, Mug, etc.)
- `Product` - Specific products
- `ProductVariant` - Size, color, material variations
- `PrintArea` - Configurable print regions

**Services:**
- `ProductService` - Business logic for products
- `InventoryService` - Stock management

### 2.2 Design Engine Module

**Purpose:** Canvas-based product customization

**Key Components:**
- `SavedDesign` - Customer design data
- `DesignTemplate` - Pre-made templates
- `DesignElement` - Individual design components

**Services:**
- `CanvasRenderer` - Render designs
- `ExportService` - Generate export files
- `TemplateService` - Template management

### 2.3 Asset Management Module

**Purpose:** Handle uploaded images, fonts, cliparts

**Key Components:**
- `UserAsset` - User uploaded files
- `Font` - Font library
- `Clipart` - Clipart collection

**Services:**
- `ImageService` - Image processing
- `StorageManager` - File storage abstraction

### 2.4 Order Management Module

**Purpose:** Process customer orders

**Key Components:**
- `Order` - Order header
- `OrderItem` - Line items with customizations
- `PaymentTransaction` - Payment records

**Services:**
- `OrderProcessor` - Order fulfillment
- `PaymentGateway` - Payment integration

---

## 3. Database Design

### Entity Relationship Diagram

```
product_types (1) ──< (M) products
     │
     ├──< (M) print_areas
     │
     ├──< (M) design_templates
     │
     └──< (M) saved_designs
               │
               └──< (M) order_items

products (1) ──< (M) product_variants
     │
     └──< (M) order_items

users (1) ──< (M) saved_designs
     │
     ├──< (M) user_assets
     │
     └──< (1) orders
               │
               └──< (M) order_items

clipart_categories (1) ──< (M) cliparts

fonts ── (reference only, no relationships)
```

### Key Design Decisions

1. **JSON Columns:** Used for flexible data structures
   - `design_data` in `saved_designs` - Complete canvas state
   - `customization_data` in `order_items` - Product options
   - `tags` in `cliparts` and `templates` - Searchable metadata

2. **Polymorphic Relationships:** Minimized in favor of explicit FKs for clarity

3. **Soft Deletes:** Not used to maintain data integrity; use hard deletes with cascade

---

## 4. API Design

### RESTful Principles

- Resource-based URLs
- HTTP verbs for actions
- JSON request/response format
- Stateless authentication
- Consistent error handling

### Endpoint Structure

```
GET    /api/v1/products          - List products
POST   /api/v1/products          - Create product
GET    /api/v1/products/{id}     - Get product
PUT    /api/v1/products/{id}     - Update product
DELETE /api/v1/products/{id}     - Delete product
```

### Response Format

**Success:**
```json
{
  "data": { ... },
  "meta": {
    "current_page": 1,
    "last_page": 10,
    "per_page": 20,
    "total": 200
  }
}
```

**Error:**
```json
{
  "error": "Error message",
  "errors": {
    "field": ["Validation error"]
  }
}
```

---

## 5. Frontend Architecture

### Component Hierarchy

```
App.vue
├── Layout (AppLayout.vue)
│   ├── Header
│   ├── Footer
│   └── Main Content
│       ├── ProductDesigner.vue
│       │   ├── Toolbar.vue
│       │   ├── Canvas.vue
│       │   ├── PropertiesPanel.vue
│       │   └── LayersPanel.vue
│       ├── ProductCatalog.vue
│       │   ├── ProductCard.vue
│       │   └── VariantSelector.vue
│       └── Cart.vue
│           ├── CartDrawer.vue
│           └── CheckoutForm.vue
```

### State Management

**Pattern:** Composition API + Pinia (optional)

```javascript
// stores/designerStore.js
import { defineStore } from 'pinia';

export const useDesignerStore = defineStore('designer', {
  state: () => ({
    canvas: null,
    selectedObject: null,
    isDirty: false,
    currentDesign: null,
  }),
  
  actions: {
    initializeCanvas() {
      // Setup Fabric.js canvas
    },
    
    addObject(object) {
      this.canvas.add(object);
      this.isDirty = true;
    },
    
    saveDesign() {
      const json = this.canvas.toJSON();
      // API call to save
    },
  },
});
```

---

## 6. Image Processing Pipeline

### Upload Flow

```
User Upload
    │
    ▼
File Validation (size, type)
    │
    ▼
Generate Unique Filename
    │
    ▼
Store Original (S3/Local)
    │
    ├──────────────┬──────────────┐
    ▼              ▼              ▼
Generate      Generate      Generate
Thumbnail     WebP         Metadata
(300px)       (optimized)  (dimensions)
    │              │              │
    └──────────────┴──────────────┘
                   │
                   ▼
          Update Database Record
                   │
                   ▼
          Return URLs to Client
```

### Export Flow (High-Resolution)

```
Design JSON
    │
    ▼
Parse Elements
    │
    ▼
Create High-Res Canvas (4x @ 300 DPI)
    │
    ├──────────────┬──────────────┐
    ▼              ▼              ▼
Render Text    Render Images  Render Shapes
    │              │              │
    └──────────────┴──────────────┘
                   │
                   ▼
            Apply Filters
            (if any)
                   │
                   ▼
            Export as PNG/PDF
                   │
                   ▼
            Store & Return URL
```

---

## 7. Queue System

### Job Types

1. **GenerateDesignPreview**
   - Triggered after design save
   - Generates web preview image
   - Low priority

2. **GeneratePrintFiles**
   - Triggered on order placement
   - Creates high-resolution exports
   - High priority

3. **ProcessOrderItems**
   - Validates inventory
   - Generates packing slips
   - Sends confirmation emails

4. **SendOrderConfirmation**
   - Email to customer
   - Notification to admin

### Queue Configuration

```php
// config/queue.php
'redis' => [
    'driver' => 'redis',
    'connection' => 'default',
    'queue' => env('REDIS_QUEUE', 'default'),
    'retry_after' => 90,
    'block_for' => null,
],
```

### Worker Setup

```bash
# Default queue
php artisan queue:work --queue=default --tries=3

# High priority jobs
php artisan queue:work --queue=high --max-jobs=100
```

---

## 8. Caching Strategy

### Cache Layers

1. **Application Cache** (Redis)
   - Product listings
   - Font lists
   - Clipart categories
   - Settings

2. **Browser Cache**
   - Static assets (JS, CSS)
   - Cached images (CDN)
   - API responses (where appropriate)

3. **Database Query Cache**
   - Frequently accessed data
   - Complex aggregations

### Implementation Examples

```php
// Cache product listing
$products = Cache::remember(
    'products.active.page.' . $page, 
    3600, 
    fn() => Product::active()->paginate(20)
);

// Cache fonts
$fonts = Cache::remember('fonts.list', 86400, fn() => Font::active()->get());

// Tag-based cache invalidation (with redis-tags)
Cache::tags(['products', 'product-' . $id])->put($key, $value);
```

---

## 9. Security Architecture

### Authentication & Authorization

**Backend:**
- Laravel Breeze for session auth
- Laravel Sanctum for API tokens
- Role-based access control (RBAC)

**Frontend:**
- CSRF token protection
- Secure HTTP-only cookies
- XSS prevention via Vue's auto-escaping

### Input Validation

```php
// Request validation
public function store(StoreProductRequest $request)
{
    $validated = $request->validated();
    // Only validated data proceeds
}
```

### File Upload Security

```php
// Validate file
$request->validate([
    'file' => 'required|file|max:10240|mimes:jpg,png,svg,webp',
]);

// Check MIME type
$allowedTypes = ['image/jpeg', 'image/png', 'image/svg+xml'];
if (!in_array($file->getMimeType(), $allowedTypes)) {
    throw new \Exception('Invalid file type');
}
```

### SQL Injection Prevention

- Use Eloquent ORM (parameterized queries)
- Avoid raw queries where possible
- Sanitize user input

### Rate Limiting

```php
// routes/api.php
Route::middleware('throttle:60,1')->group(function () {
    // 60 requests per minute
});
```

---

## 10. Performance Optimization

### Database Optimization

1. **Indexing Strategy**
   ```sql
   INDEX idx_product_type ON products(product_type_id, is_active)
   INDEX idx_user_designs ON saved_designs(user_id, created_at)
   INDEX idx_session ON saved_designs(session_id)
   ```

2. **Eager Loading**
   ```php
   // Load relationships to avoid N+1
   $designs = SavedDesign::with(['productType', 'user'])->get();
   ```

3. **Query Optimization**
   ```php
   // Select only needed columns
   $products = Product::select(['id', 'name', 'price', 'thumbnail_url'])
       ->active()
       ->get();
   ```

### Frontend Performance

1. **Code Splitting**
   ```javascript
   // Lazy load designer component
   const Designer = () => import('@/Components/Designer/ProductDesigner.vue');
   ```

2. **Image Lazy Loading**
   ```vue
   <img v-lazy="product.thumbnail_url" :alt="product.name">
   ```

3. **Virtual Scrolling**
   - For large clipart/font lists
   - Infinite scroll pagination

### Caching Headers

```nginx
# Nginx configuration
location ~* \.(jpg|jpeg|png|gif|ico|css|js)$ {
    expires 1y;
    add_header Cache-Control "public, immutable";
}
```

---

## 11. Scaling Strategy

### Horizontal Scaling

1. **Load Balancer** → Multiple App Servers
2. **Database Replication** → Read replicas
3. **Redis Cluster** → Session/cache distribution
4. **S3/CDN** → Static asset delivery

### Vertical Scaling

1. Increase PHP memory limit
2. Optimize database configuration
3. Use OPcache for PHP

### Microservices Readiness

Current architecture can be split into:
- **Product Service** - Catalog management
- **Design Service** - Canvas operations
- **Order Service** - Order processing
- **Asset Service** - File management
- **Notification Service** - Emails/SMS

---

## 12. Monitoring & Logging

### Application Monitoring

- Laravel Telescope (development)
- Sentry (production errors)
- Laravel Horizon (queue monitoring)

### Performance Monitoring

- New Relic or DataDog
- Chrome DevTools Lighthouse
- GTmetrix

### Logging Strategy

```php
// Different log channels
Log::channel('daily')->info('Design saved');
Log::channel('single')->error('Export failed');
Log::channel('slack')->critical('Payment failed');
```

---

## 13. Testing Strategy

### Test Pyramid

```
        /\
       /  \      E2E Tests (10%)
      /----\     (Playwright/Cypress)
     /      \   
    /--------\  Feature Tests (30%)
   /          \ (HTTP tests)
  /------------\ 
 /              \ Unit Tests (60%)
/________________\ (Models, Services)
```

### Example Tests

```php
// Unit Test
class ExportServiceTest extends TestCase
{
    public function test_generates_high_res_image()
    {
        $design = SavedDesign::factory()->create();
        $url = $this->service->exportHighRes($design);
        
        $this->assertNotNull($url);
        $this->assertFileExists($url);
    }
}

// Feature Test
class DesignApiTest extends TestCase
{
    public function test_can_create_design()
    {
        $response = $this->postJson('/api/designs', [
            'product_type_id' => 1,
            'design_data' => [...],
        ]);
        
        $response->assertStatus(201);
        $this->assertDatabaseHas('saved_designs', [
            'product_type_id' => 1,
        ]);
    }
}
```

---

## 14. Deployment Checklist

### Pre-Deployment

- [ ] Run all tests
- [ ] Code review completed
- [ ] Security audit passed
- [ ] Performance benchmarks met

### Deployment Steps

1. Pull latest code
2. Install dependencies (`composer install --optimize-autoloader`)
3. Clear caches (`php artisan optimize:clear`)
4. Run migrations (`php artisan migrate --force`)
5. Build assets (`npm run build`)
6. Restart queue workers
7. Verify deployment

### Post-Deployment

- [ ] Smoke tests passed
- [ ] Error monitoring active
- [ ] Backups running
- [ ] SSL certificate valid

---

## 15. Disaster Recovery

### Backup Strategy

1. **Database** - Daily automated backups
2. **Files** - S3 versioning enabled
3. **Code** - Git repository

### Recovery Procedures

1. **Database Loss:** Restore from latest backup
2. **File Loss:** Sync from S3
3. **Server Failure:** Deploy to new instance

### Business Continuity

- Multi-region deployment (for production)
- CDN fallback
- Maintenance mode ready

---

This architecture supports:
- ✅ High availability
- ✅ Scalability
- ✅ Maintainability
- ✅ Security
- ✅ Performance
