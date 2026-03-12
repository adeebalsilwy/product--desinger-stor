# Project Organization Guide

## 📁 Quick Navigation

### Core Directories
- **`app/`** - Application business logic (Controllers, Models, Services)
- **`config/`** - Configuration files
- **`database/`** - Migrations, seeders, factories
- **`public/`** - Public entry point and assets
- **`resources/`** - Source views, CSS, JS
- **`routes/`** - Route definitions
- **`storage/`** - Logs, cache, uploads

### Documentation & Resources
- **`docs/`** - 📚 All project documentation
  - Brand guides
  - Technical architecture
  - Implementation guides
  - Fix reports
  - API documentation
  
### Tools & Utilities
- **`scripts/`** - 🔧 Utility PHP scripts
  - Settings checkers
  - Database tools
  - Analysis utilities
  
- **`tools/`** - 🛠️ Development tools
  - PlantUML for diagrams
  - Other utilities

### Assets & Media
- **`assets/`** - 🎨 Media demonstrations
  - GIF screenshots
  - Marketing materials
  
- **`diagrams/`** - 📐 UML and architecture diagrams

### Backups
- **`backups/`** - 💾 Project backups
- **`config/backup/`** - Configuration backups
- **`database/backups/`** - Database backups

---

## 🚀 Quick Start

### 1. View Documentation
```bash
cd docs/
cat QUICK_START.md
```

### 2. Run Utility Scripts
```bash
php scripts/check-settings.php
php scripts/diagnose-settings.php
```

### 3. Access Brand Assets
```bash
# Brand images location
storage/app/public/brands/
```

---

## 📊 File Categories

### Documentation (in `docs/`)
- Brand guidelines
- Technical specs
- Setup guides
- Troubleshooting
- API docs
- Reports

### Code (in `app/`, `config/`, etc.)
- Controllers
- Models
- Services
- Configurations
- Routes

### Utilities (in `scripts/`)
- Maintenance scripts
- Diagnostic tools
- Update utilities

### Media (in `assets/`)
- Demonstrations
- Screenshots
- Visual assets

---

## 🎯 For New Team Members

Start here:
1. Read `README.md` in root
2. Check `docs/QUICK_START.md`
3. Review `docs/PROJECT_STRUCTURE.md`
4. Explore `docs/TECHNICAL_ARCHITECTURE.md`

---

## 📝 Contributing

When adding new files:
- **Documentation** → `docs/`
- **Scripts** → `scripts/`
- **Media** → `assets/`
- **Tools** → `tools/`
- **Code** → appropriate Laravel directory

---

## 🔍 Finding Files

Need documentation? → Check `docs/`
Need to run a utility? → Check `scripts/`
Need media assets? → Check `assets/`
Need development tools? → Check `tools/`

---

## 📞 Need Help?

Refer to:
- `REORGANIZATION_SUMMARY.md` - What was reorganized
- `docs/PROJECT_STRUCTURE.md` - Detailed structure
- `docs/QUICK_START.md` - Getting started guide
