<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Silber\Bouncer\BouncerFacade as Bouncer;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Création du super administrateur de la colonie (lui n'a pas besoin de changer son mdp au 1er boot)
        $admin = User::firstOrCreate(
            ['email' => 'commandant@mars.colony'],
            [
                'name' => 'Commandant de Base',
                'password' => Hash::make('AdminMars2026!'),
                'needs_password_change' => false,
            ]
        );

        // Configuration des rôles et des permissions avec Bouncer
        Bouncer::allow('admin')->to('manage-users');
        Bouncer::assign('admin')->to($admin);

        // Si vous voulez des utilisateurs de test (optionnel)
        $colon = User::firstOrCreate(
            ['email' => 'colon1@mars.colony'],
            [
                'name' => 'John Doe',
                'password' => Hash::make('temp1234'),
                'needs_password_change' => true,
            ]
        );
        Bouncer::assign('colonist')->to($colon);
    }
}
