<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class VenueAddonSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $addons = [
            // Catering
            [
                'name' => 'Basic Catering Package',
                'description' => 'Includes appetizers, main course, and dessert for up to 50 people',
                'price' => 15000.00,
                'category' => 'catering',
                'sort_order' => 1
            ],
            [
                'name' => 'Premium Catering Package',
                'description' => 'Gourmet menu with premium ingredients for up to 50 people',
                'price' => 25000.00,
                'category' => 'catering',
                'sort_order' => 2
            ],
            [
                'name' => 'Additional Guest Meal',
                'description' => 'Extra meal per person (above 50 guests)',
                'price' => 350.00,
                'category' => 'catering',
                'sort_order' => 3
            ],
            
            // Decoration
            [
                'name' => 'Basic Decoration Package',
                'description' => 'Simple balloon arrangements and table centerpieces',
                'price' => 3000.00,
                'category' => 'decoration',
                'sort_order' => 4
            ],
            [
                'name' => 'Premium Decoration Package',
                'description' => 'Elegant floral arrangements, lighting, and themed decorations',
                'price' => 8000.00,
                'category' => 'decoration',
                'sort_order' => 5
            ],
            [
                'name' => 'Photo Booth Setup',
                'description' => 'Professional photo booth with props and backdrop',
                'price' => 5000.00,
                'category' => 'decoration',
                'sort_order' => 6
            ],
            
            // Equipment
            [
                'name' => 'Sound System Upgrade',
                'description' => 'Professional sound system with microphones',
                'price' => 2500.00,
                'category' => 'equipment',
                'sort_order' => 7
            ],
            [
                'name' => 'Projector & Screen',
                'description' => 'HD projector with large screen for presentations',
                'price' => 3000.00,
                'category' => 'equipment',
                'sort_order' => 8
            ],
            [
                'name' => 'Additional Tables & Chairs',
                'description' => 'Extra seating arrangement (10 chairs + 2 tables)',
                'price' => 1500.00,
                'category' => 'equipment',
                'sort_order' => 9
            ],
            
            // Services
            [
                'name' => 'Event Coordinator',
                'description' => 'Professional event coordinator for the entire event',
                'price' => 4000.00,
                'category' => 'service',
                'sort_order' => 10
            ],
            [
                'name' => 'Photography Service',
                'description' => 'Professional photographer for 4 hours',
                'price' => 8000.00,
                'category' => 'service',
                'sort_order' => 11
            ],
            [
                'name' => 'Cleanup Service',
                'description' => 'Post-event cleanup and venue restoration',
                'price' => 2000.00,
                'category' => 'service',
                'sort_order' => 12
            ]
        ];

        foreach ($addons as $addon) {
            \App\Models\VenueAddon::create($addon);
        }
    }
}
