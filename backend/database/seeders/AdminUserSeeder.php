<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Créer l'utilisateur administrateur principal
        User::create([
            'first_name' => 'Admin',
            'last_name' => 'CSS',
            'email' => 'admin@css.tn',
            'phone' => '+21612345678',
            'password' => Hash::make('password'), // Changer en production!
            'role' => 'admin',
            'user_type' => 'socios',
            'email_verified_at' => now(),
            'phone_verified_at' => now(),
            'socios_verified' => true,
            'socios_number' => 'ADMIN-001',
            'socios_membership_date' => now(),
            'loyalty_level' => 'platinum',
            'referral_code' => 'ADMIN2024',
        ]);

        // Créer un deuxième admin pour les tests
        User::create([
            'first_name' => 'Super',
            'last_name' => 'Admin',
            'email' => 'superadmin@css.tn',
            'phone' => '+21698765432',
            'password' => Hash::make('password'), // Changer en production!
            'role' => 'admin',
            'user_type' => 'socios',
            'email_verified_at' => now(),
            'phone_verified_at' => now(),
            'socios_verified' => true,
            'socios_number' => 'ADMIN-002',
            'socios_membership_date' => now(),
            'loyalty_level' => 'platinum',
            'referral_code' => 'SUPERADMIN2024',
        ]);

        $this->command->info('✅ Utilisateurs administrateurs créés avec succès!');
        $this->command->info('📧 Email: admin@css.tn | Password: password');
        $this->command->info('📧 Email: superadmin@css.tn | Password: password');
        $this->command->warn('⚠️  IMPORTANT: Changez ces mots de passe en production!');
    }
}
