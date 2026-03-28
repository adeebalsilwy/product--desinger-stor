# Designer Pages - Troubleshooting & Browser Cache Fix

## ⚠️ Issues You Might See

### 1. Vue Component Warnings
```
[Vue warn]: Failed to resolve component: CustomerLayout
[Vue warn]: Failed to resolve component: Customer
```

**Cause:** Browser is caching old version of the file

**Solution:** Hard refresh the browser
- **Windows/Linux:** `Ctrl + Shift + R` or `Ctrl + F5`
- **Mac:** `Cmd + Shift + R`

### 2. "updateCursor is not a function" Error
```
Uncaught (in promise) TypeError: this.updateCursor is not a function
```

**Cause:** Old cached version of the file doesn't have the method

**Solution:** Clear browser cache completely

### 3. Save Route 405 Error
```
POST http://127.0.0.1:8000/designer/save 405 (Method Not Allowed)
```

**Cause:** Route might not be defined or using wrong HTTP method

**Solution:** Check your Laravel routes

---

## ✅ Complete Fix Steps

### Step 1: Verify Files Are Correct

Check that Create.vue starts with:
```vue
<!-- Designer Page - Professional Canvas Editor v2.0 Fixed -->
<template>
  <Customer :showNav="false" :showFooter="false" :showCart="false" contentClass="designer-content">
```

**NOT:**
```vue
<CustomerLayout :showNav="false" ...>  <!-- WRONG -->
```

### Step 2: Clear Browser Cache

#### Chrome/Edge:
1. Press `F12` to open DevTools
2. Right-click the refresh button
3. Select **"Empty Cache and Hard Reload"**

#### Firefox:
1. Press `Ctrl + Shift + Delete`
2. Select "Cached Web Content"
3. Click "Clear Now"
4. Press `Ctrl + F5`

#### Safari:
1. Develop menu → Empty Caches
2. Press `Cmd + Option + E`

### Step 3: Clear Vite Cache

Sometimes Vite's dev server cache needs clearing:

```bash
# Stop the dev server (Ctrl+C)

# Delete vite cache
rm -rf node_modules/.vite

# Or on Windows PowerShell:
Remove-Item -Recurse -Force node_modules/.vite

# Restart dev server
npm run dev
```

### Step 4: Verify Methods Exist

Open browser console and check:

```javascript
// In browser console, after page loads:
console.log(typeof app._instance.proxy.updateCursor);
// Should output: "function"

console.log(typeof app._instance.proxy.activateTool);
// Should output: "function"
```

---

## 🔍 How to Verify Everything Works

### Console Should Show (No Errors):
✅ No "Failed to resolve component" warnings
✅ No "updateCursor is not a function" errors
✅ Drawing tools work smoothly
✅ Cursor changes when selecting tools

### Expected Console Output (Success):
```
CSRFService.js:13 CSRF token from meta tag: FOUND
CSRFService.js:37 Final CSRF token: FOUND
CSRFService.js:283 CSRF Protection initialized
```

### Test Drawing Tools:
1. **Select Brush** → Cursor should become crosshair ✛
2. **Draw on canvas** → Smooth lines appear
3. **Select Pen** → Cursor stays crosshair ✛
4. **Draw fine details** → Thin lines appear
5. **Select Eraser** → Cursor changes to cell ▦
6. **Erase strokes** → Lines disappear
7. **Select Text** → Cursor becomes move arrow ➔

---

## 🎯 Features That Should Work

### ✅ All Drawing Tools
- [x] Brush with smooth lines
- [x] Pen for fine details
- [x] Eraser that works properly
- [x] Color selection
- [x] Size adjustment (1-50px)

### ✅ Element Tools
- [x] Add text elements
- [x] Upload images
- [x] Add shapes (rectangle, circle, triangle)
- [x] Move elements
- [x] Resize elements
- [x] Rotate elements

### ✅ Control Features
- [x] Undo/Redo working
- [x] Duplicate elements
- [x] Delete elements
- [x] Clear drawing
- [x] Reset design
- [x] Download design
- [x] Save to database (if authenticated)

### ✅ Visual Features
- [x] Beautiful female mannequin
- [x] 3D wardrobe with templates
- [x] All palettes have close buttons (X)
- [x] Layers system working
- [x] Real-time mockup updates

---

## 🐛 If Problems Persist

### Problem: Still seeing CustomerLayout warnings

**Solution:** Find and delete old copies:

```bash
# Search for old files
find . -name "*Create*.vue" -o -name "*Edit*.vue"

# Delete backup copies
rm "resources/js/Pages/Customer/Designer/Create - Copy.vue"
rm "resources/js/Pages/Customer/Designer/Create copy.vue"
rm "resources/js/Pages/Customer/Designer/CreateCanvasMockup.vue"
# etc.
```

### Problem: Drawings not appearing

**Check:**
1. Canvas context initialized?
   ```javascript
   console.log(this.drawingContext);
   // Should be: CanvasRenderingContext2D
   ```

2. Global composite operation set?
   ```javascript
   console.log(this.drawingContext.globalCompositeOperation);
   // Should be: "source-over"
   ```

3. Line styles configured?
   ```javascript
   console.log(this.drawingContext.lineCap);
   // Should be: "round"
   ```

### Problem: Save still returns 405

**Check Laravel routes:**

```bash
php artisan route:list | grep designer
```

Should show:
```
POST|PUT   /designer/save   App\Http\Controllers\DesignerController@save
```

If route is missing, add to `routes/web.php`:

```php
Route::post('/designer/save', [DesignerController::class, 'save'])
    ->middleware(['auth']);
```

---

## 📝 Quick Reference

### File Locations:
- **Create.vue:** `resources/js/Pages/Customer/Designer/Create.vue`
- **Edit.vue:** `resources/js/Pages/Customer/Designer/Edit.vue`
- **Customer Layout:** `resources/js/Layouts/Customer.vue`

### Key Methods:
```javascript
mounted()           // Initialize canvas
updateCursor()      // Change cursor based on tool
activateTool()      // Select active tool
onStageMouseDown()  // Start drawing
onStageMouseMove()  // Continue drawing
onStageMouseUp()    // Stop drawing
saveDesign()        // Save/export design
```

### CSS Classes:
```css
.drawing-layer      // Canvas element
.tool-icon          // Tool buttons
.palette            // Floating panels
.mannequin-3d       // 3D mannequin
```

---

## 🎉 Success Indicators

You'll know everything is working when:

1. ✅ No console errors
2. ✅ Cursor changes with each tool
3. ✅ Drawing tools create visible strokes
4. ✅ Eraser removes strokes
5. ✅ Elements can be moved/resized/rotated
6. ✅ Save downloads PNG file
7. ✅ Mannequin displays beautifully
8. ✅ Wardrobe shows all templates
9. ✅ All palettes close with X button
10. ✅ Undo/Redo work perfectly

---

## 📞 Need More Help?

If issues persist after following all steps:

1. **Check file timestamps** - Ensure files were modified recently
2. **Restart dev server** - Stop and restart `npm run dev`
3. **Check file permissions** - Ensure files are writable
4. **Verify dependencies** - Run `npm install`
5. **Clear all caches** - Browser + Vite + Laravel

```bash
# Nuclear option - clear everything:
npm run dev --force
```

---

**Remember:** The most common issue is browser caching. Always try a hard refresh first!

**Version:** 2.0 Fixed
**Last Updated:** 2026-03-24
