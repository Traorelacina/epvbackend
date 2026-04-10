<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // Super Administrateur
        User::firstOrCreate(
            ['email' => 'superadmin@epvmarel.ci'],
            [
                'name'      => 'Super Administrateur',
                'password'  => Hash::make('EPVMarel@2025!'),
                'role'      => 'super_admin',
                'is_active' => true,
            ]
        );

        // Administrateur
        User::firstOrCreate(
            ['email' => 'admin@epvmarel.ci'],
            [
                'name'      => 'Administrateur MAREL',
                'password'  => Hash::make('Admin@2025!'),
                'role'      => 'admin',
                'is_active' => true,
            ]
        );

        // Secrétariat
        User::firstOrCreate(
            ['email' => 'secretariat@epvmarel.ci'],
            [
                'name'      => 'Secrétariat EPV MAREL',
                'password'  => Hash::make('Secret@2025!'),
                'role'      => 'secretaire',
                'is_active' => true,
            ]
        );

        $this->command->info('✅ Utilisateurs créés avec succès.');
        $this->command->warn('⚠️  Pensez à changer les mots de passe en production !');
    }
}