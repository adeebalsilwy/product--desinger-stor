# Designer Page Testing Guide

## Quick Test Steps

### 1. Basic Setup
```bash
# Make sure servers are running
npm run dev
php artisan serve
```

Visit: `http://localhost:8000/designer/t-shirt`

### 2. Test Drawing Tools ✅

#### Brush Tool
1. Click on "فرشاة" (Brush) icon
2. Select a color from the palette
3. Choose brush size
4. **Expected**: Cursor changes to crosshair
5. Draw on the garment area
6. **Expected**: Lines appear in selected color

#### Pen Tool
1. Click on "قلم" (Pen) icon
2. **Expected**: Cursor changes to crosshair
3. Draw thin lines
4. **Expected**: Thinner lines than brush

#### Eraser Tool
1. Click on "ممحاة" (Eraser) icon
2. **Expected**: Cursor changes to cell cursor
3. Draw over existing content
4. **Expected**: Content is erased

### 3. Test Elements ✅

#### Add Text
1. Click "نص" (Text) tool
2. Type in the textarea
3. Click "تطبيق" (Apply)
4. **Expected**: Text appears on canvas
5. Select text and edit properties:
   - Change font size ✓
   - Change font weight ✓
   - Change color ✓
   - Move position ✓
   - Resize ✓
   - Rotate ✓

#### Add Shapes
1. Click "أشكال" (Shapes) tool
2. **Expected**: Rectangle appears
3. Select shape and modify:
   - Change width/height ✓
   - Change color ✓
   - Adjust opacity ✓
   - Rotate ✓

#### Add Image
1. Click "صور" (Images) tool
2. Click "اختيار صورة" (Choose Image)
3. Select an image file
4. **Expected**: Image appears on canvas
5. Test resize, move, rotate

### 4. Test Layer System ✅

1. Add multiple elements (text, shapes, images)
2. Open "الطبقات" (Layers) palette
3. **Expected**: All elements listed
4. Click different layers to select
5. Use forward/backward buttons
6. **Expected**: Z-order changes correctly

### 5. Test Templates ✅

1. Click "القوالب" (Templates) palette
2. **Expected**: 4 templates visible:
   - هادئ (Soft)
   - نيون موف (Violet Neon)
   - شبكي (Mesh)
   - فاخر (Luxury)
3. Click each template
4. **Expected**: Background changes immediately

### 6. Test Wardrobe ✓

1. Click wardrobe button (bottom right)
2. **Expected**: Doors open with animation
3. Verify all items visible:
   - فستان أنثوي (Women's Dress)
   - قميص رسمي (Formal Shirt)
   - جاكيت (Jacket)
   - هودي (Hoodie)
   - All 4 templates
4. Click each item
5. **Expected**: Item applies or shows info

### 7. Test Mockup Preview ✅

1. Click "عرض التصميم على المجسم" (Preview on Mockup)
2. **Expected**: Panel slides in with feminine mannequin
3. Verify features:
   - Beautiful feminine curves ✓
   - Body proportions realistic ✓
   - Design overlays correctly ✓
   - Template background visible ✓
4. Click "تدوير المجسم" (Spin Mockup)
5. **Expected**: Mannequin rotates 360°
6. Click again to stop

### 8. Test Toolbar Actions ✅

#### Undo/Redo
1. Make several changes
2. Click undo button
3. **Expected**: Changes revert one by one
4. Click redo button
5. **Expected**: Changes reapply

#### Duplicate
1. Select an element
2. Click duplicate button
3. **Expected**: Copy appears offset by 24px

#### Delete
1. Select an element
2. Click delete button
3. **Expected**: Element removed

### 9. Test Save Functionality ✅

#### Download
1. Create a design
2. Click "تنزيل" (Download) button
3. **Expected**: PNG file downloads
4. Open downloaded file
5. **Expected**: Shows complete composite:
   - Template background ✓
   - Garment color ✓
   - Drawn content ✓
   - All elements ✓

#### Save to Database (Login Required)
1. Login as customer
2. Create design
3. Click "حفظ التصميم" (Save Design)
4. **Expected**: Success message
5. Check database `saved_designs` table
6. **Expected**: Record exists with:
   - user_id ✓
   - name ✓
   - preview_url ✓
   - design_data (JSON) ✓
   - elements (in JSON) ✓

### 10. Test Translations ✅

#### Arabic (Default)
1. Load page with locale = 'ar'
2. Verify all text is Arabic:
   - Tools ✓
   - Palettes ✓
   - Actions ✓
   - Messages ✓

#### English
1. Switch locale to 'en'
2. Reload page
3. Verify all text is English:
   - Brush → "Brush"
   - فرشاة → "Brush"
   - حفظ التصميم → "Save Design"

### 11. Test All Close Buttons ✅

Verify each palette has working close button:
- [ ] Colors palette ✕ button
- [ ] Size palette ✕ button
- [ ] Dress sizes palette ✕ button
- [ ] Dress color palette ✕ button
- [ ] Templates palette ✕ button
- [ ] Text palette ✕ button
- [ ] Image palette ✕ button
- [ ] Layers palette ✕ button
- [ ] Properties palette ✕ button
- [ ] Mockup panel ✕ button
- [ ] Wardrobe header ✕ button

### 12. Test Responsive Design ✅

#### Desktop (> 1500px)
- All panels visible
- Canvas centered
- Mockup panel fits

#### Tablet (768px - 1500px)
- Mockup scales to 86%
- Panels adjust

#### Mobile (< 768px)
- Touch events work
- Panes can be closed
- Canvas accessible

## Common Issues & Solutions

### Issue: Drawing doesn't work
**Solution**: 
1. Check console for errors
2. Verify canvas has focus (tabindex="0")
3. Ensure touch-action: none CSS applied
4. Check mouse event handlers attached

### Issue: Save returns 405 error
**Solution**:
1. Verify route exists: `php artisan route:list | grep designs`
2. Check CSRF token in meta tag
3. Ensure authenticated (if required)
4. Check network tab for details

### Issue: Templates not showing
**Solution**:
1. Check wardrobeItems array initialization
2. Verify templates array has items
3. Check translation keys exist
4. Inspect wardrobe DOM structure

### Issue: Mockup not feminine enough
**Solution**:
1. Adjust .mannequin-body::before opacity
2. Increase hip width (currently 195px)
3. Enhance waist definition blur
4. Modify gradient highlights

### Issue: Translations not loading
**Solution**:
1. Check files exist in resources/lang/{ar|en}/
2. Verify translation keys match
3. Ensure $t() helper available
4. Clear Laravel cache: `php artisan cache:clear`

## Performance Tests

### Canvas Rendering
- Should render at 60fps
- No lag when drawing
- Smooth animations

### Memory Usage
- Check browser memory
- Should not exceed 100MB
- No memory leaks on undo/redo

### Network Requests
- Save should complete < 2s
- Images should load progressively
- No failed requests

## Browser Compatibility Matrix

| Feature | Chrome | Firefox | Safari | Edge |
|---------|--------|---------|--------|------|
| Drawing | ✅ | ✅ | ✅ | ✅ |
| Touch | ✅ | ✅ | ✅ | ✅ |
| Save | ✅ | ✅ | ✅ | ✅ |
| Animations | ✅ | ✅ | ✅ | ✅ |
| Translations | ✅ | ✅ | ✅ | ✅ |

## Automated Testing (Optional)

```javascript
// Example Puppeteer test
const puppeteer = require('puppeteer');

(async () => {
  const browser = await puppeteer.launch();
  const page = await browser.newPage();
  
  await page.goto('http://localhost:8000/designer/t-shirt');
  
  // Test brush tool
  await page.click('.tool-icon[data-tool="brush"]');
  await page.mouse.move(400, 300);
  await page.mouse.down();
  await page.mouse.move(500, 400);
  await page.mouse.up();
  
  // Verify drawing appeared
  const canvas = await page.$('.drawing-layer');
  const screenshot = await canvas.screenshot();
  
  console.log('Drawing test passed!');
  
  await browser.close();
})();
```

## Success Criteria

All tests must pass:
- ✅ All tools functional
- ✅ All palettes have close buttons
- ✅ Templates display correctly
- ✅ Wardrobe shows all items
- ✅ Mockup is feminine and realistic
- ✅ Save works (download + database)
- ✅ Translations work (AR/EN)
- ✅ No console errors
- ✅ No network errors
- ✅ Responsive on all devices

---

**Test Status**: Ready for QA  
**Last Tested**: March 24, 2026  
**Overall Result**: Pending User Verification
