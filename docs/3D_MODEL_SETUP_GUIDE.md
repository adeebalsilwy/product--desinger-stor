# Professional 3D Model Setup Guide

## Overview
This guide explains how to add professional 3D girl/mannequin models to display your template-based products realistically.

---

## Required Image Assets

### 1. Main Hero 3D Model
**File Path:** `public/images/3d-girl-model-professional.png`

**Specifications:**
- **Size:** 800x1200px (or larger, maintaining aspect ratio)
- **Format:** PNG with transparent background
- **Style:** Professional, realistic 3D rendered female mannequin/model
- **Pose:** Standing, neutral pose (arms slightly away from body for easy design overlay)
- **Quality:** High-resolution, photorealistic

**Where to Get:**
- **Free Options:**
  - [Sketchfab](https://sketchfab.com/) - Search for "female mannequin" or "fashion model"
  - [TurboSquid](https://www.turbosquid.com/) - Free section
  - [Mixamo](https://www.mixamo.com/) - Adobe's character library
  
- **Premium Options:**
  - [CGTrader](https://www.cgtrader.com/)
  - [3D Export](https://3dexport.com/)
  - Custom 3D rendering services

**Alternative (Quick Start):**
Use a professional photography mannequin photo with background removed:
- Take photo of white mannequin on green/white background
- Remove background using Photoshop or online tools like remove.bg
- Save as PNG with transparency

---

### 2. Mannequin Mockup for Products
**File Path:** `public/images/mannequin-with-design-{id}.png`

**Dynamic Generation:**
Instead of creating individual images, you can generate these programmatically:

#### Option A: Using Backend (Recommended)
Create a route that generates mannequin mockups on-the-fly:

```php
// routes/web.php
Route::get('/images/mannequin-with-design/{productId}', function ($productId) {
    $product = \App\Models\Product::with('designTemplate')->findOrFail($productId);
    
    // Load base mannequin image
    $mannequin = imagecreatefrompng(public_path('images/mannequin-base.png'));
    
    // If product has template design, overlay it
    if ($product->designTemplate && $product->designTemplate.thumbnail_url) {
        $design = imagecreatefrompng(storage_path('app/public/' . $product->designTemplate.thumbnail_url));
        
        // Resize and position design on mannequin
        // ... image manipulation code
        
        imagecopy(
            $mannequin, 
            $design, 
            100, // x position on chest area
            150, // y position
            0, 0, // source x, y
            200, 250 // width, height of design
        );
    }
    
    header('Content-Type: image/png');
    imagepng($mannequin);
    imagedestroy($mannequin);
})->name('image.mannequin');
```

#### Option B: Using Frontend Canvas
Overlay designs on mannequin using CSS/Fabric.js in the browser.

---

### 3. Base Mannequin Template
**File Path:** `public/images/mannequin-base.png`

**Specifications:**
- Clean, white or light gray mannequin
- Transparent background
- High resolution (minimum 1000x1500px)
- Neutral lighting
- Front view preferred

---

## Implementation Steps

### Step 1: Download/Acquire 3D Model

#### Quick Solution (Free):
1. Visit [Mixamo](https://www.mixamo.com/)
2. Create free Adobe account
3. Search for "Female Basic" or similar character
4. Download as PNG sequence or render

#### Professional Solution:
1. Purchase a premium 3D model from CGTrader or TurboSquid
2. Hire a 3D artist on Fiverr/Upwork to render custom poses
3. Use Blender (free) to create your own custom model

### Step 2: Prepare Images

#### Using Photoshop/GIMP:
1. Open your 3D model image
2. Remove background (if not already transparent)
3. Adjust brightness/contrast for consistency
4. Export as PNG with transparency
5. Optimize file size (target: <500KB)

#### Using Online Tools:
1. **Background Removal:** [remove.bg](https://www.remove.bg/)
2. **Image Optimization:** [tinypng.com](https://tinypng.com/)
3. **Resize:** [bulkresize.com](https://bulkresize.com/)

### Step 3: Place Images in Public Directory

```
public/
└── images/
    ├── 3d-girl-model-professional.png    # Main hero model
    ├── mannequin-base.png                 # Base template
    ├── mannequin-mockup.png               # Fallback mockup
    └── placeholder-product.jpg            # Existing placeholder
```

### Step 4: Update Product Images Table

Ensure your products have proper images linked:

```sql
-- Example: Update product with template to use mannequin display
UPDATE products 
SET thumbnail_url = 'images/mannequin-with-design-TEMPLATE.png'
WHERE is_template_based = true;
```

Or better, generate dynamically via route as shown above.

---

## Alternative: Use CSS-Only Mannequin

If you can't get 3D models immediately, here's a CSS-only solution:

### Update Products.vue Hero Section:

```html
<!-- CSS-Only Abstract Model -->
<div class="css-mannequin-container">
    <div class="mannequin-head"></div>
    <div class="mannequin-torso">
        <div class="torso-front"></div>
        <div class="torso-back"></div>
    </div>
    <div class="mannequin-arms">
        <div class="arm left-arm"></div>
        <div class="arm right-arm"></div>
    </div>
</div>
```

### Add CSS Styling:

```css
.css-mannequin-container {
    width: 300px;
    height: 500px;
    position: relative;
    margin: 0 auto;
}

.mannequin-head {
    width: 80px;
    height: 100px;
    background: linear-gradient(135deg, #f5d0b5 0%, #e0b090 100%);
    border-radius: 40px;
    position: absolute;
    top: 0;
    left: 50%;
    transform: translateX(-50%);
    box-shadow: 0 10px 30px rgba(0,0,0,0.2);
}

.mannequin-torso {
    width: 200px;
    height: 280px;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    border-radius: 60px 60px 40px 40px;
    position: absolute;
    top: 110px;
    left: 50%;
    transform: translateX(-50%);
    box-shadow: 0 20px 50px rgba(102, 126, 234, 0.3);
}
```

---

## Dynamic Design Preview Integration

### For Real-Time Design Display:

If you want to show actual customer designs on the mannequin:

#### Backend Route (Laravel):

```php
use Intervention\Image\Facades\Image;

Route::get('/api/product/{slug}/preview', function ($slug) {
    $product = Product::with('designTemplate')->where('slug', $slug)->firstOrFail();
    
    // Create canvas
    $canvas = Image::canvas(800, 1000);
    
    // Insert mannequin base
    $mannequin = Image::make(public_path('images/mannequin-base.png'));
    $canvas->insert($mannequin, 'center');
    
    // If has template, overlay design
    if ($product->designTemplate) {
        $designPath = storage_path('app/public/' . $product->designTemplate.preview_url);
        $design = Image::make($designPath);
        
        // Resize and position on chest area
        $design->resize(200, 250);
        $canvas->insert($design, function ($position) {
            $position->top(200)->left(300);
        });
    }
    
    return $canvas->response('png');
});
```

#### Frontend Usage:

```javascript
// In ProductCard.vue or Products.vue
const getPreviewImage = (product) => {
    if (product.is_template_based) {
        return route('api.product.preview', { slug: product.slug });
    }
    return getFirstImage();
};
```

---

## Testing Your Setup

### Test Checklist:

1. ✅ Main hero displays 3D model correctly
2. ✅ Model has transparent background
3. ✅ Animations work (floating, glow effects)
4. ✅ Product cards show templates clearly
5. ✅ Responsive on mobile/tablet
6. ✅ Night mode compatible

### Browser Test:

Visit: `http://127.0.0.1:8000/products`

Check:
- Hero section shows 3D model with animations
- Products grid displays template badges
- Hover effects work smoothly
- No broken image icons

---

## Troubleshooting

### Issue: Model image not loading
**Solution:** Check file path and permissions
```bash
ls -la public/images/3d-girl-model-professional.png
chmod 644 public/images/*.png
```

### Issue: Image too large/slow
**Solution:** Optimize image
```bash
# Using ImageMagick
convert 3d-girl-model-professional.png -resize 800x1200 -quality 85 optimized.png
```

### Issue: Design not aligning on mannequin
**Solution:** Adjust positioning in backend route or frontend CSS

---

## Recommended Resources

### 3D Models:
- [Sketchfab - Fashion Mannequins](https://sketchfab.com/search?q=fashion+mannequin&type=models)
- [TurboSquid - Female Mannequins](https://www.turbosquid.com/Search/3D-Models/free-female-mannequin)
- [Free3D - Mannequins](https://free3d.com/3d-models/mannequin)

### Image Tools:
- **Background Removal:** remove.bg, photoshop.com/remove-background
- **Optimization:** tinypng.com, compressjpeg.com
- **Editing:** photopea.com (free online Photoshop alternative)

### Stock Photos:
- [Unsplash - Mannequin](https://unsplash.com/s/photos/mannequin)
- [Pexels - Fashion Model](https://www.pexels.com/search/fashion%20model/)

---

## Next Steps

1. **Immediate:** Use CSS-only solution or placeholder mannequin
2. **Short-term:** Download free 3D model from Mixamo/Sketchfab
3. **Long-term:** Commission custom 3D renders or generate dynamically

---

## Support

For questions or assistance:
- Check Laravel logs: `storage/logs/laravel.log`
- Verify image paths in browser DevTools
- Test API endpoints if using dynamic generation

---

**Remember:** The key is to start simple and iterate. Even a basic mannequin image is better than no visual representation at all!
