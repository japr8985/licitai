<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Tenant;

class TenantSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Creamos el Tenant "Global Corp"
        $tenant1 = Tenant::create([
            'id' => 'globalcorp', // Esto definirá el nombre de la DB: tenantglobalcorp
            'company_name' => 'Global Corporation S.A.',
            'plan' => 'enterprise',
            'industry' => 'IT'
        ]);

        // Le asignamos su dominio (ej: globalcorp.localhost o globalcorp.tuapp.com)
        $tenant1->domains()->create(['domain' => 'globalcorp.localhost']);

        // Si quieres crear otro tenant de prueba rápidamente:
        $tenant2 = Tenant::create([
            'id' => 'techsolutions',
            'company_name' => 'Tech Solutions LLC.',
            'plan' => 'pro',
            'industry' => 'IT'
        ]);
        $tenant2->domains()->create(['domain' => 'techsolutions.localhost']);
    }
}
