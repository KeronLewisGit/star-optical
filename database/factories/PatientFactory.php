<?php

namespace Database\Factories;

use App\Models\Patient;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<Patient> */
class PatientFactory extends Factory
{
    protected $model = Patient::class;

    public function definition(): array
    {
        return [
            'first_name' => fake()->firstName(),
            'last_name' => fake()->lastName(),
            'phone' => '1868'.fake()->numerify('#######'),
            'email' => fake()->optional()->safeEmail(),
            'date_of_birth' => fake()->optional()->date('Y-m-d', '-18 years'),
            'gender' => fake()->randomElement(Patient::GENDERS),
            'status' => 'active',
        ];
    }
}
