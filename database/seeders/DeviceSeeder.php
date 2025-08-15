<?php

namespace Database\Seeders;

use App\Models\StolenDevice;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DeviceSeeder extends Seeder
{
    public function run(): void
    {
        if (! class_exists(StolenDevice::class)) {
            $this->command?->warn('StolenDevice model not found. Skipping DeviceSeeder.');
            return;
        }

        DB::transaction(function () {
            $Device = StolenDevice::class;

            // Resolve some users by email (adjust to your actual seeding emails)
            $reporter = \App\Models\User::where('email', 'user@example.com')->first();
            $security = \App\Models\User::where('email', 'security@example.com')->first();

            // Seed devices (idempotent by imei)
            $rows = [
                [
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
                    'reported_by' => $reporter->id,
                    'assigned_to' => $security->id,
                ],
                [
                    'imei' => '987654321098765',
                    'brand' => 'Apple',
                    'model' => 'iPhone 13 Pro',
                    'color' => 'أزرق',
                    'status' => 'investigating',
                    'lost_date' => now()->subDays(10),
                    'description' => 'سُرق من السيارة أثناء التوقف',
                    'loss_location' => 'موقف المستشفى',
                    'latitude' => 24.7136,
                    'longitude' => 46.6753,
                    'reported_by' => $reporter->id,
                    'assigned_to' => $security->id,
                ],
                [
                    'imei' => '356938035643809',
                    'brand' => 'Xiaomi',
                    'model' => 'Redmi Note 11',
                    'color' => 'أبيض',
                    'status' => 'recovered',
                    'lost_date' => now()->subDays(20),
                    'description' => 'تم العثور عليه بعد بلاغ الجيران',
                    'loss_location' => 'حي النرجس',
                    'latitude' => 24.7850,
                    'longitude' => 46.6900,
                    'reported_by' => $reporter->id,
                    'assigned_to' => $security->id,
                ],
                [
                    'imei' => '353918054127365',
                    'brand' => 'Huawei',
                    'model' => 'P40',
                    'color' => 'فضي',
                    'status' => 'missing',
                    'lost_date' => now()->subDays(2),
                    'description' => 'فُقد أثناء المواصلات العامة',
                    'loss_location' => 'محطة الباص الرئيسية',
                    'latitude' => 19.6175,
                    'longitude' => 37.2164,
                    'reported_by' => $reporter->id,
                    'assigned_to' => $security->id,
                ],
            ];

            foreach ($rows as $row) {
                $Device::query()->updateOrCreate(
                    ['imei' => $row['imei']],
                    $row
                );
            }
        });
    }
}
