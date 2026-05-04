<?php

namespace Database\Factories;

use App\Models\Booking;
use App\Models\Venue;
use Illuminate\Database\Eloquent\Factories\Factory;

class BookingFactory extends Factory
{
    protected $model = Booking::class;

    public function definition(): array
    {
        $startDate = $this->faker->dateTimeBetween('now', '+3 months');
        $endDate = (clone $startDate)->modify('+1 day');

        return [
            'venue_id' => Venue::factory(),
            'client_name' => $this->faker->name,
            'client_email' => $this->faker->safeEmail,
            'client_phone' => $this->faker->phoneNumber,
            'event_type' => $this->faker->randomElement(['Wedding', 'Birthday', 'Corporate', 'Conference', 'Party']),
            'start_date' => $startDate,
            'end_date' => $endDate,
            'start_time' => $this->faker->randomElement(['morning', 'afternoon', 'evening']),
            'guest_count' => $this->faker->numberBetween(10, 200),
            'total_amount' => $this->faker->numberBetween(5000, 50000),
            'status' => $this->faker->randomElement(['pending', 'confirmed', 'cancelled']),
            'payment_status' => $this->faker->randomElement(['pending', 'partial', 'paid']),
            'notes' => $this->faker->optional()->paragraph,
        ];
    }

    public function pending(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'pending',
            'payment_status' => 'pending',
        ]);
    }

    public function confirmed(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'confirmed',
        ]);
    }

    public function paid(): static
    {
        return $this->state(fn (array $attributes) => [
            'payment_status' => 'paid',
        ]);
    }
}