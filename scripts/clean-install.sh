#!/bin/bash

echo "🧹 Cleaning and fixing NPM dependencies for Vue.js application..."

# Remove all React/Next.js related files and dependencies
echo "🗑️ Removing React/Next.js dependencies..."

# Clear everything
rm -rf node_modules
rm -f package-lock.json
rm -f yarn.lock
npm cache clean --force

# Create clean package.json for Vue.js only
echo "📝 Creating clean Vue.js package.json..."
cat > package.json << 'EOF'
{
  "name": "stolen-phone-tracker",
  "version": "2.0.0",
  "description": "Professional web application for tracking stolen phones with Laravel and Vue.js",
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
    "vue-toastification": "^2.0.0-rc.5",
    "date-fns": "^4.1.0",
    "clsx": "^2.1.1",
    "tailwind-merge": "^2.5.5"
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

# Install dependencies
echo "📦 Installing Vue.js dependencies..."
npm install

# Check if installation was successful
if [ $? -eq 0 ]; then
    echo "✅ NPM installation successful!"
    echo ""
    echo "📋 Installed packages:"
    npm list --depth=0
    echo ""
    echo "🚀 Ready to start development:"
    echo "   npm run dev"
else
    echo "❌ Installation failed. Trying with legacy peer deps..."
    npm install --legacy-peer-deps
    
    if [ $? -eq 0 ]; then
        echo "✅ Installation successful with legacy peer deps!"
    else
        echo "❌ Installation still failing. Please check manually."
    fi
fi
