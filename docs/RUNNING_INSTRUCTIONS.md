# Running Instructions for D-Shirts Application

## Prerequisites

Make sure you have the following installed:
- PHP 8.1 or higher
- Node.js and npm
- Composer
- A web server (Apache/Nginx) or use the built-in PHP server

## Installation Steps

1. **Install PHP Dependencies**
   ```bash
   composer install
   ```

2. **Install Node.js Dependencies**
   ```bash
   npm install
   ```

3. **Configure Environment**
   - Copy `.env.example` to `.env`
   - Generate application key:
     ```bash
     php artisan key:generate
     ```

4. **Database Setup**
   ```bash
   php artisan migrate --seed
   ```

## Running the Application

### Development Mode

The application uses two servers:
1. **Laravel Server** - Serves the backend API and pages
2. **Vite Dev Server** - Handles frontend asset compilation and hot reloading

### Option 1: Manual Start (Recommended for Development)

**Step 1:** Start the Laravel server in one terminal:
```bash
php artisan serve --host=127.0.0.1 --port=8000
```

**Step 2:** In another terminal, start the Vite dev server:
```bash
npx vite --host
```

The application will be available at: `http://127.0.0.1:8000`

### Option 2: Using Startup Scripts

**For Windows (PowerShell):**
```bash
.\start-dev-servers.ps1
```

**For Unix/Linux/Mac:**
```bash
chmod +x start-dev-servers.sh
./start-dev-servers.sh
```

## Troubleshooting

### Common Issues and Solutions

1. **Connection Refused Errors**
   - Make sure both servers are running
   - Check that the ports (8000 for Laravel, 5173 for Vite) are not in use
   - Verify the APP_URL in `.env` matches your server address

2. **Vite Assets Not Loading**
   - Ensure the Vite dev server is running
   - Check that the Laravel server can communicate with Vite
   - Look for CORS issues if serving from different ports

3. **Hot Reload Not Working**
   - Make sure the Vite server is running
   - Check that HMR (Hot Module Replacement) is enabled in the Vite config

4. **Performance Issues**
   - Clear the cache: `php artisan cache:clear`
   - Clear compiled views: `php artisan view:clear`
   - Clear configuration: `php artisan config:clear`

### Configuration Notes

- The Laravel server runs on `http://127.0.0.1:8000`
- The Vite dev server typically runs on `http://localhost:5173`
- The Laravel Vite plugin handles the communication between servers
- Hot module replacement (HMR) is enabled for fast development

## Production Build

To build for production:
```bash
npm run build
```

Then serve the application normally with your web server.

## Useful Commands

- `php artisan serve` - Start Laravel development server
- `npx vite` - Start Vite development server
- `npm run build` - Build for production
- `php artisan migrate` - Run database migrations
- `php artisan db:seed` - Seed the database
- `php artisan cache:clear` - Clear application cache

## Notes

- The Vite dev server handles asset compilation and hot reloading
- The Laravel server serves the application and API
- Both servers must be running for full functionality
- The `APP_URL` in `.env` should match your Laravel server address