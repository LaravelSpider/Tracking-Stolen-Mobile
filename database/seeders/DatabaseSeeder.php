<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\StolenDevice;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run()
    {
        // Create admin user
        User::create([
            'name' => 'مدير النظام',
            'email' => 'admin@example.com',
            'password' => Hash::make('password'),
            'role' => 'admin',
        ]);

        // Create security agency user
        User::create([
            'name' => 'الجهة الأمنية',
            'email' => 'security@example.com',
            'password' => Hash::make('password'),
            'role' => 'security_agency',
            'organization' => 'إدارة الشرطة',
        ]);

        // Create regular user
        $user = User::create([
            'name' => 'أحمد محمد',
            'email' => 'user@example.com',
            'password' => Hash::make('password'),
            'role' => 'user',
            'phone' => '+966501234567',
        ]);

        // Create sample stolen devices
        StolenDevice::create([
            'imei' => '123456789012345',
            'brand' => 'Samsung',
            'model' => 'Galaxy S21',
            'color' => 'أسود',
            'status' => 'missing',
            'lost_date' => now()->subDays(5),
            'description' => 'فُقد في المول التجاري',
            'loss_location' => 'مول الرياض، وسط المدينة',
            'latitude' => 24.7136,
            'longitude' => 46.6753,
            'reported_by' => $user->id,
        ]);

        StolenDevice::create([
            'imei' => '987654321098765',
            'brand' => 'iPhone',
            'model' => '13 Pro',
            'color' => 'أزرق',
            'status' => 'investigating',
            'lost_date' => now()->subDays(10),
            'description' => 'سُرق من السيارة',
            'loss_location' => 'موقف المستشفى',
            'latitude' => 24.7136,
            'longitude' => 46.6753,
            'reported_by' => $user->id,
        ]);

        StolenDevice::create([
            'imei' => '456789123456789',
            'brand' => 'Huawei',
            'model' => 'P40 Pro',
            'color' => 'ذهبي',
            'status' => 'found',
            'lost_date' => now()->subDays(15),
            'found_at' => now()->subDays(2),
            'description' => 'فُقد في الحديقة',
            'loss_location' => 'حديقة الملك فهد',
            'latitude' => 24.7136,
            'longitude' => 46.6753,
            'reported_by' => $user->id,
        ]);
    }
}
