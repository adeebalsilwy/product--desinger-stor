# 🚀 Quick Reference Card

## 📁 Where to Find Things

| I Need... | Go To | Example |
|-----------|-------|---------|
| 📚 Documentation | `docs/` | `cd docs/` |
| 🔧 Utility Scripts | `scripts/` | `php scripts/check-settings.php` |
| 🎨 Media Files | `assets/` | `cd assets/` |
| 🛠️ Dev Tools | `tools/` | `java -jar tools/plantuml.jar` |
| 💾 Backups | `backups/` | `cd backups/` |
| 🏷️ Brand Assets | `storage/app/public/brands/` | Upload brand logos here |
| 📐 UML Diagrams | `diagrams/` | `cd diagrams/` |
| ⚙️ Config Backups | `config/backup/` | `cd config/backup/` |
| 🗄️ DB Backups | `database/backups/` | `cd database/backups/` |

---

## 📂 New Directory Structure

```
Project Root/
├── docs/          # All documentation (37 files)
├── scripts/       # Utility scripts (10 files)
├── assets/        # Media files (GIFs, images)
├── tools/         # Development tools (jars)
├── backups/       # Project backups
└── [Laravel core directories]
```

---

## 🔍 Common Commands

### Documentation
```bash
# View all docs
ls docs/

# Read quick start
cat docs/QUICK_START.md

# Check project structure
cat docs/PROJECT_STRUCTURE.md
```

### Scripts
```bash
# Check settings
php scripts/check-settings.php

# Diagnose issues
php scripts/diagnose-settings.php

# Update brand
php scripts/update-brand.php
```

### Navigation
```bash
# Fast navigation
cd docs/           # Documentation
cd scripts/        # Scripts
cd assets/         # Media
cd tools/          # Tools
```

---

## 📋 Key Files in Root

**Keep these in root:**
- ✅ `README.md` - Main documentation
- ✅ `composer.json` - PHP dependencies
- ✅ `package.json` - Node dependencies
- ✅ `artisan` - Laravel CLI
- ✅ `vite.config.js` - Build tool
- ✅ `.env.example` - Environment template

**Moved out of root:**
- ❌ Documentation → `docs/`
- ❌ Scripts → `scripts/`
- ❌ Media → `assets/`
- ❌ Tools → `tools/`

---

## 🎯 For New Team Members

1. **Start Here**: `README.md`
2. **Structure**: `docs/PROJECT_STRUCTURE.md`
3. **Quick Start**: `docs/QUICK_START.md`
4. **Tech Details**: `docs/TECHNICAL_ARCHITECTURE.md`

---

## 📞 Need Help?

### Documentation Issues
→ Check `docs/` folder  
→ Read `PROJECT_STRUCTURE.md`

### Script Problems  
→ Go to `scripts/` folder  
→ Run diagnostic scripts

### General Questions
→ See `ORGANIZATION_GUIDE.md`  
→ Ask team lead

---

## ✅ Best Practices

### DO:
- ✅ Add new docs to `docs/`
- ✅ Put scripts in `scripts/`
- ✅ Store media in `assets/`
- ✅ Keep tools in `tools/`
- ✅ Regular backups

### DON'T:
- ❌ Scatter files in root
- ❌ Mix docs with code
- ❌ Store binaries in source
- ❌ Ignore structure

---

## 📊 Quick Stats

- **Files Organized**: 51
- **New Directories**: 9
- **Root Cleanup**: 73%
- **Time Saved**: Hours of searching!

---

## 🎉 Remember

> **"A place for everything, and everything in its place."**

- Documentation → `docs/`
- Scripts → `scripts/`
- Media → `assets/`
- Tools → `tools/`
- Backups → `backups/`

**Clean structure = Happy developers!** 😊

---

**Quick Access Links:**
- [Full Structure](docs/PROJECT_STRUCTURE.md)
- [Reorganization Summary](REORGANIZATION_SUMMARY.md)
- [Arabic Summary](ARABIC_REORGANIZATION_SUMMARY.md)
- [Before/After Comparison](docs/BEFORE_AFTER_COMPARISON.md)
