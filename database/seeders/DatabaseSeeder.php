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

        // Create Details for Admin
        \App\Models\UserDetail::firstOrCreate(
            ['user_id' => $admin->id],
            ['biome_id' => 1, 'dome' => 'A', 'blood_group' => 'O+', 'clearance_level' => 5, 'logs' => 'Commandant en poste.']
        );

        // Details for Colon 1 (Dome A)
        \App\Models\UserDetail::firstOrCreate(
            ['user_id' => $colon->id],
            ['biome_id' => 1, 'dome' => 'A', 'blood_group' => 'A+', 'clearance_level' => 2, 'logs' => 'Assigné à la maintenance.']
        );

        // Create Colon 2
        $colon2 = User::firstOrCreate(
            ['email' => 'colon2@mars.colony'],
            [
                'name' => 'Jane Smith',
                'password' => Hash::make('temp1234'),
                'needs_password_change' => true,
            ]
        );
        Bouncer::assign('colonist')->to($colon2);

        // Details for Colon 2 (Dome B)
        \App\Models\UserDetail::firstOrCreate(
            ['user_id' => $colon2->id],
            ['biome_id' => 2, 'dome' => 'B', 'blood_group' => 'B-', 'clearance_level' => 2, 'logs' => 'Assignée à l\'agriculture.']
        );

        // Create some test messages
        if (\App\Models\Message::count() === 0) {
            \App\Models\Message::create([
                'sender_id' => $colon->id,
                'target_dome' => 'B',
                'content' => "Ici le Dôme A. Comment se passe la récolte d'algues de votre côté ?",
                'metadata' => ['priority' => 'normal', 'system_time' => now()->toIso8601String()]
            ]);

            \App\Models\Message::create([
                'sender_id' => $colon2->id,
                'target_dome' => 'A',
                'content' => "Dôme B au rapport. Récolte optimale. Avez-vous reçu les pièces de rechange ?",
                'metadata' => ['priority' => 'normal', 'system_time' => now()->addMinutes(5)->toIso8601String()]
            ]);

            \App\Models\Message::create([
                'sender_id' => $admin->id,
                'target_dome' => 'B',
                'content' => "Attention Dôme B, tempête solaire détectée. Protégez les serres d'ici 2 heures.",
                'metadata' => ['priority' => 'high', 'system_time' => now()->addMinutes(10)->toIso8601String()]
            ]);
        }
    }
}
