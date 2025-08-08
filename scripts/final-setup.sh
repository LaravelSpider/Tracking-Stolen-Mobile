#!/bin/bash

echo "🚀 Final Setup for Stolen Phone Tracker (Vue.js Only)"

# Step 1: Clean installation
echo "🧹 Step 1: Cleaning previous installation..."
rm -rf node_modules
rm -f package-lock.json
rm -f yarn.lock
npm cache clean --force

# Step 2: Install dependencies
echo "📦 Step 2: Installing Vue.js dependencies..."
npm install

if [ $? -ne 0 ]; then
    echo "⚠️ Standard install failed, trying with legacy peer deps..."
    npm install --legacy-peer-deps
fi

# Step 3: Verify installation
echo "✅ Step 3: Verifying installation..."
if [ -d "node_modules" ]; then
    echo "✅ Node modules installed successfully"
    npm list --depth=0
else
    echo "❌ Installation failed"
    exit 1
fi

# Step 4: Build assets
echo "🔨 Step 4: Building assets..."
npm run build

echo ""
echo "🎉 Setup Complete!"
echo ""
echo "🚀 To start development:"
echo "   1. php artisan serve"
echo "   2. npm run dev (in another terminal)"
echo ""
echo "📱 Application stack:"
echo "   ✅ Laravel (Backend API)"
echo "   ✅ Vue.js 3 (Frontend)"
echo "   ✅ Tailwind CSS (Styling)"
echo "   ✅ Pinia (State Management)"
echo "   ✅ Vue Router (Routing)"
echo "   ✅ Vue I18n (Internationalization)"
echo ""
echo "❌ Removed unnecessary packages:"
echo "   ❌ React"
echo "   ❌ Next.js"
echo "   ❌ Geist"
echo "   ❌ All React-related dependencies"
echo ""
