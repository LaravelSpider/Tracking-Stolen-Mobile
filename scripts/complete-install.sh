#!/bin/bash

echo "🚀 Complete Installation Script for Stolen Phone Tracker"

# Check system requirements
echo "📋 Checking system requirements..."

# Check PHP version
if ! command -v php &> /dev/null; then
    echo "❌ PHP is not installed"
    exit 1
fi

PHP_VERSION=$(php -r "echo PHP_VERSION;")
echo "✅ PHP Version: $PHP_VERSION"

# Check Composer
if ! command -v composer &> /dev/null; then
    echo "❌ Composer is not installed"
    exit 1
fi
echo "✅ Composer is installed"

# Check Node.js
if ! command -v node &> /dev/null; then
    echo "❌ Node.js is not installed"
    exit 1
fi

NODE_VERSION=$(node --version)
echo "✅ Node.js Version: $NODE_VERSION"

# Create project directory
PROJECT_DIR="stolen-phone-tracker"
echo "📁 Creating project directory: $PROJECT_DIR"

if [ -d "$PROJECT_DIR" ]; then
    echo "⚠️ Directory already exists. Removing..."
    rm -rf "$PROJECT_DIR"
fi

mkdir "$PROJECT_DIR"
cd "$PROJECT_DIR"

# Initialize Laravel project
echo "📦 Creating Laravel project..."
composer create-project laravel/laravel . --prefer-dist

# Install PHP dependencies
echo "📦 Installing PHP dependencies..."
composer require laravel/sanctum barryvdh/laravel-dompdf intervention/image

# Create package.json with correct dependencies
echo "📝 Creating package.json..."
cat > package.json << 'EOF'
{
  "name": "stolen-phone-tracker",
  "version": "2.0.0",
  "private": true,
  "type": "module",
  "scripts": {
    "dev": "vite",
    "build": "vite build"
  },
  "dependencies": {
    "@headlessui/vue": "^1.7.22",
    "@heroicons/vue": "^2.1.5",
    "@vueuse/core": "^11.1.0",
    "axios": "^1.7.7",
    "pinia": "^2.2.4",
    "vue": "^3.5.12",
    "vue-i18n": "^10.0.4",
    "vue-router": "^4.4.5"
  },
  "devDependencies": {
    "@vitejs/plugin-vue": "^5.1.4",
    "autoprefixer": "^10.4.20",
    "laravel-vite-plugin": "^1.0.5",
    "postcss": "^8.4.47",
    "tailwindcss": "^3.4.14",
    "@tailwindcss/forms": "^0.5.9",
    "vite": "^5.4.8"
  }
}
EOF

# Install Node.js dependencies
echo "📦 Installing Node.js dependencies..."
npm install --legacy-peer-deps

# Setup environment
echo "⚙️ Setting up environment..."
cp .env.example .env
php artisan key:generate

# Setup SQLite database
echo "🗄️ Setting up database..."
touch database/database.sqlite

# Update .env for SQLite
sed -i.bak 's/DB_CONNECTION=mysql/DB_CONNECTION=sqlite/' .env
sed -i.bak 's/DB_HOST=127.0.0.1/#DB_HOST=127.0.0.1/' .env
sed -i.bak 's/DB_PORT=3306/#DB_PORT=3306/' .env
sed -i.bak 's/DB_DATABASE=laravel/DB_DATABASE=database\/database.sqlite/' .env
sed -i.bak 's/DB_USERNAME=root/#DB_USERNAME=root/' .env
sed -i.bak 's/DB_PASSWORD=/#DB_PASSWORD=/' .env

# Publish Sanctum
echo "🔐 Publishing Sanctum..."
php artisan vendor:publish --provider="Laravel\Sanctum\SanctumServiceProvider"

# Run migrations
echo "🔄 Running migrations..."
php artisan migrate

# Create storage link
echo "🔗 Creating storage link..."
php artisan storage:link

echo ""
echo "✅ Basic Laravel setup complete!"
echo ""
echo "📝 Next steps:"
echo "   1. Copy the provided code files to their respective locations"
echo "   2. Run: php artisan migrate:fresh --seed"
echo "   3. Start development servers:"
echo "      - php artisan serve"
echo "      - npm run dev (in another terminal)"
echo ""
echo "🌐 The application will be available at: http://localhost:8000"
echo ""
