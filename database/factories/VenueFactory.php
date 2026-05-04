<?php

namespace Database\Factories;

use App\Models\Venue;
use Illuminate\Database\Eloquent\Factories\Factory;

class VenueFactory extends Factory
{
    protected $model = Venue::class;

    public function definition(): array
    {
        return [
            'name' => $this->faker->company . ' ' . $this->faker->randomElement(['Hall', 'Room', 'Suite', 'Venue']),
            'type' => $this->faker->randomElement(['venue', 'suite']),
            'description' => $this->faker->paragraph(3),
            'capacity' => $this->faker->numberBetween(10, 500),
            'price_per_day' => $this->faker->numberBetween(1000, 10000),
            'price_morning' => $this->faker->numberBetween(500, 3000),
            'price_afternoon' => $this->faker->numberBetween(500, 3000),
            'price_evening' => $this->faker->numberBetween(500, 4000),
            'amenities' => $this->faker->randomElements([
                'WiFi', 'Air Conditioning', 'Parking', 'Sound System', 
                'Projector', 'Catering', 'Security', 'Restrooms'
            ], $this->faker->numberBetween(2, 6)),
            'images' => [],
            'is_active' => $this->faker->boolean(80),
        ];
    }

    public function venue(): static
    {
        return $this->state(fn (array $attributes) => [
            'type' => 'venue',
        ]);
    }

    public function suite(): static
    {
        return $this->state(fn (array $attributes) => [
            'type' => 'suite',
        ]);
    }

    public function active(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_active' => true,
        ]);
    }

    public function inactive(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_active' => false,
        ]);
    }
}