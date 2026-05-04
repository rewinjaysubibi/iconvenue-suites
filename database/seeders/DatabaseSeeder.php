<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\User;
use App\Models\ContactSetting;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Create roles
        $adminRole = Role::create([
            'name' => 'admin',
            'description' => 'Administrator with full system access'
        ]);

        $staffRole = Role::create([
            'name' => 'staff',
            'description' => 'Staff member who manages bookings'
        ]);

        // Create admin user
        User::create([
            'name' => 'Admin User',
            'email' => 'admin@iconvenue.com',
            'password' => Hash::make('admin123'),
            'role_id' => $adminRole->id,
            'is_active' => true
        ]);

        // Create staff user
        User::create([
            'name' => 'Staff User',
            'email' => 'staff@iconvenue.com',
            'password' => Hash::make('staff123'),
            'role_id' => $staffRole->id,
            'is_active' => true
        ]);

        // Create contact settings
        ContactSetting::create([
            'phone' => '0933 866 7716',
            'email' => 'iconvenueandsuites@gmail.com',
            'facebook' => 'https://facebook.com/iconvenue',
            'messenger' => null, // Messenger field kept for backward compatibility but not used
            'whatsapp' => '+1234567890',
            'address' => '123 Venue Street, City, Country',
            'business_hours' => 'Monday - Sunday: 9:00 AM - 6:00 PM'
        ]);
    }
}
