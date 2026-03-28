# Professional Image Editor - Quick Reference Guide

## 🎯 Quick Access

### **Opening the Editor**
- Click the **purple button** with sliders icon (top right of viewer)
- Or call `viewerRef.openImageEditorModal()` programmatically

### **Closing the Editor**
- Click **×** in top right corner
- Click **"تم" (Done)** button at bottom
- Call `viewerRef.closeImageEditorModal()` programmatically

---

## 🛠️ Tool Categories

### 1️⃣ **Image Selection** (Blue Section)
- Grid of available design images
- Click to select and apply
- Gold border = currently selected

### 2️⃣ **Quick Actions** (Green Section)
| Button | Icon | Function |
|--------|------|----------|
| Remove BG | 🗑️ Eraser | Toggle background removal |
| Rotate | ⏯️ Play/Spinner | Start/stop auto rotation |
| Reset Design | ↩️ Undo | Reset all transformations |
| Camera | 📷 Camera | Reset camera view |

### 3️⃣ **Position Controls** (Purple Section)
| Button | Direction |
|--------|-----------|
| ↑ | Move Up (+Y) |
| ↓ | Move Down (-Y) |
| ← | Move Left (-X) |
| → | Move Right (+X) |

### 4️⃣ **Scale & Rotation** (Orange Section)
| Button | Action |
|--------|--------|
| ➕ | Scale Up (increase size) |
| ➖ | Scale Down (decrease size) |
| ↺ | Rotate Left (counter-clockwise) |
| ↻ | Rotate Right (clockwise) |

### 5️⃣ **Fine Tuning** (Indigo Section)

#### **Rotation Sliders:**
- **Rotate Z**: Full 360° rotation (-180° to 180°)
- **Rotate X**: Tilt forward/backward (-45° to 45°)

#### **Shape Controls:**
- **Curve**: Adjust curvature (0% to 25%)
- **Opacity**: Transparency level (20% to 100%)

#### **Scale Controls:**
- **Scale X**: Width adjustment (0.2x to 2.0x)
- **Scale Y**: Height adjustment (0.2x to 2.0x)

#### **Axis Position:**
- **Axis X**: Horizontal position (-1.0 to 1.0)
- **Axis Y**: Vertical position (-0.5 to 1.5)
- **Axis Z**: Depth position (-0.2 to 1.0)

---

## 🎨 Properties Overlay

Toggle with **cog icon** button (top right):

Shows real-time metrics:
```
🔵 Position: X: 0.00 | Y: 0.42 | Z: 0.18
🟢 Scale: X: 0.82 | Y: 1.02
🟣 Rotation: X: 4° | Y: 0° | Z: 0°
🟠 Curve: 10.0%
🩷 Opacity: 100%
🔷 Quality: High Quality (if BG removed)
```

---

## ⚡ Keyboard Shortcuts (Future Enhancement)

| Key | Action |
|-----|--------|
| `E` | Open editor |
| `Esc` | Close editor |
| `R` | Reset design |
| `Space` | Toggle rotation |

---

## 💡 Pro Tips

1. **Start with Quick Actions**: Use preset buttons for common adjustments
2. **Fine-tune with Sliders**: Get pixel-perfect positioning
3. **Check Properties Panel**: Monitor exact values
4. **Use Background Removal**: For transparent designs
5. **Reset Often**: If lost, reset and start fresh
6. **Combine Movements**: Use multiple adjustments together
7. **Test on Different Models**: Designs look different on various body types

---

## 🌐 Translation Keys

### Common Actions:
| English | Arabic |
|---------|--------|
| Image Tools | أدوات الصورة |
| Properties | الخصائص |
| Remove Background | إزالة الخلفية |
| Reset Design | إعادة ضبط التصميم |
| Done | تم |
| Position | الموضع |
| Scale | الحجم |
| Rotation | الدوران |
| Opacity | الشفافية |
| Curve | الانحناء |

---

## 🔧 Component API

### Methods:
```javascript
// Open/close modal
openImageEditorModal()
closeImageEditorModal()

// Toggle properties
togglePropertyOverlays()

// Control rotation
setAutoRotate(enabled)
toggleAutoRotate()

// Load image with BG removal
loadProductTextureWithBackgroundRemoval(url, reset)

// Transform design
rotateDesignLeft()
rotateDesignRight()
scaleDesignUp()
scaleDesignDown()
moveDesignUp()
moveDesignDown()
moveDesignLeft()
moveDesignRight()
resetDesignTransform()
resetView()
```

### Properties:
```javascript
showImageEditorModal // Modal visibility
showPropertyOverlays // Properties panel visibility
isBackgroundRemovalActive // BG removal status
projection // { x, y, z, scaleX, scaleY, rotateX, rotateY, rotateZ, curve, opacity }
```

---

## 📱 Responsive Breakpoints

| Screen Size | Layout |
|-------------|--------|
| Desktop (>1024px) | 6-col images, 4-col actions |
| Tablet (768-1024px) | 4-col images, 2-col actions |
| Mobile (<768px) | 4-col images, 2-col actions, stacked sliders |

---

## 🎯 Common Workflows

### **Workflow 1: Quick Setup**
1. Select image from grid
2. Click "Remove Background"
3. Adjust position with arrow buttons
4. Click "Done"

### **Workflow 2: Precision Placement**
1. Open properties overlay
2. Use fine-tuning sliders
3. Monitor exact values
4. Test on different models
5. Save when satisfied

### **Workflow 3: Reset & Restart**
1. Click "Reset Design"
2. Select new image
3. Auto-fits to default position
4. Make minor adjustments
5. Done

---

## ✅ Best Practices

1. ✅ Always preview on multiple models
2. ✅ Use background removal for PNG designs
3. ✅ Keep opacity above 80% for visibility
4. ✅ Test rotation within ±45° range
5. ✅ Maintain aspect ratio when scaling
6. ✅ Check properties panel for precision
7. ✅ Reset if design looks distorted

---

## 🆘 Troubleshooting

| Issue | Solution |
|-------|----------|
| Design not visible | Increase opacity, check if hidden |
| Wrong positioning | Use reset, then adjust gradually |
| Background shows | Enable background removal |
| Too small/large | Use scale buttons, then fine-tune |
| Rotation off | Reset rotation, adjust Z-axis first |

---

## 📞 Support

For issues or questions about the image editor:
1. Check this reference guide first
2. Review full implementation documentation
3. Test with sample designs
4. Consult development team if needed

---

**Last Updated:** 2026-03-25  
**Version:** 1.0  
**Status:** ✅ Production Ready
