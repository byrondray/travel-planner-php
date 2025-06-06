#!/bin/bash

echo "🚀 Starting Travel Planner Application..."

# Function to handle cleanup
cleanup() {
    echo "⚠️ Received shutdown signal. Cleaning up..."
    kill $QUEUE_PID 2>/dev/null
    kill $WEB_PID 2>/dev/null
    exit 0
}

# Trap signals
trap cleanup SIGTERM SIGINT

# Restart any existing queue workers
echo "🔄 Restarting queue workers..."
php artisan queue:restart

# Start queue worker in background
echo "🔧 Starting queue worker..."
php artisan queue:work --verbose --tries=3 --timeout=300 --memory=512 --sleep=3 &
QUEUE_PID=$!
echo "✅ Queue worker started with PID: $QUEUE_PID"

# Give queue worker time to start
sleep 2

# Start web server
echo "🌐 Starting web server on port $PORT..."
php artisan serve --host=0.0.0.0 --port=$PORT &
WEB_PID=$!
echo "✅ Web server started with PID: $WEB_PID"

# Wait for either process to exit
wait 