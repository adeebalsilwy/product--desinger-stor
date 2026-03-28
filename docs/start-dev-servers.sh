#!/bin/bash
# Development startup script for D-Shirts application

echo "Starting D-Shirts Development Servers..."
echo ""

# Start Laravel server in the background
echo "Starting Laravel server on http://127.0.0.1:8000"
php artisan serve --host=127.0.0.1 --port=8000 > laravel.log 2>&1 &

# Wait a moment for Laravel to start
sleep 3

# Start Vite dev server in the background
echo "Starting Vite dev server on http://127.0.0.1:5173"
npx vite --host > vite.log 2>&1 &

echo ""
echo "Servers started successfully!"
echo "Laravel: http://127.0.0.1:8000"
echo "Vite: http://127.0.0.1:5173 (or local network IP)"
echo ""
echo "Logs are being written to laravel.log and vite.log"
echo ""
echo "Press Ctrl+C to stop both servers"
echo ""

# Keep the script running
wait