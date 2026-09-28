<?php

namespace Database\Factories;

use App\Models\Booking;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<Booking> */
class BookingFactory extends Factory
{
    protected $model = Booking::class;

    public function definition(): array
    {
        return [
            'name' => fake()->name(),
            'phone' => '1868'.fake()->numerify('#######'),
            'email' => fake()->optional()->safeEmail(),
            'service' => fake()->randomElement(Booking::SERVICES),
            'preferred_date' => fake()->optional()->dateTimeBetween('now', '+3 weeks'),
            'preferred_time' => fake()->randomElement(Booking::TIMES),
            'notes' => fake()->optional()->sentence(),
            'status' => 'new',
            'source' => 'website',
        ];
    }
}
