<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class VendorFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'name' => $this->faker->name(),
            'email' => 'vendor@vendor.com',
            'phone' => "5344704757",
            'another_phone' => $this->faker->phoneNumber(),
            'description' => $this->faker->text(),
            'commercial_registration_number' => $this->faker->phoneNumber(),
            'image_commercial' => $this->faker->imageUrl,
            'image_iban' => $this->faker->imageUrl,
            'image_mark' => $this->faker->imageUrl,
            'image_tax' => $this->faker->imageUrl,
            'expire_date_commercial_registration' => $this->faker->dateTime(),
            'banned' => 0,
            'password' => bcrypt(12345678),
        ];
    }

    /**
     * Indicate that the model's email address should be unverified.
     *
     * @return \Illuminate\Database\Eloquent\Factories\Factory
     */
    public function unverified()
    {
        return $this->state(function (array $attributes) {
            return [
                'email_verified_at' => null,
            ];
        });
    }
}
