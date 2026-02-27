<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Tenant\Project;
use App\Models\User;
use App\Enums\Tenant\UserRole;

use Illuminate\Support\Carbon;
class ProjectSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Buscamos al gerente que creamos antes
        $manager = User::where('role', UserRole::MANAGER)->first();

        if (!$manager) return;

        $projects = [
            [
                'title' => 'Smart Grid Expansion v2',
                'issuing_entity' => 'Dept. of Infrastructure',
                'valuation' => 4200000.00,
                'deadline' => Carbon::now()->addDays(12),
                'status' => 'analyzed',
                'ai_score' => 85,
                'ai_verdict' => 'High Viability',
            ],
            [
                'title' => 'Neural Hub Licensing',
                'issuing_entity' => 'National Tech Registry',
                'valuation' => 12500000.00,
                'deadline' => Carbon::now()->addDays(2),
                'status' => 'processing',
                'ai_score' => 60,
                'ai_verdict' => 'In Analysis',
            ],
            [
                'title' => 'Quantum Link Infrastructure',
                'issuing_entity' => 'Telecom Defense Agency',
                'valuation' => 32100000.00,
                'deadline' => Carbon::now()->addDays(24),
                'status' => 'analyzed',
                'ai_score' => 15,
                'ai_verdict' => 'Low Viability',
            ]
        ];

        foreach ($projects as $data) {
            Project::create(array_merge($data, ['user_id' => $manager->id]));
        }
    }
}
