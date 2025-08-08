#!/bin/bash

echo "🚀 Complete Setup for Stolen Phone Tracker Application..."

# Check if composer is installed
if ! command -v composer &> /dev/null; then
    echo "❌ Composer is not installed. Please install Composer first."
    echo "Visit: https://getcomposer.org/download/"
    exit 1
fi

# Check if Node.js is installed
if ! command -v node &> /dev/null; then
    echo "❌ Node.js is not installed. Please install Node.js first."
    echo "Visit: https://nodejs.org/"
    exit 1
fi

# Create project directory
echo "📁 Creating project directory..."
mkdir -p stolen-phone-tracker
cd stolen-phone-tracker

# Initialize composer.json if it doesn't exist
if [ ! -f "composer.json" ]; then
    echo "📦 Creating composer.json..."
    cat > composer.json << 'EOF'
{
    "name": "laravel/stolen-phone-tracker",
    "type": "project",
    "description": "Professional web application for tracking stolen phones",
    "keywords": ["laravel", "vue", "stolen-phone", "tracker"],
    "license": "MIT",
    "require": {
        "php": "^8.1",
        "barryvdh/laravel-dompdf": "^2.0",
        "guzzlehttp/guzzle": "^7.2",
        "laravel/framework": "^10.10",
        "laravel/sanctum": "^3.2",
        "laravel/tinker": "^2.8"
    },
    "require-dev": {
        "fakerphp/faker": "^1.9.1",
        "laravel/pint": "^1.0",
        "mockery/mockery": "^1.4.4",
        "nunomaduro/collision": "^7.0",
        "phpunit/phpunit": "^10.1",
        "spatie/laravel-ignition": "^2.0"
    },
    "autoload": {
        "psr-4": {
            "App\\": "app/",
            "Database\\Factories\\": "database/factories/",
            "Database\\Seeders\\": "database/seeders/"
        }
    },
    "scripts": {
        "post-autoload-dump": [
            "Illuminate\\Foundation\\ComposerScripts::postAutoloadDump",
            "@php artisan package:discover --ansi"
        ],
        "post-create-project-cmd": [
            "@php artisan key:generate --ansi"
        ]
    },
    "config": {
        "optimize-autoloader": true,
        "preferred-install": "dist",
        "sort-packages": true
    },
    "minimum-stability": "stable",
    "prefer-stable": true
}
EOF
fi

# Install PHP dependencies
echo "📦 Installing PHP dependencies..."
composer install

# Create package.json if it doesn't exist
if [ ! -f "package.json" ]; then
    echo "📦 Creating package.json..."
    cat > package.json << 'EOF'
{
  "name": "stolen-phone-tracker",
  "version": "1.0.0",
  "private": true,
  "scripts": {
    "dev": "vite",
    "build": "vite build"
  },
  "dependencies": {
    "vue": "^3.3.0",
    "vue-router": "^4.2.0",
    "pinia": "^2.1.0",
    "vue-i18n": "^9.2.0",
    "axios": "^1.4.0"
  },
  "devDependencies": {
    "@vitejs/plugin-vue": "^4.2.0",
    "tailwindcss": "^3.3.0",
    "@tailwindcss/forms": "^0.5.0",
    "autoprefixer": "^10.4.0",
    "postcss": "^8.4.0",
    "vite": "^4.3.0",
    "laravel-vite-plugin": "^0.7.0"
  }
}
EOF
fi

# Install Node.js dependencies
echo "📦 Installing Node.js dependencies..."
npm install

# Setup environment file
echo "⚙️ Setting up environment..."
if [ ! -f ".env" ]; then
    cp .env.example .env 2>/dev/null || echo "APP_NAME=\"Stolen Phone Tracker\"
APP_ENV=local
APP_KEY=
APP_DEBUG=true
APP_URL=http://localhost:8000

DB_CONNECTION=sqlite
DB_DATABASE=database/database.sqlite

SANCTUM_STATEFUL_DOMAINS=localhost,localhost:3000,127.0.0.1,127.0.0.1:8000,::1" > .env
fi

# Generate application key
echo "🔑 Generating application key..."
php artisan key:generate

# Create database directory and file
echo "🗄️ Setting up database..."
mkdir -p database
touch database/database.sqlite

# Run migrations
echo "🔄 Running migrations..."
php artisan migrate --force

# Seed database
echo "🌱 Seeding database..."
php artisan db:seed --force

# Publish Sanctum files
echo "🔐 Publishing Sanctum files..."
php artisan vendor:publish --provider="Laravel\Sanctum\SanctumServiceProvider" --force

echo ""
echo "✅ Setup complete!"
echo ""
echo "🚀 To start the application:"
echo "   1. Run: php artisan serve"
echo "   2. In another terminal, run: npm run dev"
echo "   3. Open: http://localhost:8000"
echo ""
echo "👤 Default login credentials:"
echo "   Admin: admin@example.com / password"
echo "   Security: security@example.com / password"
echo "   User: user@example.com / password"
echo ""
