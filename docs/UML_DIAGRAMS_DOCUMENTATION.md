# Ahlam’s Girls    Laravel Project - Professional UML Diagrams Documentation

## Project Overview
This document contains comprehensive UML diagrams for the Ahlam’s Girls    Laravel e-commerce platform. The system provides custom t-shirt design and ordering functionality with user management, design tools, and order processing.

## Generated Diagrams

### 1. Entity Relationship Diagram (ERD)
**File:** `erd_diagram_simple.svg` | `erd_diagram_simple.puml`

**Key Components:**
- **Core Entities:**
  - `users` - User authentication and profiles (admin, staff, customer roles)
  - `customers` - Customer information and order history
  - `orders` - Order management with status tracking
  - `order_items` - Individual items within orders
  - `products` - Product catalog with template-based design
  - `product_types` - Product categorization (Ahlam's Girls Products, hoodies, etc.)
  - `saved_designs` - User-generated custom designs
  - `design_templates` - Pre-made design templates
  - `roles` - User role management
  - `permissions` - Granular permission system

**Relationships:**
- One-to-many relationships between users, customers, orders, and order items
- Product hierarchies with product types and variants
- Design system with saved designs and templates
- Role-based access control with permissions

### 2. Class Diagram
**File:** `class_diagram.svg` | `class_diagram.puml`

**Structure:**
- **Model Classes:**
  - User - Authentication and role management
  - Customer - Customer-specific functionality
  - Order - Order processing and management
  - OrderItem - Order item details
  - Product - Product management
  - ProductType - Product categorization
  - SavedDesign - Design storage and management
  - DesignTemplate - Template management
  - Role & Permission - RBAC system

- **Controller Classes:**
  - DesignController - Design management APIs
  - OrderController - Order processing APIs
  - AssetController - File upload and management

- **Service Classes:**
  - ExportService - Design export functionality
  - ImageService - Image processing
  - FileService - File management
  - TemplateService - Template operations

### 3. Activity Diagrams
**Files:** `activity_order_process.puml` | `activity_design_process.puml`

**Process Flows:**
- **Order Process Flow:**
  - Customer browsing and product selection
  - User authentication (guest or registered)
  - Cart management
  - Checkout and payment processing
  - Order creation and confirmation
  - Automated print file generation

- **Design Process Flow:**
  - Designer access and product selection
  - Design creation and modification
  - Design saving options (user account vs. session)
  - Export functionality (multiple formats)
  - Integration with ordering system

### 4. Sequence Diagrams
**Files:** `sequence_order_creation.puml` | `sequence_design_process.puml`

**Key Interactions:**
- **Order Creation Sequence:**
  - Customer submits order
  - System validates data and inventory
  - Payment processing
  - Database transaction handling
  - Email notifications
  - Print file generation queue

- **Design Process Sequence:**
  - User creates/modify design
  - Design saving to database
  - Preview generation queuing
  - Export file generation
  - File storage and URL generation

## Key Features of the System

### Business Logic
1. **User Management System**
   - Role-based access control (Admin, Staff, Customer)
   - Session management for guest users
   - Comprehensive permission system

2. **Order Management**
   - Complete order lifecycle tracking
   - Real-time inventory management
   - Payment integration (Stripe/PayPal)
   - Automated print file generation
   - Order status updates

3. **Design System**
   - Real-time design customization
   - Template-based design creation
   - Multi-format export options
   - Guest session handling
   - Print specification compliance

4. **Product Management**
   - Template-based product system
   - Product variants support
   - Category management
   - Inventory tracking

### Technical Architecture
- **Database:** MySQL/MariaDB with comprehensive relationships
- **Framework:** Laravel 11.x with modern PHP features
- **Authentication:** Sanctum for API tokens
- **Frontend:** Inertia.js with Vue.js
- **Storage:** Multi-disk filesystem support
- **Payment:** Stripe integration
- **Background Processing:** Job queue system

### Scalability Features
- Decoupled design with service pattern
- RESTful API architecture
- Async processing for image operations
- Comprehensive database indexing
- File optimization pipeline

## Architecture Pattern
The system follows a **Layered Architecture** with:
- **Presentation Layer:** Inertia.js/Vue frontend
- **Application Layer:** Laravel controllers and services
- **Business Logic Layer:** Models and domain services
- **Data Access Layer:** Eloquent ORM
- **Infrastructure Layer:** Database and external services

## Security Considerations
- Role-based access control
- Input validation and sanitization
- Secure file upload handling
- Payment security compliance
- Session management
- API token authentication

## Performance Optimizations
- Database query optimization
- Caching strategies
- Background job processing
- Image optimization
- Lazy loading patterns
- Efficient relationship loading

---
*Generated on: March 10, 2026*
*Project: Ahlam’s Girls    Laravel E-commerce Platform*
*Diagram Format: SVG (Scalable Vector Graphics)*