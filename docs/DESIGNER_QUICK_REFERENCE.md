# Designer Quick Reference Guide

## 🎨 Tool Functions

### Drawing Tools

| Tool | Icon | Function | Palette | Shortcut |
|------|------|----------|---------|----------|
| **Brush** | 🖌️ | Freehand drawing with smooth lines | Color, Size | - |
| **Pen** | ✒️ | Fine detail drawing | Color, Size | - |
| **Eraser** | 🧹 | Erase drawing strokes | Size only | - |

### Element Tools

| Tool | Icon | Function | Layer | Editable |
|------|------|----------|-------|----------|
| **Text** | T | Add text elements | Elements | ✅ Move, Resize, Rotate |
| **Image** | 🖼️ | Upload images | Elements | ✅ Move, Resize, Rotate |
| **Shapes** | ⬜ | Add geometric shapes | Elements | ✅ Move, Resize, Rotate |

### Control Tools

| Tool | Icon | Function | Affects |
|------|------|----------|---------|
| **Color** | 🎨 | Change drawing/element color | Active tool or selected element |
| **Size** | 📏 | Adjust brush/pen/eraser size | Drawing tools |
| **Dress Color** | 👗 | Change garment template color | Template layer |
| **Templates** | 📋 | Select background templates | Template layer |
| **Layers** | 📚 | Manage element stacking order | Elements layer |

## 🖱️ Mouse Operations

### Drawing (Brush/Pen/Eraser)
```
1. Click tool icon
2. Select color (for brush/pen)
3. Select size
4. Click and drag on canvas to draw
5. Release to finish stroke
```

### Moving Elements
```
1. Click element to select
2. Click and drag to move
3. Release to place
```

### Resizing Elements
```
1. Click element to select
2. Drag resize handle (bottom-right corner)
3. Release when desired size reached
```

### Rotating Elements
```
1. Click element to select
2. Drag rotate handle (top of element)
3. Release when desired angle reached
```

## 🎯 Layers System

### Order (Bottom to Top)
```
┌──────────────────────┐
│   Elements Layer     │ ← Text, Images, Shapes (DOM)
├──────────────────────┤
│   Drawing Layer      │ ← Brush, Pen, Eraser (Canvas)
├──────────────────────┤
│   Template Layer     │ ← Garment template (Canvas)
└──────────────────────┘
```

### Layer Independence
- **Template Layer:** Static background, doesn't interfere with drawing
- **Drawing Layer:** Freehand strokes, can be erased without affecting template
- **Elements Layer:** Independent objects that float above drawing

## ⚙️ Canvas Settings

### Stage Dimensions
- **Width:** 600px
- **Height:** 700px
- **Coordinate System:** Scaled to canvas size

### Brush Settings
- **Brush Size Range:** 1-50px
- **Pen Size:** Automatically thinner (brush size - 2)
- **Eraser Size:** 1-50px

### Drawing Configuration
```javascript
lineCap: 'round'        // Smooth line ends
lineJoin: 'round'       // Smooth corners
globalCompositeOperation: 'source-over'  // Normal drawing
```

## 🔄 History System

### Undo/Redo
- **Undo:** Click ↶ button (Ctrl+Z equivalent)
- **Redo:** Click ↷ button (Ctrl+Y equivalent)
- **History Tracking:** Every action is saved
- **Limit:** Unlimited undo/redo steps

### What's Tracked
✅ Element additions/deletions
✅ Element modifications (move, resize, rotate)
✅ Drawing strokes
✅ Color changes
✅ Template changes
❌ Palette openings (not tracked)

## 🎨 Color Management

### Color Selection
1. Click Color tool
2. Choose from palette
3. Applied to:
   - Active drawing tool (brush/pen)
   - Selected element (text/shapes)

### Default Colors
- Black (#000000)
- White (#FFFFFF)
- Red (#FF0000)
- Green (#00FF00)
- Blue (#0000FF)
- Yellow (#FFFF00)
- Cyan (#00FFFF)
- Magenta (#FF00FF)
- Gray (#808080)
- Orange (#FFA500)
- Purple (#800080)
- Pink (#FFC0CB)

## 📐 Element Properties

### Text Elements
```javascript
{
  type: 'text',
  text: 'Your text here',
  x: 330,
  y: 180,
  width: 260,
  height: 120,
  rotation: 0,
  opacity: 1,
  color: '#000000',
  fontSize: 42,
  fontWeight: '700',
  zIndex: 1
}
```

### Shape Elements
```javascript
{
  type: 'rect', // or 'circle' or 'triangle'
  x: 320,
  y: 220,
  width: 180,
  height: 180,
  rotation: 0,
  opacity: 1,
  color: '#FF0000',
  zIndex: 1
}
```

### Image Elements
```javascript
{
  type: 'image',
  src: 'data:image/...',
  x: 300,
  y: 200,
  width: 200,
  height: 200,
  rotation: 0,
  opacity: 1,
  zIndex: 1
}
```

## 🛠️ Toolbar Functions

### Top Toolbar
- **Undo (↶):** Reverse last action
- **Redo (↷):** Revert undo
- **Duplicate (📋):** Copy selected element
- **Delete (🗑️):** Remove selected element
- **Save (💾):** Save design
- **Preview (👗):** Toggle mockup view

### Bottom Toolbar
- **Clear Drawing (🧹):** Erase all drawing strokes
- **Reset (🔄):** Reset entire design
- **Download (⬇️):** Export design

## 🎯 Professional Tips

### Best Practices
1. **Work in Layers:** Use elements layer for main design, drawing layer for details
2. **Start Large:** Begin with big shapes, add details last
3. **Use Undo:** Don't hesitate to undo mistakes
4. **Layer Order:** Arrange elements by z-index for proper stacking
5. **Save Often:** Save your work regularly

### Drawing Techniques
- **Light Strokes:** Start with light colors, build up gradually
- **Smooth Lines:** Use continuous motion for smooth curves
- **Eraser Precision:** Use smaller eraser for detailed work
- **Color Harmony:** Choose complementary colors

### Element Arrangement
- **Center First:** Place main elements first
- **Balance:** Distribute visual weight evenly
- **Spacing:** Leave adequate space between elements
- **Alignment:** Use consistent alignment

## ⚡ Performance Tips

### Optimization
- Limit number of elements (< 50 recommended)
- Use appropriate image sizes (< 500KB per image)
- Clear unused drawing strokes
- Close palettes when not in use

### Browser Compatibility
- Chrome/Edge: Best performance
- Firefox: Good performance
- Safari: Compatible
- Mobile: Desktop recommended for complex designs

## 🆘 Troubleshooting

### Common Issues

**Issue: Drawing not appearing**
- Solution: Check if correct tool selected, verify color isn't transparent

**Issue: Can't select element**
- Solution: Ensure elements layer is active, click directly on element

**Issue: Tools not responding**
- Solution: Refresh page, check browser console for errors

**Issue: Canvas appears blank**
- Solution: Check template color, verify stage dimensions

**Issue: Elements disappear**
- Solution: Check z-index ordering, may be hidden behind other elements

## 📱 Keyboard Shortcuts

*(Note: Currently mouse-only interface, shortcuts planned for future)*

Planned Shortcuts:
- `Ctrl+Z`: Undo
- `Ctrl+Y`: Redo
- `Delete`: Remove selected
- `Ctrl+C`: Duplicate
- `Esc`: Deselect/CLOSE palette

---

**Quick Start:** Select a tool → Choose color/size → Create on canvas → Adjust as needed → Save design

**Pro Tip:** Use layers concept - template for base, drawing for details, elements for decorations!
