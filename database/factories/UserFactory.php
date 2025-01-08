<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\User>
 */
class UserFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        return [
            'uuid'=>'162aaf48-5c27-4e9c-8568-dcac484e4dd9',
            'name' =>'khulud',
            'email' => 'khulud123@gmail.com',
            'phone'=>'0993311407',
            'email_verified_at' => now(),
            'password' =>bcrypt('kh123@'), // password
            'remember_token' => Str::random(10),
            'role'=>'admin',
        ];
    }

    /**
     * Indicate that the model's email address should be unverified.
     *
     * @return static
     */
    public function unverified()
    {
        return $this->state(fn (array $attributes) => [
            'email_verified_at' => null,
        ]);
    }
}
