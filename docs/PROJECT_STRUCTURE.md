# 📁 Project Structure Documentation

## Overview
This document describes the organized directory structure of the D-Shirts Laravel project.

## Directory Structure

### 📂 Root Level (Core Project Files)
Essential Laravel project files that remain in the root:
- `composer.json` - PHP dependency management
- `package.json` - Node.js dependency management
- `artisan` - Laravel CLI tool
- `vite.config.js` - Vite build configuration
- `tailwind.config.js` - Tailwind CSS configuration
- `phpunit.xml` - PHPUnit testing configuration
- `.env.example` - Environment variables template
- `.gitignore` - Git ignore rules
- `.editorconfig` - Editor configuration

### 📂 app/
Application core code:
- `Controllers/` - HTTP request handlers
- `Models/` - Database models
- `Services/` - Business logic services
- `Jobs/` - Queue jobs
- `Mail/` - Email classes
- `Enums/` - PHP enums
- `Helpers/` - Helper functions
- `Providers/` - Service providers

### 📂 bootstrap/
Laravel bootstrap files for application initialization

### 📂 config/
Application configuration files:
- `app.php` - Application config
- `database.php` - Database config
- `mail.php` - Mail configuration
- `services.php` - Third-party services
- `backup/` - Configuration backups

### 📂 database/
Database migrations, seeders, and factories:
- `migrations/` - Database migrations
- `seeders/` - Database seeders
- `factories/` - Model factories
- `backups/` - Database backups

### 📂 diagrams/
UML and system architecture diagrams:
- Class diagrams
- Sequence diagrams
- ERD diagrams
- Activity diagrams

### 📂 docs/ (NEW)
**All project documentation files:**
- Brand guides and specifications
- Implementation guides
- Technical architecture documents
- Fix reports and summaries
- Quick start guides
- API documentation
- Settings documentation
- Permission reports

### 📂 public/
Publicly accessible files:
- `index.php` - Application entry point
- Frontend assets (after build)

### 📂 resources/
Source files for compilation:
- `views/` - Blade templates
- `css/` - Source CSS files
- `js/` - Source JavaScript files

### 📂 routes/
Application route definitions:
- `web.php` - Web routes
- `api.php` - API routes
- `auth.php` - Authentication routes
- `console.php` - Console routes

### 📂 scripts/ (NEW)
**Utility and maintenance PHP scripts:**
- Settings checkers and diagnosticians
- Brand update scripts
- Permission analysis tools
- Database restoration scripts
- Testing utilities

### 📂 storage/
Application storage:
- `framework/` - Laravel framework cache
- `logs/` - Application logs
- `app/public/brands/` - Brand assets
- `app/public/temp/` - Temporary files

### 📂 tests/
Test suites:
- `Feature/` - Feature tests
- `Unit/` - Unit tests
- `Pest.php` - Pest test configuration

### 📂 assets/ (NEW)
**Media and demonstration files:**
- GIF demonstrations
- Screenshots
- Marketing assets

### 📂 tools/ (NEW)
**Development and utility tools:**
- PlantUML jars for diagram generation
- Other development utilities

### 📂 backups/ (NEW)
**Project backups:**
- Configuration backups
- Database backups
- Important file backups

## Key Organization Principles

1. **Documentation Centralization**: All `.md` documentation files are now in `docs/`
2. **Scripts Separation**: Utility scripts moved to `scripts/`
3. **Asset Management**: Media files stored in `assets/`
4. **Tool Isolation**: Development tools in `tools/`
5. **Backup Safety**: Backups stored in dedicated `backups/` folder
6. **Core Preservation**: Essential Laravel files remain in root

## Quick Navigation

- **Getting Started**: See `/docs/QUICK_START.md`
- **Technical Details**: See `/docs/TECHNICAL_ARCHITECTURE.md`
- **Brand Guide**: See `/docs/AHLAMS_BRAND_GUIDE.md`
- **Implementation**: See `/docs/IMPLEMENTATION_GUIDE.md`
- **Settings**: See `/docs/SETTINGS_FIX_FINAL_SUMMARY.md`

## Maintenance

### Adding New Documentation
Place all new `.md` files in the `docs/` directory with descriptive names.

### Adding New Scripts
Utility scripts go in `scripts/` with clear naming conventions.

### Backup Strategy
Use the `backups/` directory for important project state snapshots.

---

*Last Updated: 2026-03-12*
