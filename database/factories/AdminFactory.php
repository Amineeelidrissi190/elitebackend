<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\admin>
 */
class AdminFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'nom_admin' => $this->faker->lastName,
            'prenom_admin' => $this->faker->firstName,
            'phone_admin' => $this->faker->phoneNumber,
            'image_admin' => 'path/to/image.jpg', // Vous pouvez utiliser une image par défaut ou une générée aléatoirement
            'id_users' => User::factory(), // Crée un utilisateur et utilise son id
        ];
    }
}
