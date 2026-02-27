<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use App\Enums\Tenant\UserRole;

class TenantDatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::create([
            'name' => 'Admin Gerencial',
            'email' => 'gerente@globalcorp.com',
            'password' => Hash::make('password'),
            'role' => UserRole::MANAGER,
        ]);

        // 2. PROJECT MANAGER
        User::create([
            'name' => 'Project Manager Alpha',
            'email' => 'pm@globalcorp.com',
            'password' => Hash::make('password'),
            'role' => UserRole::PROJECT_MANAGER,
        ]);

        // 3. DEVELOPER
        User::create([
            'name' => 'Dev Senior',
            'email' => 'dev@globalcorp.com',
            'password' => Hash::make('password'),
            'role' => UserRole::DEVELOPER,
        ]);
    }
}
