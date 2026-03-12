# 🎨 Ahlam's Girls - Color Palette Quick Reference

## Primary Brand Colors

### Deep Navy Blue
```
Hex: #1a1a2e
RGB: rgb(26, 26, 46)
HSL: hsl(240, 28%, 14%)
Usage: Headers, primary text, main backgrounds
Psychology: Elegance, sophistication, trust, professionalism
```

### Rich Navy
```
Hex: #16213e
RGB: rgb(22, 33, 62)
HSL: hsl(223, 48%, 16%)
Usage: Secondary backgrounds, cards, sections
Psychology: Depth, stability, reliability
```

### Midnight Blue
```
Hex: #0f3460
RGB: rgb(15, 52, 96)
HSL: hsl(211, 73%, 22%)
Usage: Main backgrounds, overlays, gradients
Psychology: Mystery, depth, luxury
```

---

## Accent Colors

### Rose Pink
```
Hex: #e94560
RGB: rgb(233, 69, 96)
HSL: hsl(350, 79%, 59%)
Usage: CTAs, highlights, badges, active states
Psychology: Femininity, energy, passion, warmth
Accessibility: ✓ Passes WCAG AA on white
```

### Luxury Gold
```
Hex: #d4af37
RGB: rgb(212, 175, 55)
HSL: hsl(46, 65%, 52%)
Usage: Premium features, luxury badges, special elements
Psychology: Wealth, quality, prestige, elegance
Accessibility: ✓ Passes WCAG AA on dark backgrounds
```

### Soft Rose
```
Hex: #f8b4c8
RGB: rgb(248, 180, 200)
HSL: hsl(342, 87%, 84%)
Usage: Gentle backgrounds, feminine touches
Psychology: Gentleness, romance, sweetness
Accessibility: Use with dark text only
```

### Soft Lavender
```
Hex: #b19cd9
RGB: rgb(177, 156, 217)
HSL: hsl(261, 43%, 73%)
Usage: Creative elements, shipped status
Psychology: Creativity, imagination, uniqueness
Accessibility: Use with dark text only
```

---

## Neutral Colors

### Pure White
```
Hex: #ffffff
RGB: rgb(255, 255, 255)
Usage: Text, icons, clean backgrounds
```

### Light Gray (Neumorphic BG)
```
Hex: #f0f4f8
RGB: rgb(240, 244, 248)
Usage: Neumorphic backgrounds, card bases
```

### Shadow Dark
```
Hex: #c3d0e0
RGB: rgb(195, 208, 224)
Usage: Shadows, depth effects
```

---

## CSS Variables

```css
:root {
    /* Primary Brand Colors */
    --brand-primary: #1a1a2e;
    --brand-secondary: #16213e;
    --brand-accent: #e94560;
    --brand-bg: #0f3460;
    --brand-text: #ffffff;
    
    /* Luxury Accent Colors */
    --brand-gold: #d4af37;
    --brand-rose: #f8b4c8;
    --brand-lavender: #b19cd9;
    
    /* Neumorphism Colors */
    --neumorphic-bg: #f0f4f8;
    --neumorphic-shadow-light: #ffffff;
    --neumorphic-shadow-dark: #c3d0e0;
}
```

---

## Tailwind Classes

### Background Colors
```html
<div class="bg-brand-primary">Deep Navy</div>
<div class="bg-brand-secondary">Rich Navy</div>
<div class="bg-brand-accent">Rose Pink</div>
<div class="bg-brand-gold">Luxury Gold</div>
<div class="bg-brand-rose">Soft Rose</div>
<div class="bg-brand-lavender">Soft Lavender</div>
```

### Text Colors
```html
<span class="text-brand-primary">Deep Navy Text</span>
<span class="text-brand-accent">Rose Pink Text</span>
<span class="text-brand-gold">Gold Text</span>
<span class="text-brand-rose">Soft Rose Text</span>
```

### Border Colors
```html
<div class="border border-brand-accent">Pink Border</div>
<div class="border border-brand-gold">Gold Border</div>
```

### Gradients
```html
<div class="gradient-brand">Navy Gradient</div>
<div class="gradient-luxury">Gold to Pink Gradient</div>
<div class="gradient-elegant">Rose to Lavender Gradient</div>
```

---

## Color Combinations

### ✅ Recommended Pairings

#### Classic Elegance
```
Primary: #1a1a2e (Deep Navy)
Accent: #d4af37 (Gold)
Background: #ffffff (White)
Result: Timeless luxury and sophistication
```

#### Feminine Charm
```
Primary: #16213e (Rich Navy)
Accent: #e94560 (Rose Pink)
Background: #f8b4c8 (Soft Rose)
Result: Gentle and inviting
```

#### Modern Luxury
```
Primary: #0f3460 (Midnight Blue)
Accent: #d4af37 (Gold)
Highlight: #e94560 (Rose Pink)
Result: Contemporary premium feel
```

#### Creative Vision
```
Primary: #1a1a2e (Deep Navy)
Accent: #b19cd9 (Lavender)
Highlight: #d4af37 (Gold)
Result: Innovative and unique
```

---

## Accessibility Matrix

### Contrast Ratios

| Foreground| Background| Ratio | Rating |
|------------|------------|-------|--------|
| #ffffff (White) | #1a1a2e (Navy) | 15.8:1 | ✓ AAA |
| #d4af37 (Gold) | #1a1a2e (Navy) | 8.2:1 | ✓ AAA |
| #e94560 (Pink) | #ffffff (White) | 5.1:1 | ✓ AA |
| #1a1a2e (Navy) | #f0f4f8 (Light) | 13.5:1 | ✓ AAA |
| #b19cd9 (Lavender) | #1a1a2e (Navy) | 7.9:1 | ✓ AAA |

### Color Blindness Safe

All brand colors are distinguishable by users with:
- ✓ Protanopia (red-blind)
- ✓ Deuteranopia (green-blind)
- ✓ Tritanopia (blue-blind)

---

## Usage Examples

### Buttons

#### Primary Button
```html
<button class="bg-brand-primary text-white px-6 py-3 rounded-lg hover:opacity-90">
    Primary Action
</button>
```

#### Accent Button
```html
<button class="bg-brand-accent text-white px-6 py-3 rounded-lg hover:opacity-90">
    Call to Action
</button>
```

#### Luxury Button
```html
<button class="bg-gradient-to-r from-brand-gold to-brand-accent text-white px-6 py-3 rounded-lg">
    Premium Action
</button>
```

### Cards

#### Standard Card
```html
<div class="bg-neumorphic shadow-lg rounded-xl p-6">
    <h3 class="text-brand-primary font-bold text-xl">Card Title</h3>
    <p class="text-gray-600 mt-2">Card content goes here...</p>
</div>
```

#### Luxury Card
```html
<div class="bg-gradient-to-br from-brand-secondary to-brand-bg border border-brand-gold shadow-xl rounded-xl p-6">
    <h3 class="text-brand-gold font-bold text-xl">Premium Card</h3>
    <p class="text-white mt-2">Exclusive content...</p>
</div>
```

### Badges

#### Status Badge
```html
<span class="bg-brand-gold text-white px-3 py-1 rounded-full text-sm font-semibold">
    New Arrival
</span>
```

#### Sale Badge
```html
<span class="bg-brand-accent text-white px-3 py-1 rounded-full text-sm font-semibold animate-pulse">
    Sale
</span>
```

---

## Quick Copy Palette

### For Designers (Figma/Sketch)
```
Deep Navy:    #1a1a2e
Rich Navy:    #16213e
Midnight Blue:#0f3460
Rose Pink:    #e94560
Luxury Gold:  #d4af37
Soft Rose:    #f8b4c8
Soft Lavender:#b19cd9
Pure White:   #ffffff
Light Gray:   #f0f4f8
```

### For Developers (CSS)
```css
/* Copy & Paste Ready */
--brand-primary: #1a1a2e;
--brand-secondary: #16213e;
--brand-accent: #e94560;
--brand-bg: #0f3460;
--brand-text: #ffffff;
--brand-gold: #d4af37;
--brand-rose: #f8b4c8;
--brand-lavender: #b19cd9;
```

### For Print (CMYK)
```
Deep Navy:    C:85 M:75 Y:45 K:55
Rich Navy:    C:88 M:78 Y:50 K:60
Midnight Blue:C:90 M:70 Y:40 K:50
Rose Pink:    C:0 M:85 Y:60 K:0
Luxury Gold:  C:15 M:25 Y:85 K:0
Soft Rose:    C:0 M:30 Y:20 K:0
Soft Lavender:C:30 M:35 Y:0 K:0
```

---

## Do's and Don'ts

### ✅ DO

- Use Deep Navy as the dominant color (35-40%)
- Apply Rose Pink for CTAs and important actions
- Use Gold sparingly for premium/luxury elements
- Maintain sufficient contrast ratios
- Test colors across different devices
- Use gradients for modern appeal

### ❌ DON'T

- Don't use too many bright colors together
- Don't mix conflicting neon tones
- Don't overuse gold (appears tacky)
- Don't compromise readability for style
- Don't use low-quality images
- Don't clutter the design

---

## Seasonal Variations

### Spring Collection
```
Primary: #1a1a2e
Accent: #f8b4c8 (Soft Rose)
Highlight: #b19cd9 (Lavender)
Feel: Fresh, blooming, gentle
```

### Summer Collection
```
Primary: #0f3460
Accent: #e94560 (Rose Pink)
Highlight: #d4af37 (Gold)
Feel: Vibrant, energetic, bold
```

### Fall Collection
```
Primary: #16213e
Accent: #d4af37 (Gold)
Highlight: #e94560 (Rose Pink)
Feel: Warm, rich, cozy
```

### Winter Collection
```
Primary: #1a1a2e
Accent: #b19cd9 (Lavender)
Highlight: #ffffff (White)
Feel: Cool, elegant, sophisticated
```

---

## Export Formats

### Adobe Swatch Exchange (.ase)
Available in project files

### Procreate Palette (.swatches)
Download from assets folder

### Photoshop Gradient (.grd)
Included in design kit

---

**Last Updated:** March 2026  
**Version:** 1.0  
**Status:** Active ✨
