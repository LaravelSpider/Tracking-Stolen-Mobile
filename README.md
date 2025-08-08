# Stolen Phone Tracker - نظام تتبع الهواتف المسروقة

A professional web application built with Laravel and Vue.js for tracking stolen phones and devices.

## Features

- 🔐 Secure authentication system with role-based access
- 📊 Dynamic dashboard with statistics
- 🔍 IMEI/Serial number search functionality
- 📱 Device reporting system with location tracking
- 🔔 Notification system for device matches
- 📄 PDF report generation
- 🌍 Multi-language support (Arabic RTL & English)
- 📱 Responsive design with Tailwind CSS

## Tech Stack

- **Backend**: Laravel 11+ with Sanctum
- **Frontend**: Vue 3 + Composition API
- **Database**: SQLite/MySQL/PostgreSQL
- **Styling**: Tailwind CSS
- **State Management**: Pinia
- **Internationalization**: Vue I18n

## Installation

1. Clone the repository:
\`\`\`bash
git clone <repository-url>
cd stolen-phone-tracker
\`\`\`

2. Install dependencies:
\`\`\`bash
composer install
npm install
\`\`\`

3. Setup environment:
\`\`\`bash
cp .env.example .env
php artisan key:generate
\`\`\`

4. Setup database:
\`\`\`bash
touch database/database.sqlite
php artisan migrate --seed
\`\`\`

5. Start the application:
\`\`\`bash
php artisan serve
npm run dev
\`\`\`

## Default Login Credentials

- **Admin**: admin@example.com / password
- **Security Agency**: security@example.com / password  
- **User**: user@example.com / password

## License

This project is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).
