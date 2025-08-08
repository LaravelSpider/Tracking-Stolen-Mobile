#!/bin/bash

echo "🔧 Fixing NPM dependency conflicts..."

# Clear npm cache
echo "🧹 Clearing NPM cache..."
npm cache clean --force

# Remove node_modules and package-lock.json
echo "🗑️ Removing existing node_modules and lock files..."
rm -rf node_modules
rm -f package-lock.json
rm -f yarn.lock

# Create correct package.json
echo "📝 Creating corrected package.json..."
cat > package.json << 'EOF'
{
  "name": "stolen-phone-tracker",
  "version": "2.0.0",
  "description": "Professional web application for tracking stolen phones",
  "private": true,
  "type": "module",
  "scripts": {
    "dev": "vite",
    "build": "vite build",
    "preview": "vite preview"
  },
  "dependencies": {
    "@headlessui/vue": "^1.7.22",
    "@heroicons/vue": "^2.1.5",
    "@tanstack/vue-query": "^5.59.16",
    "@vueuse/core": "^11.1.0",
    "axios": "^1.7.7",
    "chart.js": "^4.4.6",
    "pinia": "^2.2.4",
    "vue": "^3.5.12",
    "vue-chartjs": "^5.3.1",
    "vue-i18n": "^10.0.4",
    "vue-router": "^4.4.5",
    "vue-toastification": "^2.0.0-rc.5"
  },
  "devDependencies": {
    "@vitejs/plugin-vue": "^5.1.4",
    "autoprefixer": "^10.4.20",
    "laravel-vite-plugin": "^1.0.5",
    "postcss": "^8.4.47",
    "tailwindcss": "^3.4.14",
    "@tailwindcss/forms": "^0.5.9",
    "typescript": "^5.6.3",
    "vite": "^5.4.8",
    "vue-tsc": "^2.1.6"
  }
}
EOF

# Install with legacy peer deps to avoid conflicts
echo "📦 Installing dependencies with legacy peer deps..."
npm install --legacy-peer-deps

# Verify installation
if [ $? -eq 0 ]; then
    echo "✅ NPM installation successful!"
    echo "📋 Installed packages:"
    npm list --depth=0
else
    echo "❌ NPM installation failed. Trying alternative method..."
    
    # Alternative: Install with force
    echo "🔄 Trying with --force flag..."
    npm install --force
    
    if [ $? -eq 0 ]; then
        echo "✅ NPM installation successful with --force!"
    else
        echo "❌ NPM installation still failing. Manual intervention required."
        echo "💡 Try running: npm install --legacy-peer-deps"
    fi
fi

echo ""
echo "🚀 Next steps:"
echo "   1. Run: npm run dev"
echo "   2. In another terminal: php artisan serve"
echo ""
