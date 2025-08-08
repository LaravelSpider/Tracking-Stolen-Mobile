#!/bin/bash

echo "🚀 Installing Stolen Phone Tracker with Laravel 12+ and latest dependencies..."

# Check PHP version
PHP_VERSION=$(php -r "echo PHP_VERSION;")
echo "📋 PHP Version: $PHP_VERSION"

if ! php -r "exit(version_compare(PHP_VERSION, '8.3.0', '>=') ? 0 : 1);"; then
    echo "❌ PHP 8.3+ is required. Current version: $PHP_VERSION"
    exit 1
fi

# Check if composer is installed
if ! command -v composer &> /dev/null; then
    echo "❌ Composer is not installed. Please install Composer first."
    echo "Visit: https://getcomposer.org/download/"
    exit 1
fi

# Check if Node.js is installed
if ! command -v node &> /dev/null; then
    echo "❌ Node.js is not installed. Please install Node.js 18+ first."
    echo "Visit: https://nodejs.org/"
    exit 1
fi

# Create project directory
PROJECT_NAME="stolen-phone-tracker-v2"
echo "📁 Creating project directory: $PROJECT_NAME"
mkdir -p $PROJECT_NAME
cd $PROJECT_NAME

# Install Laravel 12
echo "📦 Installing Laravel 12..."
composer create-project laravel/laravel . "^12.0"

# Install additional PHP packages
echo "📦 Installing additional PHP packages..."
composer require laravel/sanctum:"^4.0" \
                 barryvdh/laravel-dompdf:"^3.0" \
                 intervention/image:"^3.8" \
                 spatie/laravel-permission:"^6.9" \
                 spatie/laravel-activitylog:"^4.8" \
                 pusher/pusher-php-server:"^7.2"

# Install Node.js dependencies
echo "📦 Installing Node.js dependencies..."
npm install vue@^3.5 \
            @vitejs/plugin-vue@^5.1 \
            vue-router@^4.4 \
            pinia@^2.2 \
            vue-i18n@^10.0 \
            axios@^1.7 \
            @headlessui/vue@^1.7 \
            @heroicons/vue@^2.1 \
            @vueuse/core@^11.1 \
            vue-toastification@^2.0.0-rc.5 \
            @tanstack/vue-query@^5.59 \
            tailwindcss@^3.4 \
            @tailwindcss/forms@^0.5 \
            @tailwindcss/typography@^0.5 \
            typescript@^5.6 \
            chart.js@^4.4 \
            vue-chartjs@^5.3

# Setup environment
echo "⚙️ Setting up environment..."
cp .env.example .env
php artisan key:generate

# Configure database (SQLite for simplicity)
echo "🗄️ Setting up SQLite database..."
touch database/database.sqlite
sed -i 's/DB_CONNECTION=mysql/DB_CONNECTION=sqlite/' .env
sed -i 's|DB_DATABASE=laravel|DB_DATABASE=database/database.sqlite|' .env

# Publish packages
echo "📋 Publishing package configurations..."
php artisan vendor:publish --provider="Laravel\Sanctum\SanctumServiceProvider"
php artisan vendor:publish --provider="Spatie\Permission\PermissionServiceProvider"
php artisan vendor:publish --provider="Spatie\Activitylog\ActivitylogServiceProvider"

# Run migrations
echo "🔄 Running migrations..."
php artisan migrate

# Create storage link
echo "🔗 Creating storage link..."
php artisan storage:link

# Set permissions
echo "🔐 Setting permissions..."
chmod -R 775 storage bootstrap/cache
chown -R www-data:www-data storage bootstrap/cache 2>/dev/null || true

echo ""
echo "✅ Installation complete!"
echo ""
echo "🚀 To start the application:"
echo "   1. Run: php artisan serve"
echo "   2. In another terminal, run: npm run dev"
echo "   3. Open: http://localhost:8000"
echo ""
echo "📝 Next steps:"
echo "   1. Copy the provided code files to their respective locations"
echo "   2. Run: php artisan migrate:fresh --seed"
echo "   3. Configure your .env file as needed"
echo ""
echo "🔧 Laravel Version: $(php artisan --version)"
echo "📦 Node Version: $(node --version)"
echo "📦 NPM Version: $(npm --version)"
