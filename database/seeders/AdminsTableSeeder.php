<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\admin;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class AdminsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $user = User::create([
            'email' => 'admin@gmail.com',
            'password' => Hash::make('password'), // Mot de passe sécurisé
            'role' => 'admin',
        ]);

        // Créer un administrateur lié à cet utilisateur
        admin::create([
            'nom_admin' => 'NomAdmin',
            'prenom_admin' => 'PrenomAdmin',
            'phone_admin' => '12341234',
            'image_admin' => 'path/to/image.jpg', // Chemin de l'image
            'id_users' => $user->id,
        ]);
 
        //
    }
}
