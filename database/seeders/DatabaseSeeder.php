<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * تشغيل جميع الـ Seeders الأساسية للمشروع.
     */
    public function run(): void
    {
        $this->call([
            RolesAndPermissionsSeeder::class, // أدوار وصلاحيات: Admin / Seller / User
            DemoUsersSeeder::class,           // مستخدمون تجريبيون وربط الأدوار
            // CategorySeeder::class,
            // PublisherSeeder::class,
            // AuthorSeeder::class,
            // BookSeeder::class,
            // ... أضف Seeders إضافية هنا لاحقاً عند إنشائها
        ]);
    }
}
