#!/bin/bash

echo "🚀 Setting up Stolen Phone Tracker Application..."

# Create Laravel project
echo "📦 Creating Laravel project..."
composer create-project laravel/laravel stolen-phone-tracker
cd stolen-phone-tracker

# Install PHP dependencies
echo "📦 Installing PHP dependencies..."
composer require laravel/sanctum barryvdh/laravel-dompdf

# Install Node.js dependencies
echo "📦 Installing Node.js dependencies..."
npm install vue@next @vitejs/plugin-vue vue-router@4 pinia vue-i18n@9 axios tailwindcss @tailwindcss/forms

# Setup environment
echo "⚙️ Setting up environment..."
cp .env.example .env
php artisan key:generate

# Setup database
echo "🗄️ Setting up database..."
touch database/database.sqlite
sed -i 's/DB_CONNECTION=mysql/DB_CONNECTION=sqlite/' .env
sed -i 's/DB_DATABASE=laravel/DB_DATABASE=\/absolute\/path\/to\/database.sqlite/' .env

# Run migrations
echo "🔄 Running migrations..."
php artisan migrate

# Publish Sanctum
echo "🔐 Setting up Sanctum..."
php artisan vendor:publish --provider="Laravel\Sanctum\SanctumServiceProvider"

echo "✅ Setup complete! Run 'php artisan serve' and 'npm run dev' to start the application."
