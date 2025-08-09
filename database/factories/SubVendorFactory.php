<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\SubVendors>
 */
class SubVendorFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            "vendor_id" => 4,
            "name" => $this->faker->name(),
            "email" => $this->faker->email(),
            "password" => bcrypt(12345678),
            "phone" => $this->faker->phoneNumber(),
        ];
    }
}
